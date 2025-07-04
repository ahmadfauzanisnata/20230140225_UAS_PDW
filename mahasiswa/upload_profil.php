<?php
require_once '../koneksi.php';
redirectIfNotLoggedIn();

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Cek apakah file benar-benar diupload
    echo '<pre>';
    var_dump($_FILES);
    echo '</pre>';

    // Pastikan file diupload tanpa error
    if (!isset($_FILES['foto_profil']) || $_FILES['foto_profil']['error'] !== UPLOAD_ERR_OK) {
        die("Error dalam upload file: " . $_FILES['foto_profil']['error'] ?? 'File tidak diupload');
    }

    $targetDir = "../uploads/profil/";
    
    // Buat folder jika belum ada
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Generate nama file unik
    $fileExt = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $fileExt;
    $targetFile = $targetDir . $fileName;

    // Validasi file gambar
    $check = getimagesize($_FILES['foto_profil']['tmp_name']);
    if ($check === false) {
        die("File yang diupload bukan gambar valid");
    }

    // Validasi ukuran file (maks 2MB)
    if ($_FILES['foto_profil']['size'] > 2000000) {
        die("Ukuran file terlalu besar (maks 2MB)");
    }

    // Validasi ekstensi file
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($fileExt), $allowedTypes)) {
        die("Hanya format JPG, JPEG, PNG & GIF yang diizinkan");
    }

    // Pindahkan file ke folder upload
    if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $targetFile)) {
        // Update database
        $stmt = $pdo->prepare("UPDATE users SET foto_profil = ? WHERE id = ?");
        $stmt->execute([$fileName, $_SESSION['user_id']]);
        
        $_SESSION['success'] = "Foto profil berhasil diubah";
        header("Location: profil.php");
        exit();
    } else {
        die("Gagal menyimpan file. Cek permission folder uploads.");
    }
} else {
    die("Metode request tidak valid");
}