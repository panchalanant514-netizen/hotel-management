<?php
$pageTitle = 'Manage Rooms';
require '../includes/header.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        $c = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_id=?"); $c->execute([$id]);
        if ($c->fetchColumn() > 0) {
            flash('This room has bookings, so it cannot be deleted. Set it to maintenance instead.', 'error');
        } else {
            $pdo->prepare("DELETE FROM rooms WHERE id=?")->execute([$id]);
            flash('Room deleted.');
        }
    } elseif (($_POST['action'] ?? '') === 'toggle') {
        $pdo->prepare("UPDATE rooms SET status = IF(status='available','maintenance','available') WHERE id=?")->execute([$id]);
        flash('Room status changed.');
    }
    redirect('admin/rooms.php');
}
$rooms = $pdo->query("SELECT * FROM rooms ORDER BY room_number")->fetchAll();
?>
<style>
  /* =========================================================
   Grand Horizon Hotel  -  main stylesheet
   ========================================================= */
:root{
  --primary:#1f3a5f; --accent:#c9a24d; --accent-dark:#a9852f;
  --bg:#f6f4ef; --text:#2b2b2b; --muted:#6b7280; --white:#fff;
  --success:#2e7d32; --danger:#c62828; --radius:14px;
  --shadow:0 6px 22px rgba(0,0,0,.09);
}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'Segoe UI',Tahoma,Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.6}
img{max-width:100%;display:block}
a{color:inherit;text-decoration:none}
.container{width:min(1150px,92%);margin:auto}
h1,h2,h3{font-family:Georgia,'Times New Roman',serif;color:var(--primary)}

/* ---------- header ---------- */
.site-header{background:var(--primary);position:sticky;top:0;z-index:50;box-shadow:0 2px 12px rgba(0,0,0,.25)}
.nav-wrap{display:flex;justify-content:space-between;align-items:center;padding:14px 0}
.logo{font-family:Georgia,serif;font-size:1.6rem;color:var(--accent);font-weight:bold;letter-spacing:1px}
nav{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
nav a{color:#e9edf3;padding:8px 12px;border-radius:8px;font-size:.95rem;transition:.2s}
nav a:hover,nav a.active{background:rgba(255,255,255,.12);color:var(--accent)}
.menu-toggle{display:none;background:none;border:0;color:#fff;font-size:1.8rem;cursor:pointer}

/* ---------- buttons ---------- */
.btn{display:inline-block;background:var(--accent);color:#1b1b1b;border:0;padding:12px 26px;border-radius:50px;font-weight:600;cursor:pointer;font-size:1rem;transition:.25s}
.btn:hover{background:var(--accent-dark);color:#fff;transform:translateY(-2px)}
.btn-small{padding:7px 16px;font-size:.88rem;background:var(--accent);color:#1b1b1b !important}
.btn-outline{background:transparent;border:2px solid var(--accent);color:var(--primary)}
.btn-danger{background:var(--danger);color:#fff}
.btn-success{background:var(--success);color:#fff}
.btn-block{display:block;width:100%;text-align:center}

/* ---------- alerts ---------- */
.alert{padding:13px 18px;border-radius:10px;margin:18px 0;font-weight:500}
.alert-success{background:#e3f4e4;color:#1b5e20;border-left:5px solid var(--success)}
.alert-error{background:#fde7e7;color:#8e1b1b;border-left:5px solid var(--danger)}

/* ---------- hero ---------- */
.hero{background:linear-gradient(rgba(10,20,40,.55),rgba(10,20,40,.7)),url('../images/hero.jpg') center/cover;min-height:560px;display:flex;align-items:center;color:#fff;text-align:center}
.hero h1{color:#fff;font-size:clamp(2.2rem,5vw,3.8rem);margin-bottom:12px}
.hero p{font-size:1.2rem;max-width:620px;margin:0 auto 28px;color:#e6e6e6}
.search-box{background:#fff;color:var(--text);padding:22px;border-radius:var(--radius);box-shadow:var(--shadow);display:grid;grid-template-columns:repeat(4,1fr) auto;gap:14px;align-items:end;margin:-55px auto 0;position:relative;z-index:5;width:min(1000px,92%)}
.search-box label{display:block;font-size:.8rem;font-weight:600;color:var(--muted);margin-bottom:4px;text-transform:uppercase}

/* ---------- forms ---------- */
input,select,textarea{width:100%;padding:11px 13px;border:1.5px solid #d6d3cc;border-radius:10px;font-size:1rem;font-family:inherit;background:#fff;transition:.2s}
input:focus,select:focus,textarea:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(201,162,77,.2)}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-weight:600;margin-bottom:5px;font-size:.92rem}
.error-text{color:var(--danger);font-size:.82rem;margin-top:3px;display:none}
.form-card{background:#fff;padding:34px;border-radius:var(--radius);box-shadow:var(--shadow);width:min(470px,100%);margin:50px auto}
.form-card h2{text-align:center;margin-bottom:22px}
.form-note{text-align:center;margin-top:16px;color:var(--muted);font-size:.92rem}
.form-note a{color:var(--accent-dark);font-weight:600}
.demo-box{background:#fff8e6;border:1px dashed var(--accent);padding:10px 14px;border-radius:10px;font-size:.85rem;margin-top:16px}

/* ---------- sections ---------- */
section{padding:70px 0}
.section-title{text-align:center;margin-bottom:42px}
.section-title h2{font-size:2.2rem}
.section-title p{color:var(--muted)}
.section-title::after{content:'';display:block;width:70px;height:3px;background:var(--accent);margin:12px auto 0}
.page-banner{background:linear-gradient(rgba(31,58,95,.85),rgba(31,58,95,.9)),url('../images/hero.jpg') center/cover;color:#fff;text-align:center;padding:60px 0}
.page-banner h1{color:#fff;font-size:2.4rem}

/* ---------- room cards ---------- */
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:28px}
.card{background:#fff;border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);transition:.3s}
.card:hover{transform:translateY(-6px);box-shadow:0 14px 34px rgba(0,0,0,.15)}
.card img{width:100%;height:220px;object-fit:cover}
.card-body{padding:20px}
.card-body h3{margin-bottom:6px}
.badge{display:inline-block;background:#eef2f8;color:var(--primary);font-size:.75rem;padding:3px 10px;border-radius:50px;font-weight:600;margin:0 4px 4px 0}
.price{font-size:1.4rem;color:var(--accent-dark);font-weight:700}
.price small{font-size:.8rem;color:var(--muted);font-weight:400}
.card-footer{display:flex;justify-content:space-between;align-items:center;margin-top:14px}
.filter-bar{background:#fff;padding:18px;border-radius:var(--radius);box-shadow:var(--shadow);display:flex;gap:14px;flex-wrap:wrap;align-items:end;margin-bottom:30px}
.filter-bar>div{flex:1;min-width:140px}
.filter-bar label{font-size:.8rem;font-weight:600;color:var(--muted);text-transform:uppercase;display:block;margin-bottom:4px}

/* ---------- features / about ---------- */
.features{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px}
.feature{background:#fff;padding:30px 22px;border-radius:var(--radius);text-align:center;box-shadow:var(--shadow)}
.feature .icon{font-size:2.4rem;margin-bottom:10px}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center}
.two-col img{border-radius:var(--radius);box-shadow:var(--shadow)}
.gallery{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:18px}
.gallery img{border-radius:var(--radius);height:210px;width:100%;object-fit:cover;transition:.3s}
.gallery img:hover{transform:scale(1.04)}
.testimonials{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:22px}
.testimonial{background:#fff;padding:26px;border-radius:var(--radius);box-shadow:var(--shadow);border-top:4px solid var(--accent)}
.testimonial .stars{color:var(--accent);letter-spacing:2px}
.stats{background:var(--primary);color:#fff;padding:45px 0}
.stats .features{grid-template-columns:repeat(auto-fit,minmax(150px,1fr))}
.stats h2{color:var(--accent);font-size:2.4rem}
.stats .feature{background:transparent;box-shadow:none;color:#fff}

/* ---------- booking page ---------- */
.booking-layout{display:grid;grid-template-columns:1.2fr 1fr;gap:34px;padding:50px 0}
.panel{background:#fff;padding:28px;border-radius:var(--radius);box-shadow:var(--shadow)}
.summary-row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #eee}
.summary-row.total{font-size:1.2rem;font-weight:700;color:var(--primary);border:0}

/* ---------- tables ---------- */
.table-wrap{overflow-x:auto;background:#fff;border-radius:var(--radius);box-shadow:var(--shadow)}
table{width:100%;border-collapse:collapse;min-width:640px}
th,td{padding:13px 15px;text-align:left;border-bottom:1px solid #eee;font-size:.93rem}
th{background:var(--primary);color:#fff;font-weight:600}
tr:hover td{background:#faf8f3}
.status{padding:4px 12px;border-radius:50px;font-size:.78rem;font-weight:700;text-transform:capitalize;white-space:nowrap}
.status.pending{background:#fff3cd;color:#856404}
.status.confirmed,.status.available{background:#d4edda;color:#155724}
.status.checked_in{background:#cce5ff;color:#004085}
.status.checked_out{background:#e2e3e5;color:#383d41}
.status.cancelled,.status.maintenance{background:#f8d7da;color:#721c24}
.actions{display:flex;gap:6px;flex-wrap:wrap}
.actions form{display:inline}

/* ---------- admin ---------- */
.admin-wrap{display:grid;grid-template-columns:230px 1fr;gap:28px;padding:36px 0}
.sidebar{background:#fff;border-radius:var(--radius);box-shadow:var(--shadow);padding:16px;height:fit-content;position:sticky;top:90px}
.sidebar a{display:block;padding:11px 14px;border-radius:9px;margin-bottom:4px;font-weight:500}
.sidebar a:hover,.sidebar a.active{background:var(--primary);color:#fff}
.stat-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:18px;margin-bottom:30px}
.stat-card{background:#fff;padding:22px;border-radius:var(--radius);box-shadow:var(--shadow);border-left:5px solid var(--accent)}
.stat-card h3{font-size:2rem}
.stat-card p{color:var(--muted);font-size:.9rem}
.admin-title{margin-bottom:20px}
.thumb{width:70px;height:48px;object-fit:cover;border-radius:8px}

/* ---------- footer ---------- */
.site-footer{background:#14243b;color:#c8d0dc;padding-top:50px;margin-top:40px}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1.4fr;gap:30px;padding-bottom:30px}
.site-footer h3,.site-footer h4{color:var(--accent);margin-bottom:10px}
.site-footer a{display:block;margin-bottom:6px}
.site-footer a:hover{color:var(--accent)}
.copyright{text-align:center;border-top:1px solid rgba(255,255,255,.1);padding:16px;font-size:.85rem}

/* ---------- animation ---------- */
.reveal{opacity:0;transform:translateY(30px);transition:.7s}
.reveal.visible{opacity:1;transform:none}

/* ---------- responsive ---------- */
@media(max-width:900px){
  .search-box{grid-template-columns:1fr 1fr}
  .two-col,.booking-layout,.admin-wrap,.footer-grid{grid-template-columns:1fr}
  .sidebar{position:static}
}
@media(max-width:760px){
  .menu-toggle{display:block}
  nav{display:none;position:absolute;top:100%;left:0;right:0;background:var(--primary);flex-direction:column;align-items:stretch;padding:12px}
  nav.open{display:flex}
  .search-box{grid-template-columns:1fr}
}
</style>
<div class="container admin-wrap">
  <?php include '_sidebar.php'; ?>
  <div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
      <h2>Rooms</h2><a class="btn btn-small" href="room-form.php">+ Add Room</a>
    </div>
    <div class="table-wrap"><table>
      <tr><th>Photo</th><th>No.</th><th>Name</th><th>Type</th><th>Price</th><th>Cap.</th><th>Status</th><th>Actions</th></tr>
      <?php foreach ($rooms as $r): ?>
      <tr>
        <td><img class="thumb" src="../assets/images/rooms/<?= e($r['image']) ?>" alt=""></td>
        <td><?= e($r['room_number']) ?></td><td><b><?= e($r['name']) ?></b></td><td><?= e($r['type']) ?></td>
        <td><?= money($r['price']) ?></td><td><?= (int)$r['capacity'] ?></td>
        <td><span class="status <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
        <td><div class="actions">
          <a class="btn btn-small" href="room-form.php?id=<?= (int)$r['id'] ?>">Edit</a>
          <form method="post"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="action" value="toggle">
            <button class="btn btn-small btn-outline">Toggle</button></form>
          <form method="post"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="action" value="delete">
            <button class="btn btn-small btn-danger" data-confirm="Delete this room permanently?">Delete</button></form>
        </div></td>
      </tr>
      <?php endforeach; ?>
    </table></div>
  </div>
</div>
<?php require '../includes/footer.php'; ?>
