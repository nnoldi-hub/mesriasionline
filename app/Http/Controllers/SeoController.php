<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * Generate XML Sitemap.
     */
    public function sitemap()
    {
        $craftsmen = User::where('role', 'specialist')
            ->where('is_active', true)
            ->select('slug', 'updated_at')
            ->get();
        
        $articles = Article::where('status', 'published')
            ->select('slug', 'updated_at')
            ->get();
        
        $categories = Category::where('is_active', true)
            ->select('slug', 'updated_at')
            ->get();
        
        $locations = Location::where('is_active', true)
            ->select('slug', 'updated_at')
            ->get();
        
        $content = view('seo.sitemap', compact('craftsmen', 'articles', 'categories', 'locations'))->render();
        
        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate robots.txt dynamically.
     */
    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /craftsman/\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /register\n";
        $content .= "Disallow: /mesaje/\n";
        $content .= "Disallow: /notificari/\n";
        $content .= "\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
        
        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
