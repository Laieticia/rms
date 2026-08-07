<?php $__env->startSection('title', 'Noter votre commande'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5" style="max-width:600px;">
    <h2 class="mb-4">Comment était votre commande ?</h2>
    <p class="text-muted mb-4"><?php echo e($order->restaurant->name); ?> — Commande #<?php echo e($order->order_number); ?></p>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('orders.review', $order)); ?>" class="bg-white rounded-4 shadow-sm p-4">
        <?php echo csrf_field(); ?>

        <?php
            $ratingFields = [
                'rating' => 'Note globale',
                'food_rating' => 'Qualité des plats',
                'delivery_rating' => 'Livraison',
                'service_rating' => 'Service',
            ];
        ?>

        <?php $__currentLoopData = $ratingFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mb-4">
                <label class="form-label fw-bold"><?php echo e($label); ?> <?php if($field === 'rating'): ?><span class="text-danger">*</span><?php endif; ?></label>
                <div class="star-rating" data-field="<?php echo e($field); ?>">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star star-icon" data-value="<?php echo e($i); ?>" style="font-size:28px;cursor:pointer;color:#ddd;"></i>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="<?php echo e($field); ?>" id="input_<?php echo e($field); ?>" value="<?php echo e(old($field)); ?>" <?php echo e($field === 'rating' ? 'required' : ''); ?>>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="mb-4">
            <label class="form-label fw-bold">Votre commentaire (optionnel)</label>
            <textarea name="comment" class="form-control" rows="4" maxlength="1000" placeholder="Partagez votre expérience..."><?php echo e(old('comment')); ?></textarea>
        </div>

        <button type="submit" class="btn w-100 py-3" style="background:var(--primary);color:#fff;border-radius:14px;">
            <i class="bi bi-send"></i> Envoyer mon avis
        </button>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.star-rating').forEach(function (group) {
    const field = group.dataset.field;
    const input = document.getElementById('input_' + field);
    const stars = group.querySelectorAll('.star-icon');

    function paint(value) {
        stars.forEach(function (star) {
            const active = parseInt(star.dataset.value) <= value;
            star.className = active ? 'bi bi-star-fill star-icon' : 'bi bi-star star-icon';
            star.style.color = active ? '#ffc107' : '#ddd';
        });
    }

    stars.forEach(function (star) {
        star.addEventListener('click', function () {
            input.value = this.dataset.value;
            paint(parseInt(this.dataset.value));
        });
    });

    if (input.value) { paint(parseInt(input.value)); }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.storefront', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hdcode\Documents\hdcode\backend\2026\news2026\rms\resources\views/reviews/create.blade.php ENDPATH**/ ?>