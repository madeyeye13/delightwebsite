@extends('layouts.admin')
@section('title', 'Orders')
@section('page-title', 'Orders')
@section('breadcrumb')
    <span class="text-xs text-gray-400 dark:text-white/30">Home</span>
    <svg class="w-3 h-3 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">Orders</span>
@endsection
@section('content')
<div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-10 flex flex-col items-center justify-center text-center min-h-[400px]">
    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center mb-4">
        <svg class="w-7 h-7 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
            <rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/>
        </svg>
    </div>
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white font-display">Orders</h2>
    <p class="text-sm text-gray-400 dark:text-white/30 mt-1 max-w-xs">Your orders page content will go here. Build your orders table, filters, and management UI in this view.</p>
</div>
@endsection