@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <span class="text-xs text-gray-400 dark:text-white/30">Home</span>
    <svg class="w-3 h-3 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">Dashboard</span>
@endsection

@section('content')
<livewire:admin.dashboard />
@endsection
