<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Services\SeoService setTitle(string $title, bool $appendSiteName = true)
 * @method static \App\Services\SeoService setDescription(string $description)
 * @method static \App\Services\SeoService setKeywords(string|array $keywords)
 * @method static \App\Services\SeoService setCanonical(string $url)
 * @method static \App\Services\SeoService setOgType(string $type)
 * @method static \App\Services\SeoService setImage(string $image)
 * @method static \App\Services\SeoService setAuthor(string $author)
 * @method static \App\Services\SeoService setPublishedTime(string $time)
 * @method static \App\Services\SeoService setModifiedTime(string $time)
 * @method static \App\Services\SeoService setTwitterCard(string $card)
 * @method static \App\Services\SeoService addBreadcrumb(string $name, ?string $url = null)
 * @method static \App\Services\SeoService forCraftsman($craftsman)
 * @method static \App\Services\SeoService forArticle($article)
 * @method static \App\Services\SeoService forCategory($category, $location = null)
 * @method static \App\Services\SeoService forService($service)
 * @method static \App\Services\SeoService forQuestion($question)
 * @method static \App\Services\SeoService forStaticPage(string $title, string $description, array $keywords = [])
 * @method static string getTitle()
 * @method static string getDescription()
 * @method static string getCanonical()
 * @method static string getImage()
 * @method static array getBreadcrumbs()
 * @method static string render()
 * @method static \App\Services\SeoService reset()
 *
 * @see \App\Services\SeoService
 */
class SEO extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\SeoService::class;
    }
}
