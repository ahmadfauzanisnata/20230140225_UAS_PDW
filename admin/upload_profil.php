<?php
require_once '../koneksi.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi apakah file diupload
    if (!isset($_FILES['foto_profil']) || $_FILES['foto_profil']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "Silakan pilih file gambar terlebih dahulu";
        header("Location: profil.php");
        exit();
    }

    // Lanjutkan proses upload jika file ada
    $targetDir = "../uploads/profil/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileExt = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $fileExt;
    $targetFile = $targetDir . $fileName;

    // Validasi gambar
    $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($fileExt), $validExtensions)) {
        $_SESSION['error'] = "Format file tidak didukung. Gunakan JPG, PNG, atau GIF.";
        header("Location: profil.php");
        exit();
    }

    if ($_FILES['foto_profil']['size'] > 2000000) {
        $_SESSION['error'] = "Ukuran file terlalu besar (maks 2MB)";
        header("Location: profil.php");
        exit();
    }

    if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $targetFile)) {
        $stmt = $pdo->prepare("UPDATE users SET foto_profil = ? WHERE id = ?");
        $stmt->execute([$fileName, $_SESSION['user_id']]);
        
        $_SESSION['success'] = "Foto profil berhasil diperbarui!";
        header("Location: profil.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal mengupload file. Coba lagi.";
        header("Location: profil.php");
        exit();
    }
}

// Jika akses langsung ke file
header("Location: profil.php");
exit();