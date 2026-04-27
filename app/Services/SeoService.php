<?php

namespace App\Services;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class SeoService
{
    protected string $title = '';
    protected string $description = '';
    protected string $keywords = '';
    protected string $canonicalUrl = '';
    protected string $ogType = 'website';
    protected string $ogImage = '';
    protected string $ogUrl = '';
    protected string $twitterCard = 'summary_large_image';
    protected array $alternateUrls = [];
    protected ?string $author = null;
    protected ?string $publishedTime = null;
    protected ?string $modifiedTime = null;
    protected array $breadcrumbs = [];

    protected string $siteName = 'Omul Potrivit';
    protected string $defaultDescription = 'Omul Potrivit - Reparații, întreținere, siguranță — totul pentru casa ta. Meseriașul potrivit, direct la tine.';
    protected string $defaultImage = '/images/og-default.jpg';
    protected string $twitterHandle = '@omulpotrivit';
    protected string $locale = 'ro_RO';

    /**
     * Set the page title.
     */
    public function setTitle(string $title, bool $appendSiteName = true): self
    {
        $this->title = $appendSiteName ? "{$title} - {$this->siteName}" : $title;
        return $this;
    }

    /**
     * Set the meta description.
     */
    public function setDescription(string $description): self
    {
        $this->description = Str::limit(strip_tags($description), 160);
        return $this;
    }

    /**
     * Set meta keywords.
     */
    public function setKeywords(string|array $keywords): self
    {
        $this->keywords = is_array($keywords) ? implode(', ', $keywords) : $keywords;
        return $this;
    }

    /**
     * Set the canonical URL.
     */
    public function setCanonical(string $url): self
    {
        $this->canonicalUrl = $url;
        return $this;
    }

    /**
     * Set Open Graph type.
     */
    public function setOgType(string $type): self
    {
        $this->ogType = $type;
        return $this;
    }

    /**
     * Set Open Graph image.
     */
    public function setImage(string $image): self
    {
        $this->ogImage = $image;
        return $this;
    }

    /**
     * Set the author.
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Set published time for articles.
     */
    public function setPublishedTime(string $time): self
    {
        $this->publishedTime = $time;
        return $this;
    }

    /**
     * Set modified time for articles.
     */
    public function setModifiedTime(string $time): self
    {
        $this->modifiedTime = $time;
        return $this;
    }

    /**
     * Set Twitter card type.
     */
    public function setTwitterCard(string $card): self
    {
        $this->twitterCard = $card;
        return $this;
    }

    /**
     * Add breadcrumb item.
     */
    public function addBreadcrumb(string $name, ?string $url = null): self
    {
        $this->breadcrumbs[] = [
            'name' => $name,
            'url' => $url,
        ];
        return $this;
    }

    /**
     * Get the title.
     */
    public function getTitle(): string
    {
        return $this->title ?: "{$this->siteName} - Meseriașul potrivit, direct la tine.";
    }

    /**
     * Get the description.
     */
    public function getDescription(): string
    {
        return $this->description ?: $this->defaultDescription;
    }

    /**
     * Get the canonical URL.
     */
    public function getCanonical(): string
    {
        if ($this->canonicalUrl) {
            return $this->canonicalUrl;
        }

        // Generate canonical from current URL without query string
        $url = Request::url();
        
        // Remove trailing slash except for homepage
        if ($url !== url('/') && Str::endsWith($url, '/')) {
            $url = rtrim($url, '/');
        }

        return $url;
    }

    /**
     * Get the OG image with full URL.
     */
    public function getImage(): string
    {
        $image = $this->ogImage ?: $this->defaultImage;
        
        if (!Str::startsWith($image, ['http://', 'https://'])) {
            $image = url($image);
        }

        return $image;
    }

    /**
     * Render all meta tags as HTML.
     */
    public function render(): string
    {
        $html = [];

        // Basic Meta Tags
        $html[] = sprintf('<meta name="description" content="%s">', e($this->getDescription()));
        
        if ($this->keywords) {
            $html[] = sprintf('<meta name="keywords" content="%s">', e($this->keywords));
        }

        // Canonical URL
        $html[] = sprintf('<link rel="canonical" href="%s">', e($this->getCanonical()));

        // Open Graph Tags
        $html[] = sprintf('<meta property="og:site_name" content="%s">', e($this->siteName));
        $html[] = sprintf('<meta property="og:locale" content="%s">', $this->locale);
        $html[] = sprintf('<meta property="og:type" content="%s">', $this->ogType);
        $html[] = sprintf('<meta property="og:title" content="%s">', e($this->getTitle()));
        $html[] = sprintf('<meta property="og:description" content="%s">', e($this->getDescription()));
        $html[] = sprintf('<meta property="og:url" content="%s">', e($this->getCanonical()));
        $html[] = sprintf('<meta property="og:image" content="%s">', e($this->getImage()));
        $html[] = '<meta property="og:image:width" content="1200">';
        $html[] = '<meta property="og:image:height" content="630">';

        // Article specific OG tags
        if ($this->ogType === 'article') {
            if ($this->publishedTime) {
                $html[] = sprintf('<meta property="article:published_time" content="%s">', $this->publishedTime);
            }
            if ($this->modifiedTime) {
                $html[] = sprintf('<meta property="article:modified_time" content="%s">', $this->modifiedTime);
            }
            if ($this->author) {
                $html[] = sprintf('<meta property="article:author" content="%s">', e($this->author));
            }
        }

        // Twitter Card Tags
        $html[] = sprintf('<meta name="twitter:card" content="%s">', $this->twitterCard);
        $html[] = sprintf('<meta name="twitter:site" content="%s">', $this->twitterHandle);
        $html[] = sprintf('<meta name="twitter:title" content="%s">', e($this->getTitle()));
        $html[] = sprintf('<meta name="twitter:description" content="%s">', e($this->getDescription()));
        $html[] = sprintf('<meta name="twitter:image" content="%s">', e($this->getImage()));

        // Author
        if ($this->author) {
            $html[] = sprintf('<meta name="author" content="%s">', e($this->author));
        }

        // Robots
        $html[] = '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">';

        return implode("\n    ", $html);
    }

    /**
     * Configure SEO for craftsman profile page.
     */
    public function forCraftsman($craftsman): self
    {
        $title = $craftsman->name;
        if ($craftsman->specialization) {
            $title .= " - {$craftsman->specialization}";
        }
        if ($craftsman->location) {
            $title .= " în {$craftsman->location->name}";
        }

        $description = $craftsman->short_bio ?? $craftsman->description ?? '';
        if (empty($description)) {
            $description = "Contactează pe {$craftsman->name}, meseriaș profesionist";
            if ($craftsman->specialization) {
                $description .= " specializat în {$craftsman->specialization}";
            }
            if ($craftsman->location) {
                $description .= " în {$craftsman->location->name}";
            }
            $description .= ". Vezi portofoliu, recenzii și prețuri.";
        }

        $this->setTitle($title)
             ->setDescription($description)
             ->setOgType('profile')
             ->setCanonical(route('craftsman.show', $craftsman->slug))
             ->addBreadcrumb('Acasă', route('home'))
             ->addBreadcrumb('Meseriași', route('home'))
             ->addBreadcrumb($craftsman->name);

        // Set profile image
        if ($craftsman->profile_photo_path) {
            $this->setImage(asset('storage/' . $craftsman->profile_photo_path));
        }

        // Keywords
        $keywords = [$craftsman->name];
        if ($craftsman->specialization) {
            $keywords[] = $craftsman->specialization;
        }
        if ($craftsman->category) {
            $keywords[] = $craftsman->category->name;
        }
        if ($craftsman->location) {
            $keywords[] = $craftsman->location->name;
        }
        $keywords = array_merge($keywords, ['meseriaș', 'servicii', 'reparații', 'Omul Potrivit']);
        $this->setKeywords($keywords);

        return $this;
    }

    /**
     * Configure SEO for article page.
     */
    public function forArticle($article): self
    {
        $this->setTitle($article->title)
             ->setDescription($article->excerpt ?? Str::limit(strip_tags($article->content), 160))
             ->setOgType('article')
             ->setCanonical(route('articole.show', $article->slug))
             ->addBreadcrumb('Acasă', route('home'))
             ->addBreadcrumb('Articole', route('articole'))
             ->addBreadcrumb($article->title);

        if ($article->featured_image) {
            $this->setImage(asset('storage/' . $article->featured_image));
        }

        if ($article->created_at) {
            $this->setPublishedTime($article->created_at->toIso8601String());
        }
        if ($article->updated_at) {
            $this->setModifiedTime($article->updated_at->toIso8601String());
        }

        if ($article->author) {
            $this->setAuthor($article->author->name ?? 'Omul Potrivit');
        }

        $keywords = ['articol', 'sfaturi', 'ghid'];
        if ($article->category) {
            $keywords[] = $article->category;
        }
        $keywords[] = 'Omul Potrivit';
        $this->setKeywords($keywords);

        return $this;
    }

    /**
     * Configure SEO for category page.
     */
    public function forCategory($category, $location = null): self
    {
        $title = "Meseriași {$category->name}";
        $description = "Găsește cei mai buni meseriași din categoria {$category->name}";
        
        if ($location) {
            $title .= " în {$location->name}";
            $description .= " în {$location->name}";
        }
        $description .= ". Verifică recenzii, portofolii și solicită oferte gratuit pe Omul Potrivit.";

        $this->setTitle($title)
             ->setDescription($description)
             ->setKeywords([$category->name, 'meseriași', 'servicii', $location?->name ?? 'România', 'Omul Potrivit'])
             ->addBreadcrumb('Acasă', route('home'))
             ->addBreadcrumb('Categorii', route('home') . '#categories')
             ->addBreadcrumb($category->name);

        if ($category->image) {
            $this->setImage(asset('storage/' . $category->image));
        }

        return $this;
    }

    /**
     * Configure SEO for service page.
     */
    public function forService($service): self
    {
        $title = $service->name;
        if ($service->user) {
            $title .= " - {$service->user->name}";
        }

        $description = $service->description ?? "Serviciu profesional: {$service->name}";
        if ($service->price) {
            $description .= ". Preț de la {$service->price} lei.";
        }

        $this->setTitle($title)
             ->setDescription($description)
             ->setOgType('product')
             ->setKeywords([$service->name, 'serviciu', 'meseriaș', 'Omul Potrivit']);

        return $this;
    }

    /**
     * Configure SEO for Q&A page.
     */
    public function forQuestion($question): self
    {
        $this->setTitle($question->title ?? $question->question)
             ->setDescription(Str::limit(strip_tags($question->content ?? $question->question), 160))
             ->addBreadcrumb('Acasă', route('home'))
             ->addBreadcrumb('Întrebări', route('intrebari'))
             ->addBreadcrumb(Str::limit($question->title ?? $question->question, 50));

        $this->setKeywords(['întrebare', 'răspuns', 'ajutor', 'sfat', 'Omul Potrivit']);

        return $this;
    }

    /**
     * Configure SEO for static pages.
     */
    public function forStaticPage(string $title, string $description, array $keywords = []): self
    {
        $this->setTitle($title)
             ->setDescription($description)
             ->setKeywords(array_merge($keywords, ['Omul Potrivit', 'meseriași']));

        return $this;
    }

    /**
     * Get breadcrumbs for structured data.
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * Reset all values for new page.
     */
    public function reset(): self
    {
        $this->title = '';
        $this->description = '';
        $this->keywords = '';
        $this->canonicalUrl = '';
        $this->ogType = 'website';
        $this->ogImage = '';
        $this->author = null;
        $this->publishedTime = null;
        $this->modifiedTime = null;
        $this->breadcrumbs = [];

        return $this;
    }
}
