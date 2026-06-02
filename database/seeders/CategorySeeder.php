<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Restaurant 1 : Le Bistro Parisien
        $categoriesR1 = [
            [
                'restaurant_id' => 1,
                'name' => 'Entrées',
                'description' => 'Entrées fraîches et savoureuses pour commencer votre repas',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Plats Principaux',
                'description' => 'Plats traditionnels français préparés avec soin',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Desserts',
                'description' => 'Desserts maison pour finir en beauté',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Vins',
                'description' => 'Sélection de vins français',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Menu Enfant',
                'description' => 'Plats adaptés aux plus petits',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        // Restaurant 2 : Pizza Roma
        $categoriesR2 = [
            [
                'restaurant_id' => 2,
                'name' => 'Pizzas Classiques',
                'description' => 'Les incontournables de la pizza italienne',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 2,
                'name' => 'Pizzas Spéciales',
                'description' => 'Créations originales du chef',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 2,
                'name' => 'Pâtes Fraîches',
                'description' => 'Pâtes faites maison quotidiennement',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 2,
                'name' => 'Antipasti',
                'description' => 'Entrées italiennes traditionnelles',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 2,
                'name' => 'Desserts Italiens',
                'description' => 'Dolci traditionnels',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 2,
                'name' => 'Boissons',
                'description' => 'Boissons fraîches et vins italiens',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        // Restaurant 3 : Sushi Master
        $categoriesR3 = [
            [
                'restaurant_id' => 3,
                'name' => 'Sushis',
                'description' => 'Sushis traditionnels préparés par nos maîtres sushi',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 3,
                'name' => 'Makis',
                'description' => 'Rouleaux de riz et algue nori',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 3,
                'name' => 'Plats Chauds',
                'description' => 'Ramen, udon et autres plats chauds japonais',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 3,
                'name' => 'Entrées Japonaises',
                'description' => 'Edamame, gyoza et autres délices',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 3,
                'name' => 'Plateaux',
                'description' => 'Plateaux de sushi pour partager',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        // Restaurant 4 : Burger House
        $categoriesR4 = [
            [
                'restaurant_id' => 4,
                'name' => 'Burgers Classiques',
                'description' => 'Nos burgers signatures',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 4,
                'name' => 'Burgers Gourmets',
                'description' => 'Créations originales avec des ingrédients premium',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 4,
                'name' => 'Accompagnements',
                'description' => 'Frites, onion rings et plus',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 4,
                'name' => 'Salades',
                'description' => 'Salades fraîches et copieuses',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 4,
                'name' => 'Desserts',
                'description' => 'Milkshakes et douceurs sucrées',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 4,
                'name' => 'Menus',
                'description' => 'Formules burger + accompagnement + boisson',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        // Restaurant 5 : Le Jardin Vert
        $categoriesR5 = [
            [
                'restaurant_id' => 5,
                'name' => 'Bowls',
                'description' => 'Bols complets et équilibrés',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 5,
                'name' => 'Salades',
                'description' => 'Salades fraîches et créatives',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 5,
                'name' => 'Soupes',
                'description' => 'Soupes maison de saison',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 5,
                'name' => 'Jus & Smoothies',
                'description' => 'Jus pressés et smoothies détox',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 5,
                'name' => 'Desserts Végans',
                'description' => 'Desserts 100% végétaux',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        $allCategories = array_merge(
            $categoriesR1, $categoriesR2, $categoriesR3, 
            $categoriesR4, $categoriesR5
        );

        foreach ($allCategories as $categoryData) {
            Category::create($categoryData);
        }
    }
}