# Admin Product Management — Implementation Documentation

> Laravel 12 · Livewire v4 · Alpine.js v3 · Tailwind CSS v3 · Spatie MediaLibrary v11

---

## Table of Contents

1. [Database Schema](#1-database-schema)
2. [Eloquent Models](#2-eloquent-models)
3. [Seeders](#3-seeders)
4. [Livewire Components](#4-livewire-components)
5. [Livewire Views](#5-livewire-views)
6. [Routing](#6-routing)
7. [Toast Notification System](#7-toast-notification-system)
8. [Data Flow](#8-data-flow)
9. [Architecture Decisions](#9-architecture-decisions)
10. [Status & Remaining Work](#10-status--remaining-work)

---

## 1. Database Schema

All migrations live in `database/migrations/`.

### `categories`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `name` | string | |
| `slug` | string | unique, auto-generated |
| `description` | text | nullable |
| `is_active` | boolean | default true |
| `sort_order` | integer | default 0 |
| `timestamps` | | |

### `selling_methods`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `name` | string | |
| `slug` | string | unique |
| `label` | string | display label |
| `unit_label` | string | e.g. "yards", "pieces" |
| `description` | text | nullable |
| `is_active` | boolean | default true |
| `sort_order` | integer | default 0 |
| `timestamps` | | |

### `products`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `category_id` | foreignId | nullable, cascades on delete |
| `selling_method_id` | foreignId | nullable |
| `name` | string | |
| `slug` | string | unique, auto-generated |
| `sku` | string | unique, auto-generated |
| `description` | text | nullable |
| `price` | decimal(12,2) | |
| `compare_price` | decimal(12,2) | nullable |
| `config_type` | json | selling-method-specific config (quantities, lengths, etc.) |
| `stock` | integer | default 0 |
| `low_stock_threshold` | integer | default 5 |
| `status` | enum | active / draft / archived |
| `featured` | boolean | default false |
| `seo_title` | string | nullable |
| `seo_description` | text | nullable |
| `seo_keywords` | string | nullable |
| `deleted_at` | timestamp | soft deletes |
| `timestamps` | | |

### `product_variants`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `product_id` | foreignId | cascades on delete |
| `color_name` | string | |
| `color_hex` | string | nullable |
| `price_modifier` | decimal(10,2) | default 0 |
| `stock` | integer | default 0 |
| `is_default` | boolean | default false |
| `sort_order` | integer | default 0 |
| `timestamps` | | |

### `product_coupons`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `product_id` | foreignId | cascades on delete |
| `code` | string | uppercased |
| `type` | enum | fixed / percentage |
| `value` | decimal(10,2) | discount amount |
| `min_order_amount` | decimal(12,2) | nullable |
| `max_uses` | integer | nullable |
| `uses_count` | integer | default 0 |
| `expires_at` | timestamp | nullable |
| `is_active` | boolean | default true |
| `timestamps` | | |

### `wishlists`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `user_id` | foreignId | cascades on delete |
| `product_id` | foreignId | cascades on delete |
| `timestamps` | | |

---

## 2. Eloquent Models

### `app/Models/Category.php`
- **Scopes:** `active()`, `ordered()`
- **Relationship:** `hasMany(Product::class)`
- **Auto-generates** `slug` from `name` on create

### `app/Models/SellingMethod.php`
- **Scopes:** `active()`, `ordered()`
- **Relationship:** `hasMany(Product::class)`

### `app/Models/Product.php`
- **Traits:** `SoftDeletes`, `HasMedia`, `InteractsWithMedia`
- **Relationships:**
  - `belongsTo(Category::class)`
  - `belongsTo(SellingMethod::class)`
  - `hasMany(ProductVariant::class)`
  - `hasMany(ProductCoupon::class)`
  - `hasMany(Wishlist::class)`
- **Accessors:** `main_image_url`, `final_price`, `discount_percentage`, `effective_stock`, `is_low_stock`
- **Scopes:** `active()`, `featured()`, `inStock()`
- **Media collections:** `main_image`, `thumbnails`

### `app/Models/ProductVariant.php`
- **Traits:** `HasMedia`, `InteractsWithMedia`
- **Relationship:** `belongsTo(Product::class)`
- **Media collections:** `variant_main_image`, `variant_thumbnails`

### `app/Models/ProductCoupon.php`
- **Relationship:** `belongsTo(Product::class)`
- **Accessor:** `is_expired`

### `app/Models/Wishlist.php`
- **Relationships:** `belongsTo(User::class)`, `belongsTo(Product::class)`

---

## 3. Seeders

### `database/seeders/CategorySeeder.php`
Seeds 6 default categories:
- Lace Fabrics
- Aso Oke
- Ankara & Prints
- Caps
- Headties
- Accessories

### `database/seeders/SellingMethodSeeder.php`
Seeds 5 selling methods:
- Per Piece
- Per Set
- Per Bundle
- Per Length (yards)
- Per Loom

### Run seeders
```bash
php artisan db:seed
```

---

## 4. Livewire Components

Both components live in `app/Livewire/Admin/Products/`. They are **embedded** (not full-page) Livewire components — rendered via `<livewire:...>` tags inside the admin Blade views at `resources/views/admin/products/`. No `#[Layout]` attribute is used.

---

### `ProductIndex.php`

Full-page Livewire component for the product listing page.

#### Methods

| Method | Signature | Description |
|--------|-----------|-------------|
| `deleteProduct` | `(int $id): void` | Soft-deletes a product, dispatches success toast |
| `bulkDelete` | `(array $ids): void` | Soft-deletes multiple products |
| `updateStatus` | `(int $id, string $status): void` | Updates status: active / draft / archived |
| `toggleFeatured` | `(int $id): void` | Toggles `featured` boolean |
| `bulkUpdateStatus` | `(array $ids, string $status): void` | Bulk status update |
| `bulkUpdateFeatured` | `(array $ids, bool $featured): void` | Bulk feature / unfeature |
| `buildUnitSummary` | `(Product $product): string` | Generates human-readable unit text from `config_type` |
| `render` | `(): View` | Loads products with eager-loaded relationships; calculates stats; returns view |

#### `render()` output (passed to view)
| Variable | Type | Purpose |
|----------|------|---------|
| `$productsJson` | string (JSON) | Alpine-ready product array |
| `$categoriesJson` | string (JSON) | Category id/name/slug map for Alpine |
| `$sellingMethodsJson` | string (JSON) | Selling method map for Alpine |
| `$stats` | array | `total`, `active`, `drafts`, `low_stock`, `featured` |
| `$categories` | Collection | Raw Eloquent collection for Blade `@foreach` filter options |
| `$sellingMethods` | Collection | Raw Eloquent collection for Blade `@foreach` filter options |

---

### `ProductForm.php`

Full-page Livewire component for create / edit product form.

#### Methods

| Method | Signature | Description |
|--------|-----------|-------------|
| `mount` | `(?Product $product = null): void` | Stores product; supports route model binding |
| `save` | `(array $payload, string $status): array` | Core save — creates/updates product, handles images/variants/coupons; returns `['success', 'productId', 'redirectUrl']` |
| `extractProductData` | `(array $payload): array` | Maps Alpine form fields → DB column names |
| `handleImages` | `(Product $product, array $payload): void` | Base64 → Spatie MediaLibrary (`main_image`, `thumbnails`) |
| `handleVariants` | `(Product $product, array $payload): void` | `updateOrCreate` per `color_name`; deletes removed variants |
| `handleCoupons` | `(Product $product, array $payload): void` | `updateOrCreate` per `code` (uppercased); deactivates removed |
| `buildEditPayload` | `(Product $product): string` | Serialises product + relationships to JSON for Alpine hydration |
| `render` | `(): View` | Loads active categories + selling methods; builds JSON; handles edit mode |

#### `render()` output
| Variable | Type | Purpose |
|----------|------|---------|
| `$categoriesJson` | string (JSON) | For Alpine `sellingMethods` / `categories` arrays |
| `$sellingMethodsJson` | string (JSON) | |
| `$productJson` | string (JSON) | Full product payload for Alpine form hydration (edit mode) |

---

## 5. Views

### Admin Blade Views (layout owners)

These live in `resources/views/admin/products/` and own the page structure — `@extends`, `@section`, breadcrumbs, title. They embed the Livewire components.

#### `admin/products/index.blade.php`

```blade
@extends('layouts.admin')
@section('title', 'Products')
@section('page-title', 'Products')
@section('breadcrumb') ... @endsection

@section('content')
<livewire:admin.products.product-index />
@endsection
```

#### `admin/products/form.blade.php`

```blade
@extends('layouts.admin')
@section('title', isset($product) ? 'Edit Product' : 'Create Product')
@section('page-title', ...)
@section('breadcrumb') ... @endsection

@section('content')
<livewire:admin.products.product-form :product="$product ?? null" />
@endsection
```

---

### Livewire Component Views (component output only)

These live in `resources/views/livewire/admin/products/`. They contain **no** `@extends`, `@section`, or layout directives — just the component HTML and `@push` stacks which bubble up through the parent layout.

#### `product-index.blade.php`

| Block | Content |
|-------|---------|
| `window.__adminProducts` | Server-rendered JSON of all products |
| `window.__adminMeta` | Category + selling method maps |
| Stats bar | 5 cards — live Blade values: `{{ $stats['total'] }}` etc. |
| Filter bar | Category + Selling Method from DB (`@foreach`). Status / Featured / Stock / Sort static Alpine dropdowns |
| Bulk actions bar | Shown when rows selected; Feature, Unfeature, Activate, Draft, Delete |
| Desktop table | All columns: Product, Category, Method, Unit, Price, Stock, Status, Featured, Add-ons, Updated, Actions |
| Mobile cards | Replaces table on small screens |
| Empty state | Contextual message (no products vs no filter results) |
| Delete modal | `x-teleport="body"`, `z-[9999]`, slide animation, spinner |
| `@push('scripts')` | `productListManager()` Alpine function — all write ops via `$wire.*` |

#### `product-form.blade.php`

| Block | Content |
|-------|---------|
| `@push('styles')` | Quill editor CSS + dark-mode overrides |
| `window.__productFormData` | `{ categories, sellingMethods, product }` JSON |
| Main div | `x-data="productFormManager()"` — full create/edit form |
| `init()` | Hydrates Alpine form from `window.__productFormData?.product` when editing |
| `saveDraft()` | `this.$wire.save(payload, 'draft')` → redirect on success |
| `publishProduct()` | `this.$wire.save(payload, 'active')` → redirect on success |
| `@push('scripts')` | `productFormManager()` Alpine function + Quill editor setup |

---

## 6. Routing

**File:** `routes/web.php`  
All product routes sit inside the `auth + admin` middleware group, prefixed with `admin/`, named `admin.*`. Routes return **admin Blade views**, which in turn embed the Livewire components.

```php
Route::get('/products', fn () => view('admin.products.index'))->name('products.index');

Route::get('/products/create', fn () => view('admin.products.form'))->name('products.create');

Route::get('/products/{product}/edit', function (Product $product) {
    return view('admin.products.form', compact('product'));
})->name('products.edit');
```

- `{product}` uses route model binding — Laravel resolves the `Product` Eloquent model and passes it to the view, which forwards it to `<livewire:admin.products.product-form :product="$product" />`
- Named route helpers: `route('admin.products.index')`, `route('admin.products.create')`, `route('admin.products.edit', $product)`

---

## 7. Toast Notification System

**File:** `resources/views/layouts/admin.blade.php`

### How it works

```
PHP: $this->dispatch('toast', type: 'success', message: 'Done.')
         ↓  (Livewire browser event)
Alpine: @toast.window="addToast($event.detail)"  → renders toast
```

### `adminToastManager` (Alpine.data)
Registered in the `alpine:init` block. Manages an array of toasts with auto-dismiss (4s) and manual dismiss.

### Toast types
| Type | Style |
|------|-------|
| `success` | `bg-neutral-900` / white text (or inverted in light mode) |
| `error` | `bg-red-600` / white text |
| `warning` | `bg-yellow-500` / dark text |
| `info` | `bg-blue-600` / white text |

Fixed overlay: `top-4 right-4`, `z-[10000]`, no border-radius (matches admin design language), slide-in from right.

---

## 8. Data Flow

```
HTTP GET /admin/products
    ↓
ProductIndex::render()
    ├─ Category::active()->ordered()->get()
    ├─ SellingMethod::active()->ordered()->get()
    ├─ Product::with(['category','sellingMethod','variants'])->get()
    ├─ calculates 5 stats
    └─ serialises to JSON
         ↓
Blade renders HTML
    ├─ injects window.__adminProducts (JSON)
    ├─ injects window.__adminMeta (JSON)
    ├─ renders {{ $stats['...'] }} live values
    └─ renders category/method filter options via @foreach
         ↓
Alpine productListManager() initialises
    ├─ reads window.__adminProducts
    └─ runs applyFilters() client-side
         ↓
User filters/sorts/searches → instant Alpine (no round-trip)
User deletes/archives/features → $wire.method() → Livewire → DB → toast dispatched → component re-renders
```

---

## 9. Architecture Decisions

| Decision | Reason |
|----------|--------|
| Alpine handles all filtering/sorting/selection | Instant response; no server round-trips for read operations |
| `$wire.*` only for writes | Delete, status change, save — only these need server validation |
| Window JSON injection | Avoids Livewire-managed reactive state for large arrays, preventing unnecessary re-renders |
| Livewire embedded in admin views (not full-page) | Admin layout uses `@yield('content')`, not `{{ $slot }}`. This version of Livewire v4 does not support the `section:` named parameter on `#[Layout]`. Embedding via `<livewire:>` tags in normal Blade views is the cleanest solution. |
| Admin views own layout, Livewire views own component output | Clean separation: `admin/products/index.blade.php` handles title/breadcrumb/layout; `livewire/admin/products/product-index.blade.php` handles only the rendered HTML |
| Soft deletes on `Product` | Deleted products are recoverable; no hard-delete exposed in UI |
| Spatie MediaLibrary | Handles image variants, conversions, and storage abstraction |
| Route model binding on `{product}` | Laravel auto-resolves `Product` instance → passed to view → forwarded to Livewire component via `:product` prop |

---

## 10. Status & Remaining Work

### Completed ✅

| Area | File(s) |
|------|---------|
| DB migrations | `database/migrations/` |
| Models + relationships | `app/Models/` |
| Seeders (categories + selling methods) | `database/seeders/` |
| `ProductIndex` Livewire component | `app/Livewire/Admin/Products/ProductIndex.php` |
| `ProductForm` Livewire component | `app/Livewire/Admin/Products/ProductForm.php` |
| Livewire product index view | `resources/views/livewire/admin/products/product-index.blade.php` |
| Livewire product form view | `resources/views/livewire/admin/products/product-form.blade.php` |
| Admin index page (embeds Livewire) | `resources/views/admin/products/index.blade.php` |
| Admin form page (embeds Livewire) | `resources/views/admin/products/form.blade.php` |
| Admin routes (return views) | `routes/web.php` |
| Toast notification system | `resources/views/layouts/admin.blade.php` |

### Remaining ⚠️

| Area | Notes |
|------|-------|
| Media upload wiring | `handleImages()` exists in `ProductForm.php`; Alpine `buildPayload()` image fields are commented out pending file picker integration |
| Livewire component tests | Feature tests for `ProductIndex` and `ProductForm` not yet written |

### Not Started ❌

- Frontend product listing + detail pages
- Wishlist system (`Wishlist` model exists, needs Livewire component)
- Currency + exchange rate admin
- Add-ons management
- Reviews system
- Advanced SEO tools

---

---

# Phase 2: Storefront & Backend Integration

> All features below were implemented after the admin product management phase. Tailwind classes and HTML structure were not altered — only backend wiring and dynamic data were added.

---

## 11. New Database Migrations

| Migration | Table | Purpose |
|-----------|-------|---------|
| `create_currencies_table` | `currencies` | Supported currencies with markup |
| `create_exchange_rates_table` | `exchange_rates` | Rate snapshots per currency |
| `create_carts_table` | `carts` | Guest (session) + auth user carts |
| `create_cart_items_table` | `cart_items` | Line items per cart |
| `create_reviews_table` | `reviews` | Product reviews, 1–5 stars, moderated |

### `currencies`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `code` | string(3) | e.g. `USD` |
| `name` | string | e.g. `US Dollar` |
| `symbol` | string | e.g. `$` |
| `is_active` | boolean | default true |
| `is_default` | boolean | default false |
| `markup` | decimal(5,4) | multiplier applied on top of rate |
| `timestamps` | | |

### `exchange_rates`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `currency_id` | foreignId | cascades on delete |
| `rate` | decimal(16,8) | NGN → currency rate |
| `fetched_at` | timestamp | when rate was recorded |
| `timestamps` | | |

### `carts`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `user_id` | foreignId | nullable (guest cart) |
| `session_id` | string | indexed; guest identifier |
| `timestamps` | | |

### `cart_items`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `cart_id` | foreignId | cascades on delete |
| `product_id` | foreignId | cascades on delete |
| `variant_id` | foreignId | nullable |
| `quantity` | integer | default 1 |
| `timestamps` | | |

### `reviews`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigIncrements | |
| `product_id` | foreignId | cascades on delete |
| `user_id` | foreignId | cascades on delete |
| `rating` | unsignedTinyInteger | 1–5 |
| `title` | string | nullable |
| `body` | text | nullable |
| `is_approved` | boolean | default false; admin-moderated |
| `timestamps` | | |
| **unique** | `(product_id, user_id)` | one review per user per product |

---

## 12. New & Updated Eloquent Models

### `app/Models/Currency.php`
- **Fillable:** `code`, `name`, `symbol`, `is_active`, `is_default`, `markup`
- **Casts:** `markup` → `decimal:4`, `is_active` / `is_default` → `boolean`
- **Relationships:** `hasMany(ExchangeRate::class)`, `hasOne(ExchangeRate::class)->latestOfMany()` as `latestRate()`
- **Scopes:** `active()`, `default()`

### `app/Models/ExchangeRate.php`
- **Fillable:** `currency_id`, `rate`, `fetched_at`
- **Casts:** `rate` → `decimal:8`, `fetched_at` → `datetime`
- **Relationship:** `belongsTo(Currency::class)`

### `app/Models/Cart.php`
- **Fillable:** `user_id`, `session_id`
- **Relationships:** `belongsTo(User::class)`, `hasMany(CartItem::class)`
- **Scopes:** `forUser(int $userId)`, `forSession(string $sessionId)`

### `app/Models/CartItem.php`
- **Fillable:** `cart_id`, `product_id`, `variant_id`, `quantity`
- **Relationships:** `belongsTo(Cart::class)`, `belongsTo(Product::class)`, `belongsTo(ProductVariant::class, 'variant_id')`

### `app/Models/Review.php`
- **Fillable:** `product_id`, `user_id`, `rating`, `title`, `body`, `is_approved`
- **Casts:** `is_approved` → `boolean`, `rating` → `integer`
- **Relationships:** `belongsTo(Product::class)`, `belongsTo(User::class)`
- **Scope:** `approved()` — where `is_approved = true`

### `app/Models/Product.php` (additions)
New relationships added:
- `reviews()` → `hasMany(Review::class)`
- `approvedReviews()` → `hasMany(Review::class)->where('is_approved', true)`
- `cartItems()` → `hasMany(CartItem::class)`

New method added:
- `toStorefrontArray(): array` — returns normalised array for Alpine/Livewire views. Includes: `id`, `name`, `slug`, `price` (`final_price`), `old_price` (`compare_price`), `unit`, `badge` (New/Sale/null), `category`, `sellingMethod`, `stockQuantity`, `minQuantity`, `quantityStep`, `image` (Spatie `main_image` URL), `variants` (each with `id`, `colorName`, `colorHex`, `image` URL), `addOns`

---

## 13. CurrencySeeder

**File:** `database/seeders/CurrencySeeder.php`  
Seeded 7 currencies with exchange rates. Added to `DatabaseSeeder.php`.

| Code | Name | Symbol | Default |
|------|------|--------|---------|
| NGN | Nigerian Naira | ₦ | ✅ |
| USD | US Dollar | $ | |
| GBP | British Pound | £ | |
| EUR | Euro | € | |
| CAD | Canadian Dollar | CA$ | |
| GHS | Ghanaian Cedi | ₵ | |
| XOF | CFA Franc | CFA | |

---

## 14. CurrencyService

**File:** `app/Services/CurrencyService.php`

Provides DB-backed Alpine store data for the multi-currency system.

### `getAlpineStoreData(): array`

Cached for 10 minutes under key `currency_store_data`. Returns:

```php
[
    'active'  => 'NGN',               // default currency code
    'rates'   => ['NGN' => 1.0, 'USD' => 0.00065, ...],
    'markup'  => ['NGN' => 1.0, 'USD' => 1.02, ...],
    'symbols' => ['NGN' => '₦', 'USD' => '$', ...],
]
```

### `clearCache(): void`
Flushes `currency_store_data` — called from `CurrencyManager` whenever rates or markup are saved.

---

## 15. New Routes

All new routes appended to `routes/web.php`.

```php
// Frontend storefront
Route::get('/shop', fn () => view('frontend.products.index'))->name('shop.index');
Route::get('/products/{product:slug}', fn (Product $product) => view('frontend.products.show', ...))->name('products.show');
Route::get('/cart', fn () => view('frontend.cart.index'))->name('cart.index');

// Admin
Route::get('/admin/currencies', fn () => view('admin.currencies.index'))->name('admin.currencies.index');
```

---

## 16. Livewire Components (Frontend)

All frontend Livewire components live in `app/Livewire/Frontend/`. They are embedded (not full-page) — no `#[Layout]` attribute used.

---

### `FeaturedProducts.php`

**View:** `resources/views/livewire/frontend/featured-products.blade.php`

Queries `Product::active()->featured()->with(['category','sellingMethod','variants','media'])->limit(8)`, maps each to a normalised array. Replaces the former static `@include` partial on the homepage (`resources/views/welcome.blade.php`).

---

### `ShopIndex.php`

**View:** `resources/views/livewire/frontend/shop-index.blade.php`

| Property | Attribute | Alpine query param |
|----------|-----------|-------------------|
| `$search` | `#[Url(as: 'q')]` | `?q=` |
| `$categorySlug` | `#[Url(as: 'category')]` | `?category=` |
| `$sort` | `#[Url(as: 'sort')]` | `?sort=` |

- `mount()`: loads all active categories for filter dropdown
- `render()`: paginates 24 products per page with live search (debounce 400ms), optional category filter via `whereHas`, sort by newest / price asc / price desc / name asc
- View shows: filter bar (search + category + sort), responsive product grid (2→5 cols), pagination via `$products->links()`

---

### `WishlistToggle.php`

**View:** `resources/views/livewire/frontend/wishlist-toggle.blade.php`

- Props: `productId: int`, `wishlisted: bool`
- `mount()`: checks DB for auth'd user's wishlist entry
- `toggle()`: dispatches `open-auth-modal` browser event for guests; for authenticated users toggles (creates / deletes) `Wishlist` record
- View: same button classes as static mock; heart SVG fill is dynamic; `wire:loading` state

---

### `ProductReviews.php`

**View:** `resources/views/livewire/frontend/product-reviews.blade.php`

- Props: `productId`, `showForm`, `submitted`, `userHasReviewed`, `rating` (0), `title`, `body`
- Public state: `$reviews` (Collection), `$avgRating` (float), `$reviewCount` (int), `$ratingBreakdown` (array, keys 5→1)
- `mount()`: calls `loadReviews()` — loads approved reviews with user relationship, computes stats, checks if current user already reviewed
- `setRating(int $value)`: sets star picker value
- `toggleForm()`: dispatches `open-auth-modal` for guests, toggles form visibility for auth'd users
- `submitReview()`: validates (rating required 1–5, body min 10 chars), `updateOrCreate` with `is_approved = false`, reloads stats
- View: summary bar (avg star display + per-star progress bars), `@forelse($reviews)` loop, write-a-review toggle, inline form with star picker + title + textarea

---

### `CartSync.php`

**View:** `resources/views/livewire/frontend/cart-sync.blade.php` (invisible `<div>`)

The DB persistence bridge between Alpine's `$store.cart` and the database.

| Event Listener | `#[On]` | Action |
|----------------|---------|--------|
| `cart:add` | `onCartAdd(array $item)` | `updateOrCreate` CartItem in DB; dispatches `cart:synced` |
| `cart:update-qty` | `onCartUpdateQty(int $cartItemId, int $quantity)` | Updates quantity in DB; dispatches `cart:synced` |
| `cart:remove` | `onCartRemove(int $cartItemId)` | Deletes CartItem from DB; dispatches `cart:synced` |

- `mount()`: calls `resolveCart()`, builds Alpine items array from DB, dispatches `cart:initialized` browser event
- `resolveCart(bool $create = false)`: finds Cart by `user_id` (auth) or `session_id` (guest)
- `buildAlpineItems(Cart $cart)`: maps CartItems → Alpine-compatible arrays with product data
- `static mergeSessionCartIntoUser(int $userId)`: merges guest cart into user cart on login
- Cart view listens: `@cart:initialized.window` → `$store.cart.items = $event.detail.items ?? []`; `@cart:synced.window` → updates items array

---

## 17. Livewire Components (Admin)

### `CurrencyManager.php`

**File:** `app/Livewire/Admin/Settings/CurrencyManager.php`  
**View:** `resources/views/livewire/admin/settings/currency-manager.blade.php`  
**Wrapper:** `resources/views/admin/currencies/index.blade.php`

| Method | Description |
|--------|-------------|
| `editCurrency(int $id)` | Loads currency into edit state |
| `saveRate(int $id)` | Validates and saves `rate` + `markup`; calls `CurrencyService::clearCache()` |
| `setDefault(int $id)` | Sets one currency as default (unsets all others) |
| `toggleActive(int $id)` | Toggles `is_active` flag |

View shows a table of currencies: code, name, symbol, rate, markup, default toggle, active toggle, inline edit form with save/cancel.

---

## 16. Alpine Currency System

**Architecture:**

```
DB (currencies + exchange_rates)
    ↓ CurrencyService::getAlpineStoreData() [10min cache]
    ↓ @json injected into cart-panel.blade.php via @php block
Alpine $store.currency = { active, rates, markup, symbols, ... }
    ↓ $store.currency.format(ngnAmount)   ← used everywhere
    ↓ $store.currency.convert(ngnAmount)  ← raw conversion
```

`$store.currency` methods:
- `format(ngnAmount)`: converts + formats with symbol and 2dp
- `convert(ngnAmount)`: `amount / rate[active] * markup[active]`
- `select(code)`: sets `active`, persists to `localStorage`

**Header `currencyList` getter:**  
Reads `Object.keys($store.currency.rates)` and maps to `[{code, flag}]` with a local `flagMap` fallback. Used by Alpine `x-for` loops in the desktop dropdown and mobile drawer — replaces former `@foreach` Blade loops, so the list updates without page reload when the store is ready.

---

## 17. Alpine Cart System

**Architecture:**

```
User action (Add to Cart button)
    ↓ $store.cart.addItem({...})    ← instant UI update
    ↓ Livewire.dispatch('cart:add', [...])
        ↓ CartSync #[On('cart:add')]  ← DB persist
        ↓ Livewire.dispatch('cart:synced', [...])
    ↓ @cart:synced.window → $store.cart.items = synced items
```

`$store.cart` key properties and methods:
- `items: []` — populated by `cart:initialized` on page load
- `addItem(product)` — deduplicates by `id+variantId`, increments qty or pushes new item, opens panel, dispatches `cart:add`
- `increaseQty(item)` / `decreaseQty(item)` — dispatches `cart:update-qty`
- `removeItem(item)` — dispatches `cart:remove`
- Computed: `items_count`, `items_subtotal`, `add_ons_total`, `cart_total`
- `lineTotal(item)` — `unit_price × qty`

---

## 18. Cart Panel & Cart Page

### Cart Panel (`resources/views/partials/frontend/cart-panel.blade.php`)
- `items: []` — mock data removed; CartSync populates on mount via `cart:initialized` event
- Currency store injected via `@php $currencyData = app(\App\Services\CurrencyService::class)->getAlpineStoreData() @endphp`
- All prices rendered via `$store.currency.format()`

### Cart Page (`resources/views/frontend/cart/index.blade.php`)
All 5 price locations connected to Alpine store:

| Location | Expression |
|----------|-----------|
| Unit price per item | `$store.currency.format(item.unit_price)` |
| Desktop line total | `$store.currency.format($store.cart.lineTotal(item))` |
| Mobile line total | `$store.currency.format($store.cart.lineTotal(item))` |
| Subtotal | `$store.currency.format($store.cart.items_subtotal)` |
| Add-ons total | `$store.currency.format($store.cart.add_ons_total)` |
| Estimated total | `$store.currency.format($store.cart.cart_total)` |

All with `₦` HTML entity fallback if the Alpine store is not yet ready.

---

## 19. Admin Sidebar Update

**File:** `resources/views/partials/admin/sidebar.blade.php`

Added "Currencies" nav item after the Settings link:
- Icon: coin/currency SVG
- Link: `route('admin.currencies.index')`
- Active highlight: `request()->routeIs('admin.currencies.*')`

---

## 20. Updated Status

### All Completed ✅

| Area | File(s) |
|------|---------|
| DB migrations (all 8 tables) | `database/migrations/` |
| Models + relationships | `app/Models/` |
| Seeders (categories, selling methods, currencies + rates) | `database/seeders/` |
| `ProductIndex` Livewire | `app/Livewire/Admin/Products/ProductIndex.php` |
| `ProductForm` Livewire | `app/Livewire/Admin/Products/ProductForm.php` |
| `CurrencyManager` Livewire | `app/Livewire/Admin/Settings/CurrencyManager.php` |
| `FeaturedProducts` Livewire | `app/Livewire/Frontend/FeaturedProducts.php` |
| `ShopIndex` Livewire | `app/Livewire/Frontend/ShopIndex.php` |
| `WishlistToggle` Livewire | `app/Livewire/Frontend/WishlistToggle.php` |
| `ProductReviews` Livewire | `app/Livewire/Frontend/ProductReviews.php` |
| `CartSync` Livewire | `app/Livewire/Frontend/CartSync.php` |
| `CurrencyService` | `app/Services/CurrencyService.php` |
| All Livewire views | `resources/views/livewire/` |
| All wrapper views | `resources/views/admin/` + `resources/views/frontend/` |
| Homepage live data | `resources/views/welcome.blade.php` |
| Cart panel live data | `resources/views/partials/frontend/cart-panel.blade.php` |
| Cart page live prices | `resources/views/frontend/cart/index.blade.php` |
| Header dynamic currencies | `resources/views/partials/frontend/header.blade.php` |
| Admin sidebar Currencies link | `resources/views/partials/admin/sidebar.blade.php` |
| All routes | `routes/web.php` |
| Toast system | `resources/views/layouts/admin.blade.php` |
| Tests: 25 passed (61 assertions) | `tests/` |

### Remaining / Not Started ⚠️

| Area | Notes |
|------|-------|
| Auth modal | `open-auth-modal` browser event is dispatched by `WishlistToggle` and `ProductReviews` but the modal component does not yet exist |
| Checkout flow | No route, view, or Livewire component exists yet |
| Order management | No orders table or model |
| Media upload wiring | `handleImages()` exists in `ProductForm.php`; Alpine image fields are commented out pending file picker integration |
| Add-on cart persistence | Currently client-side only in Alpine; `CartSync` does not yet persist add-ons |
| Category pages | No `/category/{slug}` route or view |
| Review moderation | `is_approved` field exists; no admin UI to approve/reject reviews |
