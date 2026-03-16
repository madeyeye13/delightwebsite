{{--
    Admin Media Library
    File: resources/views/admin/media/index.blade.php
    Stack: Blade + Alpine.js + Tailwind CSS
    Backend: Livewire-ready â€” every wire point is clearly commented
--}}

@extends('layouts.admin')

@section('title', 'Media Library')

@section('page-title', 'Media Library')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"
       class="text-[11px] text-gray-400 dark:text-white/30 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">
        Admin
    </a>
    <svg class="w-3 h-3 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="9 18 15 12 9 6"/>
    </svg>
    <span class="text-[11px] text-gray-500 dark:text-white/40 font-medium">Media Library</span>
@endsection

@push('styles')
<style>
    /* Drag-over highlight â€” used by media-manager Livewire view */
    .dropzone-active {
        border-color: #10b981 !important;
        background-color: rgba(16, 185, 129, 0.04);
    }
    /* Media card selected ring */
    .media-card-selected {
        outline: 2px solid #10b981;
        outline-offset: -2px;
    }
    /* Loading skeleton shimmer */
    @keyframes shimmer-sweep {
        0%   { background-position: -400px 0; }
        100% { background-position:  400px 0; }
    }
    .skeleton-shimmer {
        background: linear-gradient(90deg, #e5e7eb 25%, #f3f4f6 50%, #e5e7eb 75%);
        background-size: 400px 100%;
        animation: shimmer-sweep 1.4s ease infinite;
    }
    .dark .skeleton-shimmer {
        background: linear-gradient(90deg, #1a1d24 25%, #22262f 50%, #1a1d24 75%);
        background-size: 400px 100%;
    }
    /* Folder active state */
    .folder-active {
        background-color: rgba(16, 185, 129, 0.08);
        color: #10b981;
    }
    .dark .folder-active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #34d399;
    }
    /* Card staggered fade-in */
    @keyframes cardFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .media-card-anim { animation: cardFadeIn 0.18s ease both; }
    /* Panel thin scrollbar */
    .scrollbar-thin::-webkit-scrollbar { width: 4px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 99px; }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); }
</style>
@endpush

@section('content')
<livewire:admin.media.media-manager />
@endsection
