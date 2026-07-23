<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $id = (int) $_POST['id'];
        mysqli_query($connect, "DELETE FROM users WHERE id=$id");
        $msg = "User deleted.";
    } elseif (isset($_POST['verify_user'])) {
        $id = (int) $_POST['id'];
        mysqli_query($connect, "UPDATE users SET is_verified=1 WHERE id=$id");
        $msg = "User manually marked as verified.";
    }
}

$users = mysqli_query($connect, "SELECT * FROM users ORDER BY id DESC");

$active_page = 'users';
require __DIR__ . '/includes/header.php';
?>

<div class="topbar">
  <h1>Registered Users</h1>
  <a class="logout" href="logout.php">Logout</a>
</div>

<?php if($msg): ?><div class="msg success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<table>
  <tr><th>#</th><th>Full Name</th><th>Email</th><th>Verified</th><th>Joined</th><th>Actions</th></tr>
  <?php while($u = mysqli_fetch_assoc($users)): ?>
  <tr>
    <td>#<?= $u['id'] ?></td>
    <td><?= htmlspecialchars($u['fullname']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td><?= $u['is_verified'] ? '<span class="badge green">Verified</span>' : '<span class="badge orange">Unverified</span>' ?></td>
    <td><?= htmlspecialchars($u['created_at']) ?></td>
    <td>
      <?php if(!$u['is_verified']): ?>
      <form method="POST" class="inline">
        <input type="hidden" name="id" value="<?= $u['id'] ?>">
        <button type="submit" name="verify_user" class="btn small">Mark Verified</button>
      </form>
      <?php endif; ?>
      <form method="POST" class="inline" onsubmit="return confirm('Delete this user?');">
        <input type="hidden" name="id" value="<?= $u['id'] ?>">
        <button type="submit" name="delete_user" class="btn small" style="background:#616161;">Delete</button>
      </form>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

<?php require __DIR__ . '/includes/footer.php'; ?>
