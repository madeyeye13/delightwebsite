{{--
╔══════════════════════════════════════════════════════════════════╗
║  ADMIN PRODUCT CREATE/EDIT PAGE - ENHANCED v3                     ║
║  Added: SKU, Tags, Collection, Category Modal, Selling Method     ║
║         Modal, config_type-driven unit rendering, extensible      ║
║         selling methods architecture, updated payload             ║
╚══════════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.admin')
@section('title', isset($product) ? 'Edit Product' : 'Create Product')
@section('page-title', isset($product) ? 'Edit Product' : 'Create Product')
@section('breadcrumb')
    <span class="text-xs text-neutral-400 dark:text-neutral-500">Home</span>
    <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('admin.products.index') }}" class="text-xs text-neutral-500 dark:text-neutral-400 hover:text-brand-500">Products</a>
    <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">{{ isset($product) ? 'Edit Product' : 'Create Product' }}</span>
@endsection

@section('content')
<livewire:admin.products.product-form :product="$product ?? null" />
@endsection