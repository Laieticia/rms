<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'RestaurantMS'); ?> - Gestion Restaurant</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo e(url('admin/assets/images/favicon.ico')); ?>" />
    <link rel="stylesheet" href="<?php echo e(url('admin/assets/css/backend-plugin.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('admin/assets/css/backend.css?v=1.0.0')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('admin/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('admin/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('admin/assets/vendor/remixicon/fonts/remixicon.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="  ">
    <!-- loader Start -->
    <!-- <div id="loading">
        <div id="loading-center">
        </div>
    </div> -->
    <!-- loader END -->
    <!-- Wrapper Start -->
    <div class="wrapper">

        
        <?php echo $__env->make("layouts.menu", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>  
        <?php echo $__env->make("layouts.header", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>  
        <?php echo $__env->yieldContent("content"); ?>
        
    </div>
    <!-- Wrapper End-->
    <?php echo $__env->make("layouts.footer", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> 
    <!-- Backend Bundle JavaScript -->
    <script src="<?php echo e(url('admin/assets/js/backend-bundle.min.js')); ?>"></script>

    <!-- Table Treeview JavaScript -->
    <script src="<?php echo e(url('admin/assets/js/table-treeview.js')); ?>"></script>

    <!-- Chart Custom JavaScript -->
    <script src="<?php echo e(url('admin/assets/js/customizer.js')); ?>"></script>

    <!-- Chart Custom JavaScript -->
    <script async src="<?php echo e(url('admin/assets/js/chart-custom.js')); ?>"></script>

    <!-- app JavaScript -->
    <script src="<?php echo e(url('admin/assets/js/app.js')); ?>"></script>
    <script>
        window.userId = <?php echo e(auth()->id()); ?>;
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/layouts/app.blade.php ENDPATH**/ ?>