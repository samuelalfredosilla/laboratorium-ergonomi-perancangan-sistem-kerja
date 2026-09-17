@extends('layouts.app')
@section('title', $news->title . ' - Berita Lab EPSK UTM')
@section('meta_description', Str::limit(strip_tags($news->content), 150))
@section('content')
<div class="overview-page-wrapper">

    <!-- Header Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.85) 30%, rgba(33, 33, 33, 0.4)), url('{{ asset('images/2.jpeg') }}');">
        <div class="overview-hero-content">
            <h1 class="overview-main-title">Detail Berita</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="overview-container">

        <!-- Tombol Kembali -->
        <div class="back-navigation">
            <a href="{{ route('news.index') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Berita
            </a>
        </div>

        <div class="overview-card">
            <span class="card-tag tag-history">{{ $news->category->name ?? 'Umum' }}</span>

            <h1 class="article-title">{{ $news->title }}</h1>

            <div class="news-meta article-meta">
                <span><i class="fa-regular fa-calendar"></i> {{ optional($news->published_at)->format('d M Y') }}</span>
                <span><i class="fa-regular fa-user"></i> {{ $news->author->name ?? 'Admin EPSK' }}</span>
            </div>

            <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="article-image">

            <div class="article-content">
                {!! $news->content !!}
            </div>
        </div>

    </div>
</div>
@endsection
