@extends('layouts.app')
@section('title', 'Berita & Artikel Terbaru - Lab EPSK UTM')
@section('meta_description', 'Kumpulan informasi, pengumuman, artikel riset, dan wawasan seputar kegiatan Laboratorium Ergonomi dan Perancangan Sistem Kerja UTM.')
@section('content')
<div class="overview-page-wrapper">

    <!-- Header Banner -->
    <div class="overview-hero-header" style="background-image: linear-gradient(to right, rgba(107, 28, 28, 0.85) 30%, rgba(33, 33, 33, 0.4)), url('{{ asset('images/2.jpeg') }}');">
        <div class="overview-hero-content">
            <h1 class="overview-main-title">News &amp; Articles</h1>
            <div class="overview-title-line"></div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="overview-container">

        <!-- Tombol Kembali -->
        <div class="back-navigation">
            <a href="{{ route('home') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

        @if ($newsList->isEmpty())
            <div class="overview-card" style="text-align:center;">
                <p class="overview-text">Belum ada berita yang dipublikasikan.</p>
            </div>
        @else
            <div class="news-grid">
                @foreach ($newsList as $news)
                <div class="news-card">
                    <div>
                        <div class="news-image-wrapper">
                            <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="news-image">
                            <span class="news-category-badge">{{ $news->category->name ?? 'Umum' }}</span>
                        </div>

                        <div class="news-card-body">
                            <div class="news-meta">
                                <span><i class="fa-regular fa-calendar"></i> {{ optional($news->published_at)->format('d M Y') }}</span>
                            </div>
                            <h3 class="news-title">{{ $news->title }}</h3>
                        </div>
                    </div>

                    <div class="news-card-footer">
                        <a href="{{ route('news.show', $news->slug) }}" class="btn-read-more">READ MORE</a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="news-pagination-wrapper">
                {{ $newsList->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
