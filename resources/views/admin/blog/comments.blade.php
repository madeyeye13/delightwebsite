@extends('layouts.admin')
@section('title', 'Blog Comments')
@section('page-title', 'Blog Comments')
@section('breadcrumb')
    <span class="text-xs text-neutral-400 dark:text-neutral-500">Home</span>
    <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('admin.blog.index') }}" class="text-xs text-neutral-400 dark:text-neutral-500 hover:text-emerald-500 transition-colors">Blog Posts</a>
    <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">Comments</span>
@endsection

@section('content')
<livewire:admin.blog.comment-index />
@endsection
