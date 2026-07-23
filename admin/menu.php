<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';

$msg = "";
$edit_item = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['save_item'])) {
        $id = (int) ($_POST['id'] ?? 0);
        $name = mysqli_real_escape_string($connect, trim($_POST['name'] ?? ''));
        $description = mysqli_real_escape_string($connect, trim($_POST['description'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0);
        $image = mysqli_real_escape_string($connect, trim($_POST['image'] ?? ''));
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;

        if ($name === '' || $image === '' || $price <= 0) {
            $msg = "Please fill in name, a positive price, and an image path/URL.";
        } elseif ($id > 0) {
            mysqli_query($connect, "UPDATE menu_items SET name='$name', description='$description', price='$price', image='$image', is_featured=$is_featured WHERE id=$id");
            $msg = "Dish updated successfully.";
        } else {
            mysqli_query($connect, "INSERT INTO menu_items (name, description, price, image, is_featured) VALUES ('$name','$description','$price','$image',$is_featured)");
            $msg = "New dish added successfully.";
        }
    } elseif (isset($_POST['delete_item'])) {
        $id = (int) $_POST['id'];
        mysqli_query($connect, "DELETE FROM menu_items WHERE id=$id");
        $msg = "Dish deleted.";
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $res = mysqli_query($connect, "SELECT * FROM menu_items WHERE id=$id");
    if ($res && mysqli_num_rows($res) > 0) {
        $edit_item = mysqli_fetch_assoc($res);
    }
}

$items = mysqli_query($connect, "SELECT * FROM menu_items ORDER BY id ASC");

$active_page = 'menu';
require __DIR__ . '/includes/header.php';
?>

<div class="topbar">
  <h1>Menu Items</h1>
  <a class="logout" href="logout.php">Logout</a>
</div>

<?php if($msg): ?><div class="msg success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="form-box">
  <h3 style="margin-bottom:12px;"><?= $edit_item ? 'Edit Dish #' . $edit_item['id'] : 'Add a New Dish' ?></h3>
  <form method="POST" action="menu.php">
    <input type="hidden" name="id" value="<?= $edit_item['id'] ?? 0 ?>">
    <input type="text" name="name" placeholder="Dish name" value="<?= htmlspecialchars($edit_item['name'] ?? '') ?>" required>
    <textarea name="description" placeholder="Description" rows="2"><?= htmlspecialchars($edit_item['description'] ?? '') ?></textarea>
    <input type="number" step="0.01" name="price" placeholder="Price" value="<?= htmlspecialchars($edit_item['price'] ?? '') ?>" required>
    <input type="text" name="image" placeholder="Image path e.g. IMAGES/Pizza.jpg" value="<?= htmlspecialchars($edit_item['image'] ?? '') ?>" required>
    <label style="display:flex; align-items:center; gap:8px; font-size:0.9rem; margin-bottom:12px;">
      <input type="checkbox" name="is_featured" style="width:auto;" <?= !empty($edit_item['is_featured']) ? 'checked' : '' ?>>
      Show on Home page (featured)
    </label>
    <button type="submit" name="save_item" class="btn"><?= $edit_item ? 'Update Dish' : 'Add Dish' ?></button>
    <?php if($edit_item): ?><a href="menu.php" class="btn small" style="background:#616161;">Cancel</a><?php endif; ?>
  </form>
</div>

<table>
  <tr><th>#</th><th>Image</th><th>Name</th><th>Description</th><th>Price</th><th>Featured</th><th>Actions</th></tr>
  <?php while($item = mysqli_fetch_assoc($items)): ?>
  <tr>
    <td>#<?= $item['id'] ?></td>
    <td><small><?= htmlspecialchars($item['image']) ?></small></td>
    <td><?= htmlspecialchars($item['name']) ?></td>
    <td style="max-width:220px;"><?= htmlspecialchars($item['description']) ?></td>
    <td><?= htmlspecialchars(SITE_CURRENCY_SYMBOL) ?> <?= number_format($item['price'],2) ?></td>
    <td><?= $item['is_featured'] ? '<span class="badge green">Yes</span>' : '<span class="badge gray">No</span>' ?></td>
    <td>
      <a href="menu.php?edit=<?= $item['id'] ?>" class="btn small">Edit</a>
      <form method="POST" class="inline" onsubmit="return confirm('Delete this dish?');">
        <input type="hidden" name="id" value="<?= $item['id'] ?>">
        <button type="submit" name="delete_item" class="btn small" style="background:#616161;">Delete</button>
      </form>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

<?php require __DIR__ . '/includes/footer.php'; ?>
