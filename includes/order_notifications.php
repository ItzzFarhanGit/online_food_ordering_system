<?php
/**
 * Order status notifications for logged-in users.
 * get_order_notifications() fetches unseen updates but does NOT mark them
 * as seen - they keep showing on every page / every login until the user
 * explicitly dismisses them (closes the toast, which calls
 * mark_notification_seen.php via a small fetch() request).
 */
require_once __DIR__ . '/../config.php';

function get_order_notifications($connect, $user_id) {
    $user_id = (int) $user_id;
    $result = mysqli_query($connect, "SELECT * FROM orders WHERE user_id=$user_id AND notified=0 ORDER BY id ASC");

    $notifications = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = build_order_notification($row);
        }
    }

    return $notifications;
}

function build_order_notification($order) {
    $dish = $order['dish_name'];
    $id = $order['id'];
    $status = $order['order_status'];

    if ($status === 'Delivered') {
        return [
            'order_id' => $id,
            'type' => 'success',
            'icon' => '🎉',
            'title' => 'Order Delivered!',
            'message' => "Your order #$id ($dish) has been delivered. Thank you for ordering with " . SITE_NAME . " - enjoy your meal! ❤️",
        ];
    }

    if ($status === 'Cancelled') {
        return [
            'order_id' => $id,
            'type' => 'error',
            'icon' => '❌',
            'title' => 'Order Cancelled',
            'message' => "Your order #$id ($dish) was cancelled.",
        ];
    }

    if ($order['payment_status'] === 'Paid' && $status !== 'Delivered') {
        // Payment confirmation update (e.g. COD marked as collected)
    }

    $status_messages = [
        'Preparing' => "is now being prepared in the kitchen 👨‍🍳",
        'Out for Delivery' => "is out for delivery 🛵 - it'll be with you soon!",
        'Placed' => "has been placed and is waiting to be confirmed 📝",
    ];
    $desc = $status_messages[$status] ?? ("status changed to \"" . $status . "\"");

    return [
        'order_id' => $id,
        'type' => 'info',
        'icon' => 'ℹ️',
        'title' => 'Order Update',
        'message' => "Your order #$id ($dish) $desc",
    ];
}
