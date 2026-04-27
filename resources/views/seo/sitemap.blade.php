<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Static pages --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toW3cString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('about') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ route('contact') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ route('articole') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ route('intrebari') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    
    {{-- Craftsmen profiles --}}
    @foreach($craftsmen as $craftsman)
    <url>
        <loc>{{ route('craftsman.show', $craftsman->slug) }}</loc>
        <lastmod>{{ $craftsman->updated_at->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    @endforeach
    
    {{-- Articles --}}
    @foreach($articles as $article)
    <url>
        <loc>{{ route('articole.show', $article->slug) }}</loc>
        <lastmod>{{ $article->updated_at->toW3cString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
    
    {{-- Categories (if you have category pages) --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ url('/?category=' . $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at?->toW3cString() ?? now()->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
    
    {{-- Locations (if you have location pages) --}}
    @foreach($locations as $location)
    <url>
        <loc>{{ url('/?location=' . $location->slug) }}</loc>
        <lastmod>{{ $location->updated_at?->toW3cString() ?? now()->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>
