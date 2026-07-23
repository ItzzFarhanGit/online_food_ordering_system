<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review'])) {
    $id = (int) $_POST['id'];
    mysqli_query($connect, "DELETE FROM reviews WHERE id=$id");
    $msg = "Review deleted.";
}

$reviews = mysqli_query($connect, "SELECT * FROM reviews ORDER BY created_at DESC");

$active_page = 'reviews';
require __DIR__ . '/includes/header.php';
?>

<div class="topbar">
  <h1>Customer Reviews</h1>
  <a class="logout" href="logout.php">Logout</a>
</div>

<?php if($msg): ?><div class="msg success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<table>
  <tr><th>#</th><th>Name</th><th>Rating</th><th>Review</th><th>Date</th><th>Actions</th></tr>
  <?php while($r = mysqli_fetch_assoc($reviews)): ?>
  <tr>
    <td>#<?= $r['id'] ?></td>
    <td><?= htmlspecialchars($r['name']) ?></td>
    <td><?= str_repeat('⭐', $r['stars']) ?></td>
    <td style="max-width:320px;"><?= htmlspecialchars($r['review']) ?></td>
    <td><?= htmlspecialchars($r['created_at']) ?></td>
    <td>
      <form method="POST" class="inline" onsubmit="return confirm('Delete this review?');">
        <input type="hidden" name="id" value="<?= $r['id'] ?>">
        <button type="submit" name="delete_review" class="btn small" style="background:#616161;">Delete</button>
      </form>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

<?php require __DIR__ . '/includes/footer.php'; ?>
