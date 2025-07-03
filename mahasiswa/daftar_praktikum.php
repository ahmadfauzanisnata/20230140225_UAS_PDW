<?php
require_once('../koneksi.php');
redirectIfNotLoggedIn();

if (isAdmin()) {
    header('Location: admin/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['praktikum_id'])) {
    header('Location: katalog_praktikum.php');
    exit();
}

$praktikum_id = (int)$_POST['praktikum_id'];
$user_id = $_SESSION['user_id'];

// Cek apakah praktikum exists
$stmt = $pdo->prepare("SELECT id FROM praktikum WHERE id = ?");
$stmt->execute([$praktikum_id]);
if (!$stmt->fetch()) {
    $_SESSION['error'] = 'Praktikum tidak valid';
    header('Location: katalog_praktikum.php');
    exit();
}

// Cek apakah sudah terdaftar
$stmt = $pdo->prepare("SELECT id FROM pendaftaran WHERE user_id = ? AND praktikum_id = ?");
$stmt->execute([$user_id, $praktikum_id]);
if ($stmt->fetch()) {
    $_SESSION['error'] = 'Anda sudah terdaftar di praktikum ini';
    header('Location: katalog_praktikum.php');
    exit();
}

// Daftarkan praktikum
$stmt = $pdo->prepare("INSERT INTO pendaftaran (user_id, praktikum_id, status) VALUES (?, ?, 'pending')");
if ($stmt->execute([$user_id, $praktikum_id])) {
    $_SESSION['success'] = 'Pendaftaran berhasil! Menunggu persetujuan admin.';
} else {
    $_SESSION['error'] = 'Gagal mendaftar praktikum';
}

header('Location: katalog_praktikum.php');
exit();
?>