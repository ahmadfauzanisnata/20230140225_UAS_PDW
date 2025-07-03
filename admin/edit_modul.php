<?php
require_once '../koneksi.php';
redirectIfNotAdmin();

if (!isset($_GET['id'])) {
    header('Location: kelola_modul.php');
    exit();
}

$modul_id = (int)$_GET['id'];

// Ambil data modul
$stmt = $pdo->prepare("SELECT * FROM modul WHERE id = ?");
$stmt->execute([$modul_id]);
$modul = $stmt->fetch();

if (!$modul) {
    header('Location: kelola_modul.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = sanitize($_POST['judul']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $urutan = (int)$_POST['urutan'];
    
    // Handle upload file baru
    $file_path = $modul['file_path'];
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/modul/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        // Hapus file lama jika ada
        if ($file_path && file_exists($target_dir . basename($file_path))) {
            unlink($target_dir . basename($file_path));
        }
        
        $file_ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_path = "uploads/modul/" . $filename;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE modul_praktikum SET judul = ?, deskripsi = ?, file_path = ?, urutan = ? WHERE id = ?");
    if ($stmt->execute([$judul, $deskripsi, $file_path, $urutan, $modul_id])) {
        $_SESSION['success'] = 'Modul berhasil diperbarui';
        header("Location: kelola_modul.php");
        exit();
    } else {
        $_SESSION['error'] = 'Gagal memperbarui modul';
    }
}

$title = "Edit Modul - " . $modul['judul'];
include '../header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Modul</h1>
                <a href="kelola_modul.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Modul</label>
                            <input type="text" class="form-control" id="judul" name="judul" 
                                   value="<?= htmlspecialchars($modul['judul']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"><?= htmlspecialchars($modul['deskripsi']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="urutan" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="urutan" name="urutan" 
                                   value="<?= htmlspecialchars($modul['urutan']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">File Modul</label>
                            <?php if ($modul['file_path']): ?>
                                <div class="mb-2">
                                    <a href="../<?= htmlspecialchars($modul['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-pdf"></i> Lihat File Saat Ini
                                    </a>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="hapus_file" name="hapus_file">
                                        <label class="form-check-label" for="hapus_file">
                                            Hapus file saat ini
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input class="form-control" type="file" id="file" name="file" accept=".pdf,.doc,.docx">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah file</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>