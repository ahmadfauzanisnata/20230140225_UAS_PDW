<?php
require_once '../koneksi.php';
redirectIfNotAdmin();

$title = "Kelola Praktikum - SIMPRAK";
include '../header.php';

// Tambah praktikum
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_praktikum'])) {
    $nama_praktikum = sanitize($_POST['nama_praktikum']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $semester = sanitize($_POST['semester']);
    $tahun_ajaran = sanitize($_POST['tahun_ajaran']);
    
    // Upload thumbnail
    $thumbnail = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/thumbnails/";
        $file_ext = strtolower(pathinfo($_FILES["thumbnail"]["name"], PATHINFO_EXTENSION));
        $filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
            $thumbnail = $filename;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO praktikum (nama_praktikum, deskripsi, semester, tahun_ajaran, thumbnail) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nama_praktikum, $deskripsi, $semester, $tahun_ajaran, $thumbnail]);
    
    $_SESSION['success'] = 'Praktikum berhasil ditambahkan!';
    header('Location: kelola_praktikum.php');
    exit();
}

// Hapus praktikum
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Hapus thumbnail jika ada
    $stmt = $pdo->prepare("SELECT thumbnail FROM praktikum WHERE id = ?");
    $stmt->execute([$id]);
    $praktikum = $stmt->fetch();
    
    if ($praktikum && $praktikum['thumbnail']) {
        $file_path = "../uploads/thumbnails/" . $praktikum['thumbnail'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM praktikum WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success'] = 'Praktikum berhasil dihapus!';
    header('Location: kelola_praktikum.php');
    exit();
}

// Ambil data praktikum
$stmt = $pdo->query("SELECT * FROM praktikum ORDER BY semester, tahun_ajaran DESC");
$praktikum = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Kelola Praktikum</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPraktikumModal">
                    <i class="bi bi-plus"></i> Tambah Praktikum
                </button>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Praktikum</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($praktikum): ?>
                            <?php foreach ($praktikum as $index => $p): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($p['thumbnail']): ?>
                                                <img src="../uploads/thumbnails/<?= $p['thumbnail'] ?>" alt="Thumbnail" class="rounded me-2" width="40">
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0"><?= $p['nama_praktikum'] ?></h6>
                                                <small class="text-muted"><?= substr($p['deskripsi'], 0, 50) ?>...</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $p['semester'] ?></td>
                                    <td><?= $p['tahun_ajaran'] ?></td>
                                    <td>
                                        <a href="praktikum_detail.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="kelola_modul.php?praktikum_id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-journal-text"></i>
                                        </a>
                                        <a href="kelola_praktikum.php?hapus=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada praktikum</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- Modal Tambah Praktikum -->
<div class="modal fade" id="tambahPraktikumModal" tabindex="-1" aria-labelledby="tambahPraktikumModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPraktikumModalLabel">Tambah Praktikum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_praktikum" class="form-label">Nama Praktikum</label>
                        <input type="text" class="form-control" id="nama_praktikum" name="nama_praktikum" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-select" id="semester" name="semester" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" placeholder="2023/2024" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail (Opsional)</label>
                        <input class="form-control" type="file" id="thumbnail" name="thumbnail" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="tambah_praktikum" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>