<div class="iq-sidebar  sidebar-default ">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="header-logo">
            <img src="<?php echo e(url('admin/assets/images/logo.png')); ?>" class="img-fluid rounded-normal light-logo" alt="logo">
            <h5 class="logo-title light-logo ml-3">POSDash</h5>
        </a>
        <div class="iq-menu-bt-sidebar ml-0">
            <i class="las la-bars wrapper-menu"></i>
        </div>
    </div>
    <div class="data-scrollbar" data-scroll="1">
        <nav class="iq-sidebar-menu">
            
            <ul id="iq-sidebar-toggle" class="iq-menu">
    
                
                <li class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="svg-icon">
                        <svg class="svg-icon" id="p-dash1" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <span class="ml-4">Tableau de bord</span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.orders.*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.orders.index')); ?>" class="svg-icon">
                        <svg class="svg-icon" id="p-dash2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span class="ml-4">Commandes</span>
                        <?php if($pendingCount ?? 0 > 0): ?>
                            <span class="badge badge-warning float-right"><?php echo e($pendingCount ?? 0); ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.reservations.*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.reservations.index')); ?>" class="svg-icon">
                        <svg class="svg-icon" id="p-dash2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span class="ml-4">Réservations</span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.products.*') ? 'active' : ''); ?>">
                    <a href="#productsSubmenu" class="collapsed <?php echo e(request()->routeIs('admin.products.*') ? '' : 'collapsed'); ?>" 
                    data-toggle="collapse" aria-expanded="<?php echo e(request()->routeIs('admin.products.*') ? 'true' : 'false'); ?>">
                        <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <span class="ml-4">Produits</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="productsSubmenu" class="iq-submenu collapse <?php echo e(request()->routeIs('admin.products.*') ? 'show' : ''); ?>" data-bs-parent="#iq-sidebar-toggle">
                        <li class="<?php echo e(request()->routeIs('admin.products.index') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.products.index')); ?>"><i class="las la-list"></i> Liste des produits</a>
                        </li>
                        <li class="<?php echo e(request()->routeIs('admin.products.create') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.products.create')); ?>"><i class="las la-plus"></i> Ajouter un produit</a>
                        </li>
                    </ul>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.categories.*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.categories.index')); ?>" class="svg-icon">
                        <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span class="ml-4">Catégories</span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.menus.*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.menus.index')); ?>" class="svg-icon">
                        <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <span class="ml-4">Menus</span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.coupons.*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.coupons.index')); ?>" class="svg-icon">
                        <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 12l2 2 4-4"></path>
                        </svg>
                        <span class="ml-4">Coupons</span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.staff.*') ? 'active' : ''); ?>">
                    <a href="#staffSubmenu" class="<?php echo e(request()->routeIs('admin.staff.*') ? '' : 'collapsed'); ?>" 
                    data-toggle="collapse" aria-expanded="<?php echo e(request()->routeIs('admin.staff.*') ? 'true' : 'false'); ?>">
                        <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span class="ml-4">Personnel</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="staffSubmenu" class="iq-submenu collapse <?php echo e(request()->routeIs('admin.staff.*') ? 'show' : ''); ?>" data-bs-parent="#iq-sidebar-toggle">
                        <li class="<?php echo e(request()->routeIs('admin.staff.index') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.staff.index')); ?>"><i class="las la-users"></i> Liste du personnel</a>
                        </li>
                        <li class="<?php echo e(request()->routeIs('admin.staff.create') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.staff.create')); ?>"><i class="las la-user-plus"></i> Ajouter un membre</a>
                        </li>
                    </ul>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="svg-icon">
                        <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                        </svg>
                        <span class="ml-4">Clients</span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.reviews.*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.reviews.index')); ?>" class="svg-icon">
                        <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <span class="ml-4">Avis</span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.reports') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.reports')); ?>" class="svg-icon">
                        <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                            <line x1="2" y1="20" x2="22" y2="20"></line>
                        </svg>
                        <span class="ml-4">Rapports</span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('admin.settings') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('admin.settings')); ?>" class="svg-icon">
                        <svg class="svg-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        <span class="ml-4">Paramètres</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div id="sidebar-bottom" class="position-relative sidebar-bottom">
            <div class="card border-none">
                <div class="card-body p-0">
                    <div class="sidebarbottom-content">
                        <a href="<?php echo e(route('home')); ?>" class="btn sidebar-bottom-btn mt-4" target="_blank">Visitez le site</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-3"></div>
    </div>
</div><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/layouts/menu.blade.php ENDPATH**/ ?>