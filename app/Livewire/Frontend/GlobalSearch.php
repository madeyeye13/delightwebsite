<?php

namespace App\Livewire\Frontend;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    /** @var array<string, array<int, array<string, mixed>>> */
    public array $results = [];

    public function updatedQuery(): void
    {
        $q = trim($this->query);

        if (strlen($q) < 2) {
            $this->results = [];

            return;
        }

        $like = '%'.$q.'%';

        $products = Product::query()
            ->with('category')
            ->where('status', 'active')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('collection', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('tags', 'like', $like);
            })
            ->limit(5)
            ->get()
            ->map(fn (Product $p) => [
                'title' => $p->name,
                'url' => route('products.show', $p->slug),
                'image' => $p->thumb_image_url,
                'price' => $p->final_price,
                'label' => $p->category?->name,
            ])
            ->all();

        $posts = BlogPost::query()
            ->published()
            ->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('excerpt', 'like', $like)
                    ->orWhere('body', 'like', $like);
            })
            ->limit(4)
            ->get()
            ->map(fn (BlogPost $p) => [
                'title' => $p->title,
                'url' => route('blog.show', $p->slug),
                'image' => $p->featured_image_url,
                'excerpt' => $p->excerpt,
            ])
            ->all();

        $categories = Category::query()
            ->active()
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like);
            })
            ->limit(4)
            ->get()
            ->map(fn (Category $c) => [
                'title' => $c->name,
                'url' => route('shop.index').'?category='.$c->slug,
            ])
            ->all();

        $pages = collect([
            ['title' => 'Shop',     'url' => route('shop.index'),     'desc' => 'Browse all fabrics'],
            ['title' => 'Blog',     'url' => route('blog.index'),     'desc' => 'Fabric tips & inspiration'],
            ['title' => 'Cart',     'url' => route('cart.index'),     'desc' => 'View your shopping cart'],
            ['title' => 'About',    'url' => url('/about'),           'desc' => 'About 1st Delightsome Fabrics'],
            ['title' => 'Contact',  'url' => url('/contact'),         'desc' => 'Get in touch with us'],
            ['title' => 'FAQ',      'url' => url('/faq'),             'desc' => 'Frequently asked questions'],
            ['title' => 'Checkout', 'url' => route('checkout.index'), 'desc' => 'Complete your order'],
        ])
            ->filter(fn ($p) => str_contains(strtolower($p['title'].' '.$p['desc']), strtolower($q)))
            ->values()
            ->all();

        $this->results = compact('products', 'posts', 'categories', 'pages');
    }

    public function clearQuery(): void
    {
        $this->query = '';
        $this->results = [];
    }

    public function render(): View
    {
        return view('livewire.frontend.global-search');
    }
}
