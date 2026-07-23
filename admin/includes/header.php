<?php
// $active_page should be set by the including page (e.g. 'dashboard', 'orders'...)
$active_page = $active_page ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel | Food Delight</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family: 'Poppins', Arial, sans-serif; }
body { background:#f0f1f6; color:#222; }
.admin-wrap { display:flex; min-height:100vh; }

/* ---------- Sidebar ---------- */
.sidebar {
  width:230px; background: linear-gradient(180deg, #241f3a 0%, #1a1428 100%);
  color:#fff; padding:24px 0; flex-shrink:0; position:sticky; top:0; height:100vh;
  box-shadow: 3px 0 15px rgba(0,0,0,0.15);
}
.sidebar h2 { font-size:1.15rem; padding:0 22px 20px 22px; border-bottom:1px solid rgba(255,255,255,0.1); margin-bottom:14px; letter-spacing:0.3px; }
.sidebar a {
  display:flex; align-items:center; gap:10px; color:#c7c3dd; text-decoration:none;
  padding:13px 22px; font-size:0.93rem; font-weight:500; border-left:3px solid transparent;
  transition: all 0.2s ease;
}
.sidebar a:hover { background:rgba(255,255,255,0.06); color:#fff; }
.sidebar a.active { background:rgba(192,57,43,0.18); color:#fff; border-left:3px solid #ff7043; }

/* ---------- Content ---------- */
.content { flex:1; padding:32px 42px; animation: fadeIn 0.4s ease; }
@keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

.topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:12px; }
.topbar h1 { font-size:1.6rem; color:#241f3a; font-weight:700; }
.topbar a.logout {
  background:#fff; color:#c0392b; text-decoration:none; padding:9px 18px; border-radius:8px;
  font-size:0.88rem; font-weight:600; border:1.5px solid #c0392b; transition: all 0.2s ease;
}
.topbar a.logout:hover { background:#c0392b; color:#fff; }

/* ---------- Tables ---------- */
table { width:100%; border-collapse: collapse; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 18px rgba(30,20,60,0.07); }
th, td { padding:14px 16px; text-align:left; border-bottom:1px solid #f0f0f4; font-size:0.9rem; }
th { background:#241f3a; color:#fff; font-weight:600; font-size:0.82rem; text-transform:uppercase; letter-spacing:0.4px; }
tr { transition: background 0.15s ease; }
tr:hover { background:#faf8ff; }

/* ---------- Badges ---------- */
.badge { padding:5px 12px; border-radius:20px; font-size:0.76rem; font-weight:700; color:#fff; display:inline-block; }
.badge.green { background:#2e7d32; }
.badge.orange { background:#e65100; }
.badge.red { background:#c62828; }
.badge.blue { background:#1565c0; }
.badge.gray { background:#616161; }

/* ---------- Stat cards ---------- */
.card-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
  gap: 20px;
  margin-bottom: 32px;
}
.stat-card {
  background:#fff; border-radius:14px; padding:20px 22px; min-width:0;
  box-shadow:0 4px 18px rgba(30,20,60,0.07); border-left:5px solid #ff7043;
  display:flex; align-items:center; gap:14px; overflow:hidden;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  animation: popUp 0.45s ease backwards;
}
.stat-card:hover { transform: translateY(-4px); box-shadow:0 10px 28px rgba(30,20,60,0.14); }
.stat-card:nth-child(1){ animation-delay:0.02s; } .stat-card:nth-child(2){ animation-delay:0.08s; }
.stat-card:nth-child(3){ animation-delay:0.14s; } .stat-card:nth-child(4){ animation-delay:0.20s; }
.stat-card:nth-child(5){ animation-delay:0.26s; } .stat-card:nth-child(6){ animation-delay:0.32s; }
@keyframes popUp { from { opacity:0; transform: translateY(14px) scale(0.97); } to { opacity:1; transform: translateY(0) scale(1); } }
.stat-icon { font-size:1.6rem; width:48px; height:48px; border-radius:12px; background:#fff1ec; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-card .stat-text { min-width:0; flex:1; }
.stat-card h3 {
  font-size:1.35rem; color:#241f3a; font-weight:700; line-height:1.25;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.stat-card p { color:#888; font-size:0.78rem; margin-top:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* ---------- Buttons ---------- */
.btn { background:#c0392b; color:#fff; border:none; padding:9px 16px; border-radius:8px; cursor:pointer; font-size:0.85rem; font-weight:600; text-decoration:none; display:inline-block; transition: all 0.2s ease; }
.btn:hover { background:#96281b; transform: translateY(-1px); }
.btn.small { padding:6px 12px; font-size:0.78rem; }
form.inline { display:inline; }

.form-box { background:#fff; padding:22px 26px; border-radius:14px; box-shadow:0 4px 18px rgba(30,20,60,0.07); margin-bottom:26px; max-width:520px; }
.form-box label { font-size:0.82rem; color:#555; font-weight:600; display:block; margin-bottom:4px; }
.form-box input, .form-box textarea, .form-box select { width:100%; padding:10px; margin-bottom:14px; border:1.5px solid #e5e5ee; border-radius:8px; font-size:0.9rem; transition: border-color 0.2s ease; }
.form-box input:focus, .form-box textarea:focus, .form-box select:focus { outline:none; border-color:#ff7043; }

.msg { padding:12px 18px; border-radius:8px; margin-bottom:18px; font-weight:600; font-size:0.9rem; animation: fadeIn 0.4s ease; }
.msg.success { background:#e8f5e9; color:#256029; }
.msg.error { background:#fdecea; color:#a1260c; }
</style>
</head>
<body>
<div class="admin-wrap">
  <div class="sidebar">
    <h2>🍴 Admin Panel</h2>
    <a href="dashboard.php" class="<?= $active_page==='dashboard'?'active':'' ?>">📊 Dashboard</a>
    <a href="orders.php" class="<?= $active_page==='orders'?'active':'' ?>">🧾 Orders</a>
    <a href="menu.php" class="<?= $active_page==='menu'?'active':'' ?>">🍔 Menu Items</a>
    <a href="users.php" class="<?= $active_page==='users'?'active':'' ?>">👥 Users</a>
    <a href="reviews.php" class="<?= $active_page==='reviews'?'active':'' ?>">⭐ Reviews</a>
    <a href="messages.php" class="<?= $active_page==='messages'?'active':'' ?>">✉️ Contact Messages</a>
    <a href="../home.php" target="_blank">🔗 View Site</a>
  </div>
  <div class="content">
