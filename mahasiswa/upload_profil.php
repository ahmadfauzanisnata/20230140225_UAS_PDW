<?php
require_once '../koneksi.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_profil'])) {
    $targetDir = "../uploads/profil/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES['foto_profil']['name']);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Validasi
    $check = getimagesize($_FILES['foto_profil']['tmp_name']);
    if ($check === false) {
        die("File bukan gambar");
    }

    // Ukuran maksimal 2MB
    if ($_FILES['foto_profil']['size'] > 2000000) {
        die("Ukuran file terlalu besar (maks 2MB)");
    }

    // Format yang diizinkan
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        die("Hanya format JPG, JPEG, PNG & GIF yang diizinkan");
    }

    // Upload file
    if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $targetFile)) {
        // Update database
        $stmt = $pdo->prepare("UPDATE users SET foto_profil = ? WHERE id = ?");
        $stmt->execute([$fileName, $_SESSION['user_id']]);
        
        $_SESSION['success'] = "Foto profil berhasil diubah";
        header("Location: profil.php");
        exit();
    } else {
        die("Gagal mengupload file");
    }
}