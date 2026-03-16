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
    public function addItem(int $productId, ?int $variantId = null, int $quantity = 1): void
    {
        $product = Product::with(['category', 'sellingMethod', 'media'])->find($productId);
        if (! $product) {
            return;
        }

        $cart = $this->resolveCart(create: true);

        $existing = $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => max(1, $quantity),
            ]);
        }

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
            ->load('product.media', 'product.sellingMethod', 'product.category.parent', 'variant.media')
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
                    'suggested_add_ons' => [],
                    'added_add_ons' => [],
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
