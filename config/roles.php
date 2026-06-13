<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rôles et Permissions pour le système de gestion de restaurant camerounais
    |--------------------------------------------------------------------------
    */

    'roles' => [
        'admin' => [
            'label' => 'Administrateur',
            'description' => 'Accès complet au système',
            'color' => '#dc3545',
            'permissions' => [
                'view_dashboard',
                'view_orders',
                'create_order',
                'edit_order',
                'delete_order',
                'view_menu',
                'create_menu',
                'edit_menu',
                'delete_menu',
                'view_products',
                'create_product',
                'edit_product',
                'delete_product',
                'view_customers',
                'create_customer',
                'edit_customer',
                'delete_customer',
                'view_staff',
                'create_staff',
                'edit_staff',
                'delete_staff',
                'view_payments',
                'create_payment',
                'view_reports',
                'manage_settings',
                'view_inventory',
                'manage_inventory',
                'manage_permissions',
            ],
        ],
        'manager' => [
            'label' => 'Gestionnaire',
            'description' => 'Gestion des opérations quotidiennes',
            'color' => '#007bff',
            'permissions' => [
                'view_dashboard',
                'view_orders',
                'create_order',
                'edit_order',
                'view_menu',
                'create_menu',
                'edit_menu',
                'view_products',
                'create_product',
                'edit_product',
                'view_customers',
                'view_staff',
                'view_payments',
                'view_reports',
                'view_inventory',
                'manage_inventory',
            ],
        ],
        'chef' => [
            'label' => 'Chef cuisinier',
            'description' => 'Gestion de la cuisine',
            'color' => '#28a745',
            'permissions' => [
                'view_dashboard',
                'view_orders',
                'update_order_status',
                'view_menu',
                'edit_menu',
                'view_products',
                'edit_product',
                'view_inventory',
                'manage_inventory',
            ],
        ],
        'waiter' => [
            'label' => 'Serveur',
            'description' => 'Service et prise de commandes',
            'color' => '#17a2b8',
            'permissions' => [
                'view_dashboard',
                'view_orders',
                'create_order',
                'view_menu',
                'view_products',
                'view_customers',
                'create_customer',
            ],
        ],
        'delivery_man' => [
            'label' => 'Livreur',
            'description' => 'Gestion des livraisons',
            'color' => '#ffc107',
            'permissions' => [
                'view_orders',
                'update_delivery_status',
                'view_customers',
            ],
        ],
        'cashier' => [
            'label' => 'Caissier',
            'description' => 'Gestion des paiements',
            'color' => '#6c757d',
            'permissions' => [
                'view_orders',
                'view_payments',
                'create_payment',
                'view_dashboard',
            ],
        ],
        'kitchen_staff' => [
            'label' => 'Personnel de cuisine',
            'description' => 'Préparation des plats',
            'color' => '#e83e8c',
            'permissions' => [
                'view_orders',
                'update_order_status',
                'view_products',
            ],
        ],
    ],

    'permissions' => [
        // Dashboard
        'view_dashboard' => 'Voir le tableau de bord',

        // Orders
        'view_orders' => 'Voir les commandes',
        'create_order' => 'Créer une commande',
        'edit_order' => 'Modifier une commande',
        'delete_order' => 'Supprimer une commande',
        'update_order_status' => 'Mettre à jour le statut de la commande',

        // Menu
        'view_menu' => 'Voir le menu',
        'create_menu' => 'Créer un menu',
        'edit_menu' => 'Modifier un menu',
        'delete_menu' => 'Supprimer un menu',

        // Products
        'view_products' => 'Voir les produits',
        'create_product' => 'Créer un produit',
        'edit_product' => 'Modifier un produit',
        'delete_product' => 'Supprimer un produit',

        // Customers
        'view_customers' => 'Voir les clients',
        'create_customer' => 'Créer un client',
        'edit_customer' => 'Modifier un client',
        'delete_customer' => 'Supprimer un client',

        // Staff
        'view_staff' => 'Voir le personnel',
        'create_staff' => 'Créer un employé',
        'edit_staff' => 'Modifier un employé',
        'delete_staff' => 'Supprimer un employé',

        // Payments
        'view_payments' => 'Voir les paiements',
        'create_payment' => 'Créer un paiement',
        'refund_payment' => 'Rembourser un paiement',

        // Deliveries
        'view_deliveries' => 'Voir les livraisons',
        'update_delivery_status' => 'Mettre à jour le statut de livraison',

        // Reports
        'view_reports' => 'Voir les rapports',
        'export_reports' => 'Exporter les rapports',

        // Inventory
        'view_inventory' => 'Voir l\'inventaire',
        'manage_inventory' => 'Gérer l\'inventaire',

        // Settings
        'manage_settings' => 'Gérer les paramètres',
        'manage_permissions' => 'Gérer les permissions',
    ],
];
