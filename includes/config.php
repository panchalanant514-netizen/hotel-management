<?php
/* ------------------------------------------------------------
   config.php  -  database connection + helper functions
   Every page includes this file first.
------------------------------------------------------------ */
ob_start();   // output buffering: lets us redirect() even after HTML has started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- Database settings (XAMPP defaults) ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'hotel_db');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'Grand Horizon');

// Links are RELATIVE, so no folder-path detection is needed.
// Pages in the main folder use "."  and pages inside /admin use ".."
$inAdmin = (basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'admin');
define('BASE_URL', $inAdmin ? '..' : '.');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die('<h2 style="font-family:sans-serif">Database connection failed.</h2>
         <p style="font-family:sans-serif">Start MySQL in XAMPP and import <b>database/hotel_db.sql</b>.</p>');
}

/* ---------- helper functions ---------- */
function e($text) {                      // safe output (prevents XSS)
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}
function redirect($path) {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}
function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdmin()    { return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin'; }

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        flash('Please login to continue.', 'error');
        redirect('login.php');
    }
}
function requireAdmin() {
    if (!isAdmin()) {
        flash('Admin access only.', 'error');
        redirect('login.php');
    }
}
function flash($msg, $type = 'success') { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; }
function showFlash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash']; unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['type']) . '">' . e($f['msg']) . '</div>';
    }
}
function money($n) { return '₹' . number_format((float)$n, 0); }

// CSRF protection for forms
function csrfToken() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrfField() { return '<input type="hidden" name="csrf" value="' . csrfToken() . '">'; }
function checkCsrf() {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        die('Invalid form submission (CSRF check failed). Go back and try again.');
    }
}

// Is a room free between two dates?  (overlap check)
function isRoomAvailable($pdo, $roomId, $in, $out) {
    $sql = "SELECT COUNT(*) FROM bookings
            WHERE room_id = ? AND status IN ('pending','confirmed','checked_in')
              AND check_in < ? AND check_out > ?";
    $st = $pdo->prepare($sql);
    $st->execute([$roomId, $out, $in]);
    return $st->fetchColumn() == 0;
}
