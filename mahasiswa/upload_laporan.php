<?php
require_once '../koneksi.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['modul_id']) || !isset($_POST['praktikum_id'])) {
    header('Location: praktikum_saya.php');
    exit();
}

$modul_id = (int)$_POST['modul_id'];
$praktikum_id = (int)$_POST['praktikum_id'];
$user_id = $_SESSION['user_id'];
$catatan = isset($_POST['catatan']) ? sanitize($_POST['catatan']) : '';

// Cek apakah mahasiswa terdaftar di praktikum ini dan diterima
$stmt = $pdo->prepare("SELECT pe.id 
                      FROM pendaftaran pe
                      WHERE pe.praktikum_id = ? AND pe.user_id = ? AND pe.status = 'diterima'");
$stmt->execute([$praktikum_id, $user_id]);
$pendaftaran = $stmt->fetch();

if (!$pendaftaran) {
    $_SESSION['error'] = 'Anda tidak terdaftar atau belum diterima di praktikum ini';
    header("Location: praktikum_detail.php?id=$praktikum_id");
    exit();
}

// Cek apakah modul tersebut termasuk dalam praktikum
$stmt = $pdo->prepare("SELECT id FROM modul WHERE id = ? AND praktikum_id = ?");
$stmt->execute([$modul_id, $praktikum_id]);
$modul = $stmt->fetch();

if (!$modul) {
    $_SESSION['error'] = 'Modul tidak valid untuk praktikum ini';
    header("Location: praktikum_detail.php?id=$praktikum_id");
    exit();
}

// Upload file laporan
if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../uploads/laporan/";
    $file_ext = strtolower(pathinfo($_FILES["file_laporan"]["name"], PATHINFO_EXTENSION));
    $filename = uniqid() . '.' . $file_ext;
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($_FILES["file_laporan"]["tmp_name"], $target_file)) {
        // Simpan ke database
        $stmt = $pdo->prepare("INSERT INTO laporan (pendaftaran_id, modul_id, file_laporan, catatan) VALUES (?, ?, ?, ?)");
        $stmt->execute([$pendaftaran['id'], $modul_id, $filename, $catatan]);
        
        $_SESSION['success'] = 'Laporan berhasil diupload!';
        header("Location: praktikum_detail.php?id=$praktikum_id");
        exit();
    }
}

$_SESSION['error'] = 'Gagal mengupload laporan';
header("Location: praktikum_detail.php?id=$praktikum_id");
exit();