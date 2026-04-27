<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Electrician',
                'slug' => 'electrician',
                'description' => 'Specialiști în instalații electrice, reparații, modernizări și mentenanță electrică pentru locuințe și spații comerciale.',
                'icon' => 'bolt',
                'order' => 1,
                'is_active' => true,
                'meta_title' => 'Electricieni Profesioniști - Instalații și Reparații Electrice',
                'meta_description' => 'Găsește electricieni verificați și profesioniști pentru orice tip de lucrare electrică. Prețuri corecte, recenzii reale.',
                'meta_keywords' => 'electrician, instalații electrice, reparații electrice, bucuresti, cluj, iasi'
            ],
            [
                'name' => 'Instalator',
                'slug' => 'instalator',
                'description' => 'Profesioniști în instalații sanitare, termice, gaze. Montaj, reparații și întreținere sistem de încălzire și canalizare.',
                'icon' => 'droplet',
                'order' => 2,
                'is_active' => true,
                'meta_title' => 'Instalatori Sanitari și Termici - Reparații Rapide',
                'meta_description' => 'Instalatori profesioniști pentru instalații sanitare, termice și gaze. Intervenții rapide, garanție la lucrări.',
                'meta_keywords' => 'instalator, instalații sanitare, instalații termice, centrale termice, bucuresti'
            ],
            [
                'name' => 'Tamplar',
                'slug' => 'tamplar',
                'description' => 'Meșteri tâmplari pentru mobilier la comandă, reparații lemn, montaj uși și ferestre, parchet și finisaje din lemn.',
                'icon' => 'hammer',
                'order' => 3,
                'is_active' => true,
                'meta_title' => 'Tâmplari Profesioniști - Mobilier și Lucrări din Lemn',
                'meta_description' => 'Tâmplari cu experiență pentru mobilier personalizat, montaj uși, ferestre, parchet. Lucrări de calitate, prețuri competitive.',
                'meta_keywords' => 'tamplar, mobilier la comanda, montaj usi, montaj ferestre, parchet'
            ],
            [
                'name' => 'Zugrav',
                'slug' => 'zugrav',
                'description' => 'Zugravi și vopsitori profesioniști pentru zugrăveli interioare și exterioare, finisaje decorative, renovări complete.',
                'icon' => 'paint-brush',
                'order' => 4,
                'is_active' => true,
                'meta_title' => 'Zugravi Profesioniști - Zugrăveli și Vopsitorii',
                'meta_description' => 'Zugravi cu experiență pentru zugrăveli interioare și exterioare. Finisaje impecabile, prețuri transparente.',
                'meta_keywords' => 'zugrav, vopsitor, zugraveli, vopsitorii, renovari'
            ],
            [
                'name' => 'Zidar',
                'slug' => 'zidar',
                'description' => 'Zidari profesioniști pentru construcții noi, renovări, zidărie, tencuieli, finisaje și reparații deonstrucții.',
                'icon' => 'brick-wall',
                'order' => 5,
                'is_active' => true,
                'meta_title' => 'Zidari Profesioniști - Construcții și Renovări',
                'meta_description' => 'Zidari cu experiență pentru construcții, renovări, zidărie, tencuieli. Lucrări de calitate garantată.',
                'meta_keywords' => 'zidar, constructii, zidarie, tencuieli, renovari'
            ],
            [
                'name' => 'Faiantar',
                'slug' => 'faiantar',
                'description' => 'Specialiști în montaj faianță, gresie, mozaic. Finisaje băi, bucătării, terase și spații comerciale.',
                'icon' => 'grid',
                'order' => 6,
                'is_active' => true,
                'meta_title' => 'Faiantari Profesioniști - Montaj Faianță și Gresie',
                'meta_description' => 'Faiantari cu experiență pentru montaj faianță, gresie, mozaic. Finisaje de calitate pentru băi și bucătării.',
                'meta_keywords' => 'faiantar, montaj faianta, montaj gresie, bai, bucatarii'
            ],
            [
                'name' => 'Frigotehnist',
                'slug' => 'frigotehnist',
                'description' => 'Tehnicieni specializați în reparații și instalare aparate frigorifice, aer condiționat, sisteme de climatizare.',
                'icon' => 'snowflake',
                'order' => 7,
                'is_active' => true,
                'meta_title' => 'Frigotehniști - Reparații AC și Aparate Frigorifice',
                'meta_description' => 'Frigotehniști profesioniști pentru instalare și reparații aer condiționat, frigidere, congelatoare. Intervenții rapide.',
                'meta_keywords' => 'frigotehnist, aer conditionat, reparatii ac, instalare ac, climatizare'
            ],
            [
                'name' => 'Mecanic Auto',
                'slug' => 'mecanic-auto',
                'description' => 'Mecanici auto pentru service mobil, diagnoză, reparații, întreținere și depanări la domiciliu.',
                'icon' => 'car',
                'order' => 8,
                'is_active' => true,
                'meta_title' => 'Mecanici Auto - Service Mobil la Domiciliu',
                'meta_description' => 'Mecanici auto profesioniști pentru reparații și service mobil. Intervenții rapide la domiciliu.',
                'meta_keywords' => 'mecanic auto, service mobil, reparatii auto, diagnoza auto'
            ],
            [
                'name' => 'Gradinar',
                'slug' => 'gradinar',
                'description' => 'Grădinari profesioniști pentru amenajări grădini, întreținere spații verzi, tăiat iarbă, plantări.',
                'icon' => 'leaf',
                'order' => 9,
                'is_active' => true,
                'meta_title' => 'Grădinari Profesioniști - Amenajare și Întreținere Grădini',
                'meta_description' => 'Grădinari cu experiență pentru amenajare și întreținere grădini, spații verzi, gazon, plante.',
                'meta_keywords' => 'gradinar, amenajare gradini, intretinere gradini, taiat iarba'
            ],
            [
                'name' => 'Curatenie',
                'slug' => 'curatenie',
                'description' => 'Servicii profesionale de curățenie pentru locuințe, birouri, curățenie după renovări, curățenie generală.',
                'icon' => 'spray-can',
                'order' => 10,
                'is_active' => true,
                'meta_title' => 'Servicii Curățenie Profesională - Locuințe și Birouri',
                'meta_description' => 'Servicii profesionale de curățenie pentru locuințe și birouri. Curățenie generală, după renovări, deep cleaning.',
                'meta_keywords' => 'curatenie, servicii curatenie, curatenie profesionala, curatenie dupa renovare'
            ]
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }

        // Adăugare categorii noi pentru întreținere imobile și mentenanță
        Category::updateOrCreate([
            'slug' => 'intretinere-imobile'
        ], [
            'name' => 'Intretinere imobile',
            'slug' => 'intretinere-imobile',
            'description' => 'Servicii de intretinere generala pentru blocuri, case, birouri si alte imobile: curatenie, verificari tehnice, reparatii minore.',
            'icon' => 'building',
            'order' => 11,
            'is_active' => true,
            'meta_title' => 'Intretinere Imobile - Servicii Profesionale',
            'meta_description' => 'Gaseste specialisti pentru intretinere imobile: curatenie, verificari, reparatii, mentenanta.',
            'meta_keywords' => 'intretinere imobile, mentenanta, curatenie, verificari, reparatii'
        ]);
        Category::updateOrCreate([
            'slug' => 'mentenanta'
        ], [
            'name' => 'Mentenanta',
            'slug' => 'mentenanta',
            'description' => 'Servicii de mentenanta tehnica pentru instalatii, echipamente, sisteme de securitate si alte componente ale imobilelor.',
            'icon' => 'tools',
            'order' => 12,
            'is_active' => true,
            'meta_title' => 'Mentenanta Tehnica - Imobile si Echipamente',
            'meta_description' => 'Apeleaza la specialisti pentru mentenanta instalatii, echipamente, sisteme tehnice si reparatii.',
            'meta_keywords' => 'mentenanta, instalatii, echipamente, reparatii, tehnic'
        ]);
    }
}
