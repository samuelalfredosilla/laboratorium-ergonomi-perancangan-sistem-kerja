@extends('layouts.admin')

@section('title', 'Tambah Berita')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.news.index') }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:border-maroon-200 hover:text-maroon-600">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Tambah Berita</h1>
            <p class="mt-1 text-sm text-slate-500">Tulis dan publikasikan berita atau artikel baru.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
        @include('admin.news._form')
    </form>
</div>
@endsection
