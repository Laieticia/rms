<?php $__env->startSection('title', 'Suivi de la commande #' . $order->order_number); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0">Suivi de la commande #<?php echo e($order->order_number); ?></h2>
        <div class="d-flex gap-2">
            <?php if($order->canBeReviewedBy(auth()->user())): ?>
                <a href="<?php echo e(route('orders.review.create', $order)); ?>" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-star"></i> Laisser un avis
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('profile.orders.show', $order)); ?>" class="btn btn-outline-secondary btn-sm">Détails de la commande</a>
        </div>
    </div>

    
    <?php
        $steps = [
            'pending' => 'Reçue',
            'confirmed' => 'Confirmée',
            'preparing' => 'En préparation',
            'ready' => 'Prête',
            'in_delivery' => 'En livraison',
            'delivered' => 'Livrée',
        ];
        $stepKeys = array_keys($steps);
        $currentIndex = array_search($order->status, $stepKeys);
    ?>
    <?php if($order->status === 'cancelled'): ?>
        <div class="alert alert-danger">Cette commande a été annulée.</div>
    <?php else: ?>
        <div class="d-flex justify-content-between mb-5 flex-wrap gap-2">
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="text-center flex-fill">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                         style="width:36px;height:36px;background:<?php echo e($currentIndex !== false && array_search($key, $stepKeys) <= $currentIndex ? 'var(--primary)' : '#e0e0e0'); ?>;color:#fff;">
                        <i class="bi bi-check"></i>
                    </div>
                    <small><?php echo e($label); ?></small>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div id="trackingMap" class="rounded-4 shadow-sm" style="height:420px;background:#eee;"></div>
            <p class="text-muted small mt-2" id="trackingStatus">
                <?php if($order->deliveryPerson): ?>
                    Livreur : <?php echo e($order->deliveryPerson->first_name); ?> — en route.
                <?php else: ?>
                    En attente d'assignation d'un livreur.
                <?php endif; ?>
            </p>
        </div>

        <div class="col-lg-4">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <h6 class="mb-3"><?php echo e($order->restaurant->name); ?></h6>
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between small mb-1">
                        <span><?php echo e($item->quantity); ?> x <?php echo e($item->product_name); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span><span><?php echo e($order->formatted_total); ?></span>
                </div>
                <hr>
                <p class="small text-muted mb-0"><i class="bi bi-geo-alt me-1"></i><?php echo e($order->delivery_address); ?>, <?php echo e($order->delivery_city); ?></p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php
        $lastPoint = $order->deliveryTracking->first();
        $restaurantLat = $order->restaurant->latitude ?? 5.4667;
        $restaurantLng = $order->restaurant->longitude ?? 10.4167;
    ?>

    const startLat = <?php echo e($lastPoint->latitude ?? $restaurantLat); ?>;
    const startLng = <?php echo e($lastPoint->longitude ?? $restaurantLng); ?>;

    const map = L.map('trackingMap').setView([startLat, startLng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const deliveryIcon = L.divIcon({
        html: '<i class="bi bi-motorcycle" style="font-size:22px;color:var(--primary,#e8281a);"></i>',
        className: '', iconSize: [24, 24],
    });

    let marker = L.marker([startLat, startLng], { icon: deliveryIcon }).addTo(map);

    L.marker([<?php echo e($order->delivery_latitude ?? $restaurantLat); ?>, <?php echo e($order->delivery_longitude ?? $restaurantLng); ?>])
        .addTo(map)
        .bindPopup('Adresse de livraison');

    // Abonnement temps réel (Reverb / protocole Pusher)
    <?php if(auth()->check()): ?>
    try {
        const pusher = new Pusher('<?php echo e(config('broadcasting.connections.reverb.key')); ?>', {
            wsHost: '<?php echo e(config('broadcasting.connections.reverb.options.host')); ?>',
            wsPort: <?php echo e(config('broadcasting.connections.reverb.options.port', 8080)); ?>,
            forceTLS: <?php echo e(config('broadcasting.connections.reverb.options.useTLS') ? 'true' : 'false'); ?>,
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } },
        });

        const channel = pusher.subscribe('private-orders.<?php echo e($order->id); ?>');
        channel.bind('delivery.location.updated', function (data) {
            const newLatLng = [data.latitude, data.longitude];
            marker.setLatLng(newLatLng);
            map.panTo(newLatLng);
            document.getElementById('trackingStatus').innerText = 'Livreur en mouvement — mise à jour en direct.';
        });
    } catch (e) {
        console.warn('Suivi temps réel indisponible :', e);
    }
    <?php endif; ?>
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/orders/track.blade.php ENDPATH**/ ?>