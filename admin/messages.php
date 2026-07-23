<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    $id = (int) $_POST['id'];
    mysqli_query($connect, "DELETE FROM contact_messages WHERE id=$id");
    $msg = "Message deleted.";
}

$messages = mysqli_query($connect, "SELECT * FROM contact_messages ORDER BY created_at DESC");

$active_page = 'messages';
require __DIR__ . '/includes/header.php';
?>

<div class="topbar">
  <h1>Contact Messages</h1>
  <a class="logout" href="logout.php">Logout</a>
</div>

<?php if($msg): ?><div class="msg success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<table>
  <tr><th>#</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th><th>Actions</th></tr>
  <?php while($m = mysqli_fetch_assoc($messages)): ?>
  <tr>
    <td>#<?= $m['id'] ?></td>
    <td><?= htmlspecialchars($m['name']) ?></td>
    <td><a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email']) ?></a></td>
    <td style="max-width:350px;"><?= nl2br(htmlspecialchars($m['message'])) ?></td>
    <td><?= htmlspecialchars($m['created_at']) ?></td>
    <td>
      <form method="POST" class="inline" onsubmit="return confirm('Delete this message?');">
        <input type="hidden" name="id" value="<?= $m['id'] ?>">
        <button type="submit" name="delete_message" class="btn small" style="background:#616161;">Delete</button>
      </form>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

<?php require __DIR__ . '/includes/footer.php'; ?>
