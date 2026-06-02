<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'user_id' => 11,
                'label' => 'Domicile',
                'street_address' => '123 Rue de la Paix',
                'apartment' => 'Apt 4B',
                'city' => 'Paris',
                'postal_code' => '75001',
                'country' => 'FR',
                'latitude' => 48.8650,
                'longitude' => 2.3310,
                'instructions' => 'Code porte : 1234, 4ème étage',
                'is_default' => true,
            ],
            [
                'user_id' => 11,
                'label' => 'Travail',
                'street_address' => '45 Avenue des Ternes',
                'apartment' => null,
                'city' => 'Paris',
                'postal_code' => '75017',
                'country' => 'FR',
                'latitude' => 48.8790,
                'longitude' => 2.2950,
                'instructions' => 'Sonner au service courrier',
                'is_default' => false,
            ],
            [
                'user_id' => 12,
                'label' => 'Domicile',
                'street_address' => '67 Rue de la Liberté',
                'city' => 'Lyon',
                'postal_code' => '69003',
                'country' => 'FR',
                'latitude' => 45.7570,
                'longitude' => 4.8490,
                'instructions' => 'Bâtiment B, interphone Martin',
                'is_default' => true,
            ],
            [
                'user_id' => 13,
                'label' => 'Maison',
                'street_address' => '89 Boulevard Saint-Michel',
                'apartment' => 'RDC',
                'city' => 'Paris',
                'postal_code' => '75005',
                'country' => 'FR',
                'latitude' => 48.8490,
                'longitude' => 2.3400,
                'instructions' => 'Porte bleue à gauche',
                'is_default' => true,
            ],
            [
                'user_id' => 14,
                'label' => 'Domicile',
                'street_address' => '234 Avenue du Prado',
                'city' => 'Marseille',
                'postal_code' => '13008',
                'country' => 'FR',
                'latitude' => 43.2780,
                'longitude' => 5.3830,
                'instructions' => null,
                'is_default' => true,
            ],
            [
                'user_id' => 14,
                'label' => 'Bureau',
                'street_address' => '12 Rue de la République',
                'apartment' => 'Étage 3',
                'city' => 'Marseille',
                'postal_code' => '13001',
                'country' => 'FR',
                'latitude' => 43.2965,
                'longitude' => 5.3698,
                'instructions' => 'Digicode : 5678',
                'is_default' => false,
            ],
            [
                'user_id' => 15,
                'label' => 'Domicile',
                'street_address' => '56 Rue du Commerce',
                'city' => 'Lyon',
                'postal_code' => '69002',
                'country' => 'FR',
                'latitude' => 45.7580,
                'longitude' => 4.8350,
                'instructions' => 'Livrer au 2ème étage',
                'is_default' => true,
            ],
            [
                'user_id' => 16,
                'label' => 'Domicile',
                'street_address' => '78 Rue de Rivoli',
                'apartment' => 'Apt 12C',
                'city' => 'Paris',
                'postal_code' => '75004',
                'country' => 'FR',
                'latitude' => 48.8550,
                'longitude' => 2.3550,
                'instructions' => 'Ascenseur en panne, monter à pied',
                'is_default' => true,
            ],
            [
                'user_id' => 17,
                'label' => 'Domicile',
                'street_address' => '145 Cours Gambetta',
                'city' => 'Lyon',
                'postal_code' => '69003',
                'country' => 'FR',
                'latitude' => 45.7600,
                'longitude' => 4.8520,
                'instructions' => null,
                'is_default' => true,
            ],
            [
                'user_id' => 18,
                'label' => 'Domicile',
                'street_address' => '290 Rue Paradis',
                'apartment' => 'Bât A',
                'city' => 'Marseille',
                'postal_code' => '13006',
                'country' => 'FR',
                'latitude' => 43.2890,
                'longitude' => 5.3790,
                'instructions' => 'Interphone : Dupont',
                'is_default' => true,
            ],
        ];

        foreach ($addresses as $addressData) {
            Address::create($addressData);
        }
    }
}