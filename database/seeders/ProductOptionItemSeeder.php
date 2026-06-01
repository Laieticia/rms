<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductOptionItem;
use App\Models\ProductOption;

class ProductOptionItemSeeder extends Seeder
{
    public function run(): void
    {
        $productOptions = ProductOption::all();
        
        $optionItems = [
            'Suppléments' => [
                ['name' => 'Fromage supplémentaire', 'price' => 1.50],
                ['name' => 'Bacon', 'price' => 2.00],
                ['name' => 'Oignons caramélisés', 'price' => 1.00],
                ['name' => 'Champignons', 'price' => 1.50],
                ['name' => 'Avocat', 'price' => 2.50],
            ],
            'Sauce' => [
                ['name' => 'Sauce algérienne', 'price' => 0.50],
                ['name' => 'Sauce blanche', 'price' => 0.50],
                ['name' => 'Sauce barbecue', 'price' => 0.50],
                ['name' => 'Sauce samouraï', 'price' => 0.50],
                ['name' => 'Ketchup', 'price' => 0],
            ],
            'Niveau d\'épices' => [
                ['name' => 'Douce', 'price' => 0],
                ['name' => 'Moyenne', 'price' => 0],
                ['name' => 'Fort', 'price' => 0],
                ['name' => 'Très fort', 'price' => 0],
            ],
            'Accompagnement' => [
                ['name' => 'Frites', 'price' => 2.00],
                ['name' => 'Purée', 'price' => 2.00],
                ['name' => 'Légumes grillés', 'price' => 2.50],
                ['name' => 'Riz', 'price' => 2.00],
                ['name' => 'Salade', 'price' => 1.50],
            ],
        ];
        
        foreach ($productOptions as $option) {
            // Trouver les items correspondant au nom de l'option
            $items = [];
            foreach ($optionItems as $key => $itemList) {
                if (str_contains($option->name, $key) || str_contains($key, $option->name)) {
                    $items = $itemList;
                    break;
                }
            }
            
            // Si aucun match trouvé, utiliser les suppléments par défaut
            if (empty($items)) {
                $items = $optionItems['Suppléments'];
            }
            
            // Ajouter 2-4 items par option
            $nbItems = min(rand(2, 4), count($items));
            for ($i = 0; $i < $nbItems; $i++) {
                $item = $items[$i];
                ProductOptionItem::create([
                    'product_option_id' => $option->id,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'sort_order' => $i,
                    'is_available' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}