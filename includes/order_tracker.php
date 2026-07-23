<?php
/**
 * Renders an animated horizontal progress tracker for an order status.
 * Usage: render_order_tracker($order_status);
 */
function render_order_tracker($status) {
    $steps = ['Placed', 'Preparing', 'Out for Delivery', 'Delivered'];
    $icons = ['📝', '👨‍🍳', '🛵', '🎉'];

    if ($status === 'Cancelled') {
        echo '<div class="tracker-cancelled"><span>❌</span> This order was cancelled.</div>';
        return;
    }

    $current_index = array_search($status, $steps);
    if ($current_index === false) { $current_index = 0; }

    $progress_pct = $current_index === 0 ? 0 : ($current_index / (count($steps) - 1)) * 100;
    ?>
    <div class="order-tracker">
      <div class="tracker-line-bg">
        <div class="tracker-line-fill" style="--fill-to: <?= $progress_pct ?>%;"></div>
      </div>
      <div class="tracker-steps">
        <?php foreach ($steps as $i => $step): ?>
          <div class="tracker-step <?= $i < $current_index ? 'done' : ($i === $current_index ? 'active' : 'pending') ?>">
            <div class="tracker-dot"><?= $icons[$i] ?></div>
            <div class="tracker-label"><?= htmlspecialchars($step) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php
}
