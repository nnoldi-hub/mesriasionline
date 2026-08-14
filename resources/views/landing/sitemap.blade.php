<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    @foreach($categories as $category)
        <url>
            <loc>{{ route('landing.category', $category->slug) }}</loc>
            <lastmod>{{ now()->toDateString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
    @foreach($combos as $combo)
        <url>
            <loc>{{ route('landing.category-city', [$combo->category->slug, $combo->location->slug]) }}</loc>
            <lastmod>{{ now()->toDateString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset>
