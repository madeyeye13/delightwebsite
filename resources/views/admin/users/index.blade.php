@extends('layouts.admin')
@section('title', 'Users')
@section('page-title', 'Users')
@section('breadcrumb')
    <span class="text-xs text-gray-400 dark:text-white/30">Home</span>
    <svg class="w-3 h-3 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">Users</span>
@endsection
@section('content')
<div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-10 flex flex-col items-center justify-center text-center min-h-[400px]">
    <div class="w-14 h-14 rounded-2xl bg-purple-500/10 flex items-center justify-center mb-4">
        <svg class="w-7 h-7 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
    </div>
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white font-display">Users</h2>
    <p class="text-sm text-gray-400 dark:text-white/30 mt-1 max-w-xs">View and manage customers, admins, and all registered users of your store.</p>
</div>
@endsection