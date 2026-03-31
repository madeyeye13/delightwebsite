<?php

namespace App\Livewire\Frontend;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CartSync extends Component
{
    /**
     * On mount: push the persisted cart (user or session) into the Alpine store.
     */
    public function mount(): void
    {
        $cart = $this->resolveCart(create: false);

        if ($cart) {
            $items = $this->buildAlpineItems($cart);
            $this->dispatch('cart:initialized', items: $items);
        }
    }

    /**
     * Persist a newly added item. Dispatched by Alpine store's addItem().
     * Payload keys must match the Alpine cart item shape.
     */
    #[On('cart:add')]
    public function addItem(int $productId, ?int $variantId = null, int $quantity = 1, ?int $customPrice = null): void
    {
        $product = Product::with(['category', 'sellingMethod', 'media'])->find($productId);
        if (! $product) {
            return;
        }

        $cart = $this->resolveCart(create: true);

        // Gift cards always create a separate line — each line represents one unique code
        if (! $product->is_gift_card) {
            $existing = $cart->items()
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $quantity);
                $this->dispatch('cart:synced', items: $this->buildAlpineItems($cart->fresh()->load('items.product.media', 'items.product.sellingMethod', 'items.product.category.parent', 'items.variant.media')));

                return;
            }
        }

        $cart->items()->create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $product->is_gift_card ? 1 : max(1, $quantity),
            'custom_price' => $product->is_gift_card && $customPrice ? $customPrice : null,
        ]);

        $this->dispatch('cart:synced', items: $this->buildAlpineItems($cart->fresh()->load('items.product.media', 'items.product.sellingMethod', 'items.product.category.parent', 'items.variant.media')));
    }

    /**
     * Update quantity. Dispatched by Alpine increaseQty / decreaseQty.
     */
    #[On('cart:update-qty')]
    public function updateQty(int $productId, ?int $variantId = null, int $quantity = 1): void
    {
        $cart = $this->resolveCart(create: false);
        if (! $cart) {
            return;
        }

        $item = $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($item) {
            if ($quantity <= 0) {
                $item->delete();
            } else {
                $item->update(['quantity' => $quantity]);
            }
        }
    }

    /**
     * Update the denomination of a gift card line.
     * Dispatched by Alpine updateGiftCardPrice().
     */
    #[On('cart:update-gift-card-price')]
    public function updateGiftCardPrice(int $cartLineId, int $price): void
    {
        $cart = $this->resolveCart(create: false);
        if (! $cart) {
            return;
        }

        $item = $cart->items()->with('product')->where('id', $cartLineId)->first();
        if ($item && $item->product?->is_gift_card) {
            $item->update(['custom_price' => max(1, $price)]);
            $this->dispatch('cart:synced', items: $this->buildAlpineItems($cart->fresh()->load('items.product.media', 'items.product.sellingMethod', 'items.product.category.parent', 'items.variant.media')));
        }
    }

    /**
     * Remove a specific cart line by ID (safe for gift cards with multiple same-product lines).
     * Dispatched by Alpine removeItemByLine().
     */
    #[On('cart:remove-line')]
    public function removeLine(int $cartLineId): void
    {
        $cart = $this->resolveCart(create: false);
        if (! $cart) {
            return;
        }

        $cart->items()->where('id', $cartLineId)->delete();
        $this->dispatch('cart:synced', items: $this->buildAlpineItems($cart->fresh()->load('items.product.media', 'items.product.sellingMethod', 'items.product.category.parent', 'items.variant.media')));
    }

    /**
     * Remove an item. Dispatched by Alpine removeItem().
     */
    #[On('cart:remove')]
    public function removeItem(int $productId, ?int $variantId = null): void
    {
        $cart = $this->resolveCart(create: false);
        if (! $cart) {
            return;
        }

        $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->delete();
    }

    /**
     * Merge guest session cart into user cart on login.
     * Call this from a post-login hook or middleware.
     */
    public static function mergeSessionCartIntoUser(int $userId): void
    {
        $sessionId = Session::getId();
        $guestCart = Cart::forSession($sessionId)->with('items')->first();
        if (! $guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = Cart::firstOrCreate(
            ['user_id' => $userId, 'session_id' => null],
            ['user_id' => $userId]
        );

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'custom_price' => $item->custom_price,
                ]);
            }
        }

        $guestCart->delete();
    }

    public function render(): View
    {
        return view('livewire.frontend.cart-sync');
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function resolveCart(bool $create): ?Cart
    {
        if (Auth::check()) {
            if ($create) {
                return Cart::firstOrCreate(
                    ['user_id' => Auth::id()],
                    ['user_id' => Auth::id()]
                );
            }

            return Cart::forUser(Auth::id())->first();
        }

        $sessionId = Session::getId();

        if ($create) {
            return Cart::firstOrCreate(
                ['session_id' => $sessionId, 'user_id' => null],
                ['session_id' => $sessionId]
            );
        }

        return Cart::forSession($sessionId)->first();
    }

    /**
     * Build the Alpine item array from a Cart's eager-loaded items.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildAlpineItems(Cart $cart): array
    {
        return $cart->items
            ->load('product.media', 'product.sellingMethod', 'product.category.parent', 'variant.media', 'product.addOns.media', 'product.addOns.sellingMethod', 'product.addOns.category')
            ->map(function (CartItem $item): array {
                $product = $item->product;
                if (! $product) {
                    return [];
                }

                $variant = $item->variant;
                $unitLabel = $product->unit_label ?: ucfirst(str_replace('per-', '', $product->sellingMethod?->config_type ?? 'piece'));

                // Category / subcategory: if the product's category has a parent,
                // the parent is the "category" and the child is the "subcategory".
                $category = $product->category;
                $categoryName = '';
                $subcategoryName = '';
                if ($category) {
                    if ($category->parent) {
                        $categoryName = $category->parent->name;
                        $subcategoryName = $category->name;
                    } else {
                        $categoryName = $category->name;
                    }
                }

                // Image: prefer variant image when variant is selected
                $image = $product->thumb_image_url ?? '';
                if ($variant) {
                    $variantMedia = $variant->getFirstMedia('variant_main');
                    if ($variantMedia) {
                        $image = $variantMedia->hasGeneratedConversion('thumb')
                            ? $variantMedia->getUrl('thumb')
                            : $variantMedia->getUrl();
                    }
                }

                // Stock: variant stock if selected, otherwise product effective stock
                $stockQuantity = $variant ? $variant->stock : $product->effective_stock;

                $unitPrice = $product->final_price + ($variant->price_adjustment ?? 0);

                // For gift cards with custom amount, use stored custom_price
                if ($product->is_gift_card && $item->custom_price) {
                    $unitPrice = $item->custom_price;
                }

                return [
                    'cart_line_id' => $item->id,
                    'product_id' => $product->id,
                    'slug' => $product->slug,
                    'name' => $product->name,
                    'category' => $categoryName,
                    'subcategory' => $subcategoryName,
                    'image' => $image,
                    'selling_method' => str_replace('_', '-', $product->sellingMethod?->config_type ?? 'per-piece'),
                    'unit_label' => $unitLabel,
                    'length_unit' => $product->length_unit,
                    'units_per_order' => $product->units_per_order,
                    'min_quantity' => $product->min_quantity,
                    'quantity_step' => $product->quantity_step,
                    'stock_quantity' => $stockQuantity,
                    'loom_size' => $product->loom_size,
                    'quantity' => $item->quantity,
                    'selected_variant' => $variant ? [
                        'id' => $variant->id,
                        'color' => $variant->name,
                        'hex' => $variant->hex ?? '#cccccc',
                    ] : null,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $item->quantity,
                    'is_gift_card' => (bool) $product->is_gift_card,
                    'allow_custom_amount' => (bool) $product->allow_custom_amount,
                    'custom_price' => $item->custom_price,
                    'suggested_add_ons' => $product->show_add_ons_in_cart
                        ? $product->addOns->map(function (Product $addOn): array {
                            $addOnUnitLabel = $addOn->unit_label ?: ucfirst(str_replace('per-', '', $addOn->sellingMethod?->config_type ?? 'piece'));

                            return [
                                'product_id' => $addOn->id,
                                'slug' => $addOn->slug,
                                'name' => $addOn->name,
                                'category' => $addOn->category?->name ?? '',
                                'subcategory' => '',
                                'image' => $addOn->thumb_image_url ?? '',
                                'selling_method' => str_replace('_', '-', $addOn->sellingMethod?->config_type ?? 'per-piece'),
                                'unit_label' => $addOnUnitLabel,
                                'length_unit' => $addOn->length_unit,
                                'units_per_order' => $addOn->units_per_order,
                                'min_quantity' => $addOn->min_quantity,
                                'quantity_step' => $addOn->quantity_step,
                                'stock_quantity' => $addOn->effective_stock,
                                'loom_size' => $addOn->loom_size,
                                'unit_price' => $addOn->final_price,
                            ];
                        })->values()->toArray()
                        : [],
                    'added_add_ons' => [],
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
