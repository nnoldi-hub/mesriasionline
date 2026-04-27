<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SchemaOrg extends Component
{
    public array $data;
    public string $type;

    /**
     * Create a new component instance.
     */
    public function __construct(string $type, array $data = [])
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $schema = $this->generateSchema();
        
        return view('components.schema-org', ['schema' => $schema]);
    }

    /**
     * Generate schema based on type.
     */
    private function generateSchema(): array
    {
        return match($this->type) {
            'LocalBusiness' => $this->localBusinessSchema(),
            'Service' => $this->serviceSchema(),
            'Person' => $this->personSchema(),
            'Article' => $this->articleSchema(),
            'FAQPage' => $this->faqSchema(),
            'Review' => $this->reviewSchema(),
            'BreadcrumbList' => $this->breadcrumbSchema(),
            default => $this->data,
        };
    }

    /**
     * Schema for craftsman as LocalBusiness.
     */
    private function localBusinessSchema(): array
    {
        $craftsman = $this->data['craftsman'] ?? null;
        
        if (!$craftsman) {
            return [];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            '@id' => route('craftsman.show', $craftsman->slug),
            'name' => $craftsman->company_name ?? $craftsman->name,
            'description' => $craftsman->description,
            'url' => route('craftsman.show', $craftsman->slug),
        ];

        if ($craftsman->phone) {
            $schema['telephone'] = $craftsman->phone;
        }

        if ($craftsman->category) {
            $schema['category'] = $craftsman->category->name;
        }

        if ($craftsman->location) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'addressLocality' => $craftsman->location->name,
                'addressCountry' => 'RO',
            ];
        }

        if ($craftsman->latitude && $craftsman->longitude) {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $craftsman->latitude,
                'longitude' => $craftsman->longitude,
            ];
        }

        // Aggregate rating
        if ($craftsman->reviews_count > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round($craftsman->average_rating, 1),
                'reviewCount' => $craftsman->reviews_count,
                'bestRating' => 5,
                'worstRating' => 1,
            ];
        }

        // Opening hours
        if ($craftsman->weekly_schedule) {
            $schema['openingHoursSpecification'] = [];
            $dayMap = [
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
            ];
            
            foreach ($craftsman->weekly_schedule as $day => $schedule) {
                if ($schedule['active'] ?? false) {
                    $schema['openingHoursSpecification'][] = [
                        '@type' => 'OpeningHoursSpecification',
                        'dayOfWeek' => $dayMap[$day] ?? ucfirst($day),
                        'opens' => $schedule['start'],
                        'closes' => $schedule['end'],
                    ];
                }
            }
        }

        return $schema;
    }

    /**
     * Schema for a service.
     */
    private function serviceSchema(): array
    {
        $service = $this->data['service'] ?? null;
        
        if (!$service) {
            return [];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->name,
            'description' => $service->description,
        ];

        if ($service->price) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $service->price,
                'priceCurrency' => 'RON',
            ];
        }

        return $schema;
    }

    /**
     * Schema for Person (craftsman).
     */
    private function personSchema(): array
    {
        $person = $this->data['person'] ?? null;
        
        if (!$person) {
            return [];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $person->name,
            'jobTitle' => $person->specialization ?? ($person->category?->name ?? 'Meseriaș'),
            'url' => route('craftsman.show', $person->slug),
        ];
    }

    /**
     * Schema for Article.
     */
    private function articleSchema(): array
    {
        $article = $this->data['article'] ?? null;
        
        if (!$article) {
            return [];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title,
            'description' => $article->excerpt ?? substr(strip_tags($article->content), 0, 160),
            'datePublished' => $article->published_at?->toIso8601String(),
            'dateModified' => $article->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => url('/'),
            ],
            'mainEntityOfPage' => route('articole.show', $article->slug),
        ];
    }

    /**
     * Schema for FAQ page.
     */
    private function faqSchema(): array
    {
        $questions = $this->data['questions'] ?? [];
        
        if (empty($questions)) {
            return [];
        }

        $mainEntity = [];
        foreach ($questions as $question) {
            if ($question->answer) {
                $mainEntity[] = [
                    '@type' => 'Question',
                    'name' => $question->question,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $question->answer,
                    ],
                ];
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }

    /**
     * Schema for Review.
     */
    private function reviewSchema(): array
    {
        $review = $this->data['review'] ?? null;
        
        if (!$review) {
            return [];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $review->rating,
                'bestRating' => 5,
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $review->name,
            ],
            'reviewBody' => $review->comment,
            'datePublished' => $review->created_at->toIso8601String(),
        ];
    }

    /**
     * Schema for Breadcrumbs.
     */
    private function breadcrumbSchema(): array
    {
        $items = $this->data['items'] ?? [];
        
        if (empty($items)) {
            return [];
        }

        $itemListElement = [];
        foreach ($items as $index => $item) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];
    }
}
