<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Réinitialiser les rôles en cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Liste des permissions
        $permissions = [
            // Gestion des utilisateurs
            'user.view', 'user.create', 'user.edit', 'user.delete',
            'user.assign_role', 'user.block',
            
            // Gestion du restaurant
            'restaurant.view', 'restaurant.create', 'restaurant.edit', 'restaurant.delete',
            'restaurant.manage_settings', 'restaurant.view_reports',
            
            // Gestion des catégories
            'category.view', 'category.create', 'category.edit', 'category.delete',
            
            // Gestion des produits
            'product.view', 'product.create', 'product.edit', 'product.delete',
            'product.manage_inventory', 'product.set_prices',
            
            // Gestion des menus
            'menu.view', 'menu.create', 'menu.edit', 'menu.delete',
            
            // Gestion des commandes
            'order.view', 'order.create', 'order.edit', 'order.delete',
            'order.change_status', 'order.cancel', 'order.refund',
            'order.view_all', 'order.manage_delivery',
            
            // Gestion des livraisons
            'delivery.view', 'delivery.assign', 'delivery.track',
            'delivery.manage_zones',
            
            // Gestion des paiements
            'payment.view', 'payment.process', 'payment.refund',
            
            // Gestion des coupons
            'coupon.view', 'coupon.create', 'coupon.edit', 'coupon.delete',
            
            // Gestion des avis
            'review.view', 'review.moderate', 'review.respond',
            
            // Gestion des réservations
            'reservation.view', 'reservation.create', 'reservation.edit',
            'reservation.confirm', 'reservation.cancel',
            
            // Gestion de la fidélité
            'loyalty.view', 'loyalty.create_rewards', 'loyalty.adjust_points',
            
            // Gestion des rapports
            'reports.view', 'reports.export', 'reports.financial',
            
            // Gestion du personnel
            'staff.view', 'staff.create', 'staff.edit', 'staff.delete',
            'staff.manage_schedule',
            
            // Paramètres système
            'settings.view', 'settings.edit', 'settings.maintenance',
            'notifications.send', 'notifications.manage',
        ];

        // Créer les permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Créer les rôles et assigner les permissions
        $roles = [
            'super_admin' => $permissions, // Toutes les permissions
            'admin' => [
                'user.view', 'user.create', 'user.edit', 'user.block',
                'restaurant.view', 'restaurant.edit', 'restaurant.manage_settings', 'restaurant.view_reports',
                'category.view', 'category.create', 'category.edit', 'category.delete',
                'product.view', 'product.create', 'product.edit', 'product.delete',
                'product.manage_inventory', 'product.set_prices',
                'menu.view', 'menu.create', 'menu.edit', 'menu.delete',
                'order.view', 'order.view_all', 'order.change_status', 'order.cancel', 'order.refund',
                'delivery.view', 'delivery.assign', 'delivery.track', 'delivery.manage_zones',
                'payment.view', 'payment.process', 'payment.refund',
                'coupon.view', 'coupon.create', 'coupon.edit', 'coupon.delete',
                'review.view', 'review.moderate', 'review.respond',
                'reservation.view', 'reservation.confirm', 'reservation.cancel',
                'loyalty.view', 'loyalty.create_rewards', 'loyalty.adjust_points',
                'reports.view', 'reports.export', 'reports.financial',
                'staff.view', 'staff.create', 'staff.edit', 'staff.manage_schedule',
                'settings.view', 'settings.edit',
                'notifications.send', 'notifications.manage',
            ],
            'manager' => [
                'restaurant.view', 'restaurant.view_reports',
                'category.view', 'category.edit',
                'product.view', 'product.edit', 'product.manage_inventory',
                'menu.view', 'menu.edit',
                'order.view', 'order.change_status', 'order.cancel',
                'delivery.view', 'delivery.assign', 'delivery.track',
                'payment.view',
                'coupon.view', 'coupon.edit',
                'review.view', 'review.moderate', 'review.respond',
                'reservation.view', 'reservation.confirm', 'reservation.cancel',
                'loyalty.view',
                'reports.view', 'reports.export',
                'staff.view', 'staff.manage_schedule',
                'notifications.send',
            ],
            'chef' => [
                'category.view',
                'product.view', 'product.edit', 'product.manage_inventory',
                'menu.view',
                'order.view', 'order.change_status',
            ],
            'waiter' => [
                'category.view',
                'product.view',
                'menu.view',
                'order.view', 'order.create', 'order.change_status',
                'reservation.view', 'reservation.create',
            ],
            'delivery_person' => [
                'order.view',
                'delivery.view', 'delivery.track',
            ],
            'customer' => [
                'order.create', 'order.view',
                'review.view',
                'reservation.create', 'reservation.view',
                'loyalty.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
            $role->givePermissionTo($rolePermissions);
        }
    }
}