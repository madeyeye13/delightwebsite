@extends('layouts.admin')
@section('title', 'Products')
@section('page-title', 'Products')
@section('breadcrumb')
    <span class="text-xs text-gray-400 dark:text-white/30">Home</span>
    <svg class="w-3 h-3 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">Products</span>
@endsection
@section('content')
<div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-10 flex flex-col items-center justify-center text-center min-h-[400px]">
    <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center mb-4">
        <svg class="w-7 h-7 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>
        </svg>
    </div>
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white font-display">Products</h2>
    <p class="text-sm text-gray-400 dark:text-white/30 mt-1 max-w-xs">Manage your fabric inventory, categories, pricing and product listings here.</p>
</div>
@endsection