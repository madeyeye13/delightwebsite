{{--
╔══════════════════════════════════════════════════════════════════╗
║  ADMIN PRODUCT LIST PAGE - REFACTORED                             ║
║  Unified Alpine state with real filtering & custom dropdowns      ║
╚══════════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.admin')
@section('title', 'Products')
@section('page-title', 'Products')
@section('breadcrumb')
    <span class="text-xs text-neutral-400 dark:text-neutral-500">Home</span>
    <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">Products</span>
@endsection

@section('content')
<livewire:admin.products.product-index />
@endsection
