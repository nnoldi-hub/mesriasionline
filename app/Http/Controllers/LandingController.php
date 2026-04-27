?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Landing page per categorie (toate orașele).
     * e.g. /meseriasi/electrician
     */
    public function category(string $categorySlug)
    {
        $category = Category::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $craftsmen = User::where('role', 'specialist')
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->with(['category', 'location', 'reviews'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('is_featured')
            ->orderByDesc('is_verified')
            ->withCount(['subscriptions as has_pro' => fn($q) =>
                $q->where('status', 'active')->whereHas('plan', fn($p) => $p->where('slug', 'pro'))
            ])
            ->limit(30)
            ->get();

        $topLocations = Location::where('is_active', true)
            ->whereHas('users', fn($q) => $q->where('role', 'specialist')
                ->where('is_active', true)
                ->where('category_id', $category->id))
            ->withCount(['users as craftsmen_count' => fn($q) =>
                $q->where('role', 'specialist')->where('is_active', true)->where('category_id', $category->id)
            ])
            ->orderByDesc('craftsmen_count')
            ->limit(8)
            ->get();

        $faqItems = $this->getFaqItems($category);
        $serviceTemplates = $this->getServiceTemplates($category->slug);

        $meta = [
            'title'       => $category->meta_title ?: "{$category->name} — Găsește Profesioniști Verificați | Omul Potrivit",
            'description' => $category->meta_description ?: "Găsește {$category->name} profesioniști în zona ta. Recenzii reale, prețuri transparente. Solicită ofertă gratuită.",
            'keywords'    => $category->meta_keywords ?: strtolower("{$category->name}, meseriasi, Omul Potrivit, romania"),
            'canonical'   => route('landing.category', $categorySlug),
        ];

        return view('landing.category', compact('category', 'craftsmen', 'topLocations', 'faqItems', 'serviceTemplates', 'meta'));
    }

    /**
     * Landing page per categorie + oraș.
     * e.g. /meseriasi/electrician/bucuresti
     */
    public function categoryCity(string $categorySlug, string $locationSlug)
    {
        $category = Category::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $location = Location::where('slug', $locationSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $craftsmen = User::where('role', 'specialist')
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->where('location_id', $location->id)
            ->with(['category', 'location', 'reviews', 'gallery'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('is_featured')
            ->orderByDesc('is_verified')
            ->limit(20)
            ->get();

        $faqItems = $this->getFaqItems($category, $location->city);
        $serviceTemplates = $this->getServiceTemplates($category->slug);

        $cityName = $location->city;
        $meta = [
            'title'       => "{$category->name} {$cityName} — Profesioniști Verificați | Omul Potrivit",
            'description' => "Găsește {$category->name} de încredere în {$cityName}. {$craftsmen->count()} specialiști verificați cu recenzii reale. Solicită ofertă gratuită.",
            'keywords'    => strtolower("{$category->name} {$cityName}, meseriasi {$cityName}, Omul Potrivit"),
            'canonical'   => route('landing.category-city', [$categorySlug, $locationSlug]),
        ];

        // Structured data JSON-LD
        $structuredData = $this->buildLocalBusinessStructuredData($category, $location, $craftsmen);

        return view('landing.category-city', compact(
            'category', 'location', 'craftsmen', 'faqItems', 'serviceTemplates', 'meta', 'structuredData', 'cityName'
        ));
    }

    /**
     * Sitemap XML pentru landing pages.
     */
    public function sitemap()
    {
        $categories = Category::where('is_active', true)->get();
        $locations  = Location::where('is_active', true)->get();

        return response()->view('landing.sitemap', compact('categories', 'locations'))
            ->header('Content-Type', 'application/xml');
    }

    // -----------------------------------------------------------------------

    private function getFaqItems(Category $category, ?string $city = null): array
    {
        $cityStr = $city ? " în {$city}" : '';
        $cat     = $category->name;

        return [
            [
                'question' => "Cât costă un {$cat}?",
                'answer'   => "Prețurile variază în funcție de complexitatea lucrării. Pe Omul Potrivit poți solicita oferte gratuite de la mai mulți {$cat}i și să compari.",
            ],
            [
                'question' => "Cum găsesc un {$cat} bun{$cityStr}?",
                'answer'   => "Caută pe Omul Potrivit{$cityStr}, filtrează după recenzii și verificare, citește părerile altor clienți și solicită ofertă gratuită.",
            ],
            [
                'question' => "Meseriașii sunt verificați?",
                'answer'   => "Da, meseriașii cu badge «Verificat» au trecut prin procesul nostru de verificare a identității și calificărilor.",
            ],
            [
                'question' => "Pot programa o vizită de evaluare?",
                'answer'   => "Da, poți contacta direct meseriașul prin platforma noastră pentru a stabili o vizită de evaluare fără costuri suplimentare.",
            ],
        ];
    }

    private function getServiceTemplates(string $categorySlug): array
    {
        $templates = [
            'electrician' => [
                ['name' => 'Montaj priză / întrerupător',   'price' => 50,  'unit' => 'buc'],
                ['name' => 'Tablou electric complet',        'price' => 800, 'unit' => 'buc'],
                ['name' => 'Instalație electrică cameră',    'price' => 300, 'unit' => 'cameră'],
                ['name' => 'Montaj lampă / candelabru',      'price' => 80,  'unit' => 'buc'],
                ['name' => 'Revizie instalație electrică',   'price' => 200, 'unit' => 'apartament'],
            ],
            'instalator' => [
                ['name' => 'Montaj robinet / baterie',       'price' => 100, 'unit' => 'buc'],
                ['name' => 'Desfundat canalizare',           'price' => 150, 'unit' => 'interventie'],
                ['name' => 'Montaj centrală termică',        'price' => 500, 'unit' => 'buc'],
                ['name' => 'Revizie centrală termică',       'price' => 200, 'unit' => 'buc'],
                ['name' => 'Montaj chiuvetă / WC',           'price' => 150, 'unit' => 'buc'],
            ],
            'zugrav' => [
                ['name' => 'Zugrăveală cameră (vopsea)',     'price' => 400, 'unit' => 'cameră'],
                ['name' => 'Glet + zugrăveală cameră',       'price' => 700, 'unit' => 'cameră'],
                ['name' => 'Tapet cameră',                   'price' => 350, 'unit' => 'cameră'],
                ['name' => 'Zugrăveală apartament 3 camere', 'price' => 1500,'unit' => 'apartament'],
                ['name' => 'Vopsit tâmplărie interioară',    'price' => 80,  'unit' => 'ușă'],
            ],
            'tamplar' => [
                ['name' => 'Montaj ușă interior',            'price' => 150, 'unit' => 'buc'],
                ['name' => 'Montaj parchet laminat',         'price' => 25,  'unit' => 'm²'],
                ['name' => 'Reparație mobilier lemn',        'price' => 100, 'unit' => 'buc'],
                ['name' => 'Confecție dulap la comandă',     'price' => 800, 'unit' => 'buc'],
                ['name' => 'Montaj ferestre PVC',            'price' => 200, 'unit' => 'buc'],
            ],
            'zidar' => [
                ['name' => 'Tencuială manuală',              'price' => 25,  'unit' => 'm²'],
                ['name' => 'Zidărie cărămidă',               'price' => 50,  'unit' => 'm²'],
                ['name' => 'Montaj gresie / faianță',        'price' => 35,  'unit' => 'm²'],
                ['name' => 'Demolare perete neportant',      'price' => 150, 'unit' => 'buc'],
                ['name' => 'Șapă turnată',                   'price' => 20,  'unit' => 'm²'],
            ],
        ];

        return $templates[$categorySlug] ?? [
            ['name' => 'Serviciu consultație',   'price' => 100, 'unit' => 'oră'],
            ['name' => 'Lucrare standard',       'price' => 200, 'unit' => 'buc'],
            ['name' => 'Lucrare complexă',       'price' => 500, 'unit' => 'proiect'],
        ];
    }

    private function buildLocalBusinessStructuredData(Category $category, Location $location, $craftsmen): array
    {
        $items = $craftsmen->take(5)->map(fn($c) => [
            '@type'       => 'LocalBusiness',
            'name'        => $c->name,
            'description' => $c->description ?? $c->specialization,
            'url'         => route('craftsman.show', $c->slug ?? $c->id),
            'address'     => [
                '@type'           => 'PostalAddress',
                'addressLocality' => $location->city,
                'addressCountry'  => 'RO',
            ],
            'aggregateRating' => $c->reviews_count > 0 ? [
                '@type'       => 'AggregateRating',
                'ratingValue' => round($c->reviews_avg_rating, 1),
                'reviewCount' => $c->reviews_count,
            ] : null,
        ])->filter()->values()->toArray();

        return [
            '@context' => 'https://schema.org',
            '@type'    => 'ItemList',
            'name'     => "{$category->name} {$location->city}",
            'itemListElement' => array_map(fn($i, $item) => [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'item'     => $item,
            ], array_keys($items), $items),
        ];
    }
}
