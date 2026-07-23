<?php
/**
 * Standalone order-status toast notifications. Include this on any page
 * (after session_start() + db.php) to show the logged-in user any order
 * status updates they haven't seen yet - e.g. "Out for Delivery", or a
 * special "Delivered - thank you" message.
 *
 * These stay on screen (and keep reappearing on every page / every login)
 * until the user explicitly closes them - closing calls
 * mark_notification_seen.php so it won't show again afterwards.
 */
if (isset($_SESSION['user_id'])):
    require_once __DIR__ . '/order_notifications.php';
    $__order_notifications = get_order_notifications($connect, $_SESSION['user_id']);
    if (!empty($__order_notifications)):
?>
<div class="order-toast-stack">
  <?php foreach ($__order_notifications as $i => $n): ?>
    <div class="order-toast toast-<?= htmlspecialchars($n['type']) ?>" data-order-id="<?= (int) $n['order_id'] ?>" style="animation-delay: <?= $i * 0.15 ?>s;">
      <span class="toast-icon"><?= $n['icon'] ?></span>
      <div class="toast-body">
        <b><?= htmlspecialchars($n['title']) ?></b>
        <p><?= htmlspecialchars($n['message']) ?></p>
      </div>
      <button type="button" class="toast-close" onclick="dismissOrderToast(this)">&times;</button>
    </div>
  <?php endforeach; ?>
</div>
<script>
function dismissOrderToast(btn) {
  var toast = btn.closest('.order-toast');
  var orderId = toast.getAttribute('data-order-id');
  toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
  toast.style.opacity = '0';
  toast.style.transform = 'translateX(40px)';
  setTimeout(function () { toast.remove(); }, 300);

  var params = new URLSearchParams();
  params.append('order_id', orderId);
  fetch('mark_notification_seen.php', { method: 'POST', body: params }).catch(function () {});
}
</script>
<style>
.order-toast-stack {
  position: fixed; top: 80px; right: 20px; z-index: 9999;
  display: flex; flex-direction: column; gap: 12px; max-width: 340px;
}
.order-toast {
  background: #fff; border-radius: 12px; padding: 14px 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.25);
  display: flex; align-items: flex-start; gap: 10px;
  animation: toastIn 0.5s ease backwards;
  border-left: 5px solid #1565c0;
}
.order-toast.toast-success { border-left-color: #2e7d32; }
.order-toast.toast-error { border-left-color: #c62828; }
.order-toast.toast-info { border-left-color: #1565c0; }
@keyframes toastIn { from { opacity:0; transform: translateX(40px); } to { opacity:1; transform: translateX(0); } }
.toast-icon { font-size: 1.4rem; flex-shrink:0; }
.toast-body { flex:1; }
.toast-body b { color:#222; font-size:0.92rem; display:block; margin-bottom:2px; }
.toast-body p { color:#555; font-size:0.85rem; margin:0; text-align:left; line-height:1.35; }
.toast-close { background:none; border:none; font-size:1.1rem; color:#999; cursor:pointer; line-height:1; padding:0 0 0 6px; }
.toast-close:hover { color:#333; }
@media (max-width: 480px) {
  .order-toast-stack { right: 10px; left: 10px; max-width: none; top: 70px; }
}
</style>
<?php endif; endif; ?>
