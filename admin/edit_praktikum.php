<?php
require_once '../koneksi.php';
redirectIfNotAdmin();

if (!isset($_GET['id'])) {
    header('Location: kelola_praktikum.php');
    exit();
}

$praktikum_id = (int)$_GET['id'];

// Ambil data praktikum
$stmt = $pdo->prepare("SELECT * FROM praktikum WHERE id = ?");
$stmt->execute([$praktikum_id]);
$praktikum = $stmt->fetch();

if (!$praktikum) {
    header('Location: kelola_praktikum.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_praktikum = sanitize($_POST['nama_praktikum']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $semester = sanitize($_POST['semester']);
    $tahun_ajaran = sanitize($_POST['tahun_ajaran']);
    
    // Handle upload thumbnail baru
    $thumbnail = $praktikum['thumbnail'];
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/thumbnails/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        // Hapus thumbnail lama jika ada
        if ($thumbnail && file_exists($target_dir . $thumbnail)) {
            unlink($target_dir . $thumbnail);
        }
        
        $file_ext = strtolower(pathinfo($_FILES["thumbnail"]["name"], PATHINFO_EXTENSION));
        $filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
            $thumbnail = $filename;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE praktikum SET nama_praktikum = ?, deskripsi = ?, semester = ?, tahun_ajaran = ?, thumbnail = ? WHERE id = ?");
    if ($stmt->execute([$nama_praktikum, $deskripsi, $semester, $tahun_ajaran, $thumbnail, $praktikum_id])) {
        $_SESSION['success'] = 'Praktikum berhasil diperbarui';
        header("Location: praktikum_detail.php?id=$praktikum_id");
        exit();
    } else {
        $_SESSION['error'] = 'Gagal memperbarui praktikum';
    }
}

$title = "Edit Praktikum - " . $praktikum['nama_praktikum'];
include '../header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Praktikum</h1>
                <a href="praktikum_detail.php?id=<?= $praktikum_id ?>" class="btn btn-secondary">
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
                            <label for="nama_praktikum" class="form-label">Nama Praktikum</label>
                            <input type="text" class="form-control" id="nama_praktikum" name="nama_praktikum" 
                                   value="<?= htmlspecialchars($praktikum['nama_praktikum']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required><?= htmlspecialchars($praktikum['deskripsi']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="semester" class="form-label">Semester</label>
                                <select class="form-select" id="semester" name="semester" required>
                                    <?php for ($i = 1; $i <= 8; $i++): ?>
                                        <option value="<?= $i ?>" <?= $praktikum['semester'] == $i ? 'selected' : '' ?>>
                                            Semester <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                                <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" 
                                       value="<?= htmlspecialchars($praktikum['tahun_ajaran']) ?>" 
                                       placeholder="Contoh: 2023/2024" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Thumbnail (Opsional)</label>
                            <?php if ($praktikum['thumbnail']): ?>
                                <div class="mb-2">
                                    <img src="../uploads/thumbnails/<?= htmlspecialchars($praktikum['thumbnail']) ?>" 
                                         class="img-thumbnail" 
                                         width="150" 
                                         alt="Thumbnail">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="hapus_thumbnail" name="hapus_thumbnail">
                                        <label class="form-check-label" for="hapus_thumbnail">
                                            Hapus thumbnail saat ini
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input class="form-control" type="file" id="thumbnail" name="thumbnail" accept="image/*">
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