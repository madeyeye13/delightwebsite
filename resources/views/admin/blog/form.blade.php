@extends('layouts.admin')
@section('title', isset($post) ? 'Edit Post' : 'Create Post')
@section('page-title', isset($post) ? 'Edit Post' : 'Create Post')
@section('breadcrumb')
    <span class="text-xs text-neutral-400 dark:text-neutral-500">Home</span>
    <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('admin.blog.index') }}" class="text-xs text-neutral-500 dark:text-neutral-400 hover:text-brand-500">Blog Posts</a>
    <svg class="w-3 h-3 text-neutral-300 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">{{ isset($post) ? 'Edit Post' : 'Create Post' }}</span>
@endsection

@section('content')
@if(isset($post))
    <livewire:admin.blog.blog-form :post="$post" />
@else
    <livewire:admin.blog.blog-form />
@endif
@endsection
