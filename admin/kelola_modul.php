<?php
require_once '../koneksi.php';
redirectIfNotAdmin();

if (!isset($_GET['praktikum_id'])) {
    header('Location: kelola_praktikum.php');
    exit();
}

$praktikum_id = (int)$_GET['praktikum_id'];

// Ambil info praktikum
$stmt = $pdo->prepare("SELECT * FROM praktikum WHERE id = ?");
$stmt->execute([$praktikum_id]);
$praktikum = $stmt->fetch();

if (!$praktikum) {
    header('Location: kelola_praktikum.php');
    exit();
}

// Tambah modul
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_modul'])) {
    $judul_modul = sanitize($_POST['judul_modul']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $urutan = (int)$_POST['urutan'];
    
    // Upload file modul
    $file_modul = '';
    if (isset($_FILES['file_modul']) && $_FILES['file_modul']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/modul/";
        $file_ext = strtolower(pathinfo($_FILES["file_modul"]["name"], PATHINFO_EXTENSION));
        $filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["file_modul"]["tmp_name"], $target_file)) {
            $file_modul = $filename;
        }
    }
    
    if ($file_modul) {
        $stmt = $pdo->prepare("INSERT INTO modul (praktikum_id, judul_modul, deskripsi, file_modul, urutan) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$praktikum_id, $judul_modul, $deskripsi, $file_modul, $urutan]);
        
        $_SESSION['success'] = 'Modul berhasil ditambahkan!';
        header("Location: kelola_modul.php?praktikum_id=$praktikum_id");
        exit();
    } else {
        $_SESSION['error'] = 'Gagal mengupload file modul!';
    }
}

// Hapus modul
if (isset($_GET['hapus'])) {
    $modul_id = (int)$_GET['hapus'];
    
    // Hapus file modul
    $stmt = $pdo->prepare("SELECT file_modul FROM modul WHERE id = ?");
    $stmt->execute([$modul_id]);
    $modul = $stmt->fetch();
    
    if ($modul && $modul['file_modul']) {
        $file_path = "../uploads/modul/" . $modul['file_modul'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM modul WHERE id = ?");
    $stmt->execute([$modul_id]);
    
    $_SESSION['success'] = 'Modul berhasil dihapus!';
    header("Location: kelola_modul.php?praktikum_id=$praktikum_id");
    exit();
}

// Ambil data modul
$stmt = $pdo->prepare("SELECT * FROM modul WHERE praktikum_id = ? ORDER BY urutan");
$stmt->execute([$praktikum_id]);
$modul = $stmt->fetchAll();

$title = "Kelola Modul - " . $praktikum['nama_praktikum'];
include '../header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Kelola Modul: <?= $praktikum['nama_praktikum'] ?></h1>
                <div>
                    <a href="kelola_praktikum.php" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModulModal">
                        <i class="bi bi-plus"></i> Tambah Modul
                    </button>
                </div>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Judul Modul</th>
                            <th>Deskripsi</th>
                            <th>File</th>
                            <th>Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($modul): ?>
                            <?php foreach ($modul as $m): ?>
                                <tr>
                                    <td><?= $m['urutan'] ?></td>
                                    <td><?= $m['judul_modul'] ?></td>
                                    <td><?= substr($m['deskripsi'], 0, 50) ?>...</td>
                                    <td>
                                        <a href="../uploads/modul/<?= $m['file_modul'] ?>" class="btn btn-sm btn-outline-primary" download>
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </td>
                                    <td><?= $m['urutan'] ?></td>
                                    <td>
                                        <a href="edit_modul.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="kelola_modul.php?praktikum_id=<?= $praktikum_id ?>&hapus=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada modul untuk praktikum ini</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- Modal Tambah Modul -->
<div class="modal fade" id="tambahModulModal" tabindex="-1" aria-labelledby="tambahModulModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahModulModalLabel">Tambah Modul</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="praktikum_id" value="<?= $praktikum_id ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul_modul" class="form-label">Judul Modul</label>
                        <input type="text" class="form-control" id="judul_modul" name="judul_modul" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="urutan" class="form-label">Urutan</label>
                        <input type="number" class="form-control" id="urutan" name="urutan" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="file_modul" class="form-label">File Modul (PDF/DOCX)</label>
                        <input class="form-control" type="file" id="file_modul" name="file_modul" accept=".pdf,.doc,.docx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="tambah_modul" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>