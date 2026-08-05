<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paramètres spécifiques au Cameroun
    |--------------------------------------------------------------------------
    |
    | Configuration adaptée au contexte camerounais pour le système de 
    | gestion de restaurants.
    |
    */

    'country_code' => 'CM',
    'country_name' => 'Cameroun',
    'currency' => 'XAF',
    'currency_symbol' => 'FCFA',
    'currency_name' => 'Franc CFA',
    'phone_code' => '+237',
    'phone_length' => 9,

    /*
    |--------------------------------------------------------------------------
    | Régions du Cameroun (Régions/Provinces)
    |--------------------------------------------------------------------------
    */
    'regions' => [
        'adamaoua' => 'Adamaoua',
        'centre' => 'Centre',
        'est' => 'Est',
        'extreme_nord' => 'Extrême-Nord',
        'littoral' => 'Littoral',
        'nord' => 'Nord',
        'nord_ouest' => 'Nord-Ouest',
        'ouest' => 'Ouest',
        'sud' => 'Sud',
        'sud_ouest' => 'Sud-Ouest',
    ],

    /*
    |--------------------------------------------------------------------------
    | Villes principales du Cameroun
    |--------------------------------------------------------------------------
    */
    'cities' => [
        'adamaoua' => ['Ngaoundéré', 'Meiganga', 'Tibati'],
        'centre' => ['Yaoundé', 'Nkongsamba', 'Bafoussam'],
        'est' => ['Bertoua', 'Batouri', 'Abong-Mbang'],
        'extreme_nord' => ['Maroua', 'Garoua', 'Kaele'],
        'littoral' => ['Douala', 'Limbe', 'Buea', 'Kribi', 'Edea'],
        'nord' => ['Garoua', 'Ngaoundéré', 'Maroua'],
        'nord_ouest' => ['Bamenda', 'Kumba', 'Kunbo'],
        'ouest' => ['Bafoussam', 'Dschang', 'Bangoua'],
        'sud' => ['Ebolowa', 'Sangmelima', 'Kribi'],
        'sud_ouest' => ['Limbe', 'Buea', 'Kumba'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Modes de paiement locaux au Cameroun
    |--------------------------------------------------------------------------
    */
    'payment_methods' => [
        'cash' => [
            'label' => 'Espèces',
            'code' => 'CASH',
            'enabled' => true,
        ],
        'orange_money' => [
            'label' => 'Orange Money',
            'code' => 'ORANGE_MONEY',
            'enabled' => true,
            'provider' => 'orange',
        ],
        'mtn_money' => [
            'label' => 'MTN Money',
            'code' => 'MTN_MONEY',
            'enabled' => true,
            'provider' => 'mtn',
        ],
        'moov_money' => [
            'label' => 'Moov Money',
            'code' => 'MOOV_MONEY',
            'enabled' => true,
            'provider' => 'moov',
        ],
        'nexttel' => [
            'label' => 'NextTel Money',
            'code' => 'NEXTTEL',
            'enabled' => true,
            'provider' => 'nexttel',
        ],
        'bank_transfer' => [
            'label' => 'Virement bancaire',
            'code' => 'BANK_TRANSFER',
            'enabled' => true,
            'provider' => 'bank',
        ],
        'card' => [
            'label' => 'Carte bancaire',
            'code' => 'CARD',
            'enabled' => true,
            'provider' => 'card',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Opérateurs de téléphonie mobile
    |--------------------------------------------------------------------------
    */
    'mobile_operators' => [
        // MTN Cameroun : blocs 650-654 + 67X + 68X
        'mtn' => [
            'name' => 'MTN Cameroun',
            'prefixes' => [
                '650', '651', '652', '653', '654',
                '670', '671', '672', '673', '674', '675', '676', '677', '678', '679',
                '680', '681', '682', '683', '684', '685', '686', '687', '688', '689',
            ],
            'code' => 'MTN',
        ],
        // Orange Cameroun : blocs 655-659 + 69X
        'orange' => [
            'name' => 'Orange Cameroun',
            'prefixes' => [
                '655', '656', '657', '658', '659',
                '690', '691', '692', '693', '694', '695', '696', '697', '698', '699',
            ],
            'code' => 'ORANGE',
        ],
        // Nexttel (ex-Viettel) : bloc 66X
        'nexttel' => [
            'name' => 'NextTel',
            'prefixes' => ['660', '661', '662', '663', '664', '665', '666', '667', '668', '669'],
            'code' => 'NEXTTEL',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Horaires typiques pour restaurants camerounais
    |--------------------------------------------------------------------------
    */
    'default_operating_hours' => [
        'monday' => ['open' => '06:00', 'close' => '23:00'],
        'tuesday' => ['open' => '06:00', 'close' => '23:00'],
        'wednesday' => ['open' => '06:00', 'close' => '23:00'],
        'thursday' => ['open' => '06:00', 'close' => '23:00'],
        'friday' => ['open' => '06:00', 'close' => '00:00'],
        'saturday' => ['open' => '06:00', 'close' => '00:00'],
        'sunday' => ['open' => '06:00', 'close' => '23:00'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Jours fériés au Cameroun
    |--------------------------------------------------------------------------
    */
    'public_holidays' => [
        '2026-01-01' => 'Jour de l\'an',
        '2026-02-11' => 'Fête de la jeunesse',
        '2026-04-10' => 'Vendredi saint',
        '2026-04-13' => 'Lundi de Pâques',
        '2026-05-01' => 'Fête du travail',
        '2026-05-21' => 'Fête nationale',
        '2026-08-15' => 'Assomption',
        '2026-11-01' => 'Toussaint',
        '2026-12-25' => 'Noël',
    ],

    /*
    |--------------------------------------------------------------------------
    | Types de cuisine populaires au Cameroun
    |--------------------------------------------------------------------------
    */
    'cuisine_types' => [
        'camerounaise' => 'Cuisine camerounaise',
        'grillades' => 'Grillades',
        'street_food' => 'Street food',
        'specialites' => 'Spécialités locales',
        'africaine' => 'Cuisine africaine',
        'internationale' => 'Cuisine internationale',
        'chinoise' => 'Cuisine chinoise',
        'italienne' => 'Cuisine italienne',
        'francaise' => 'Cuisine française',
        'libanaise' => 'Cuisine libanaise',
    ],

    /*
    |--------------------------------------------------------------------------
    | Unités de mesure locales
    |--------------------------------------------------------------------------
    */
    'units' => [
        'assiette' => 'Assiette',
        'portion' => 'Portion',
        'kg' => 'Kilogramme',
        'l' => 'Litre',
        'tasse' => 'Tasse',
        'verre' => 'Verre',
        'bouteille' => 'Bouteille',
    ],

    /*
    |--------------------------------------------------------------------------
    | Délais de livraison par zone
    |--------------------------------------------------------------------------
    */
    'delivery_zones' => [
        'centre_ville' => [
            'name' => 'Centre-ville',
            'min_time' => 15,
            'max_time' => 30,
            'fee' => 500,
        ],
        'periurbain' => [
            'name' => 'Zone périurbaine',
            'min_time' => 30,
            'max_time' => 60,
            'fee' => 1000,
        ],
        'loin' => [
            'name' => 'Zone éloignée',
            'min_time' => 60,
            'max_time' => 120,
            'fee' => 1500,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Formats d'affichage
    |--------------------------------------------------------------------------
    */
    'formats' => [
        'date' => 'd/m/Y',
        'datetime' => 'd/m/Y H:i',
        'time' => 'H:i',
        'phone' => '+237 XXX XXX XXX',
    ],
];
