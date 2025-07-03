<?php
require_once '../koneksi.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: praktikum_saya.php');
    exit();
}

$praktikum_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Cek apakah mahasiswa terdaftar di praktikum ini
$stmt = $pdo->prepare("SELECT p.*, pe.status AS status_pendaftaran 
                      FROM praktikum p
                      JOIN pendaftaran pe ON p.id = pe.praktikum_id
                      WHERE p.id = ? AND pe.user_id = ?");
$stmt->execute([$praktikum_id, $user_id]);
$praktikum = $stmt->fetch();

if (!$praktikum) {
    header('Location: praktikum_saya.php');
    exit();
}

// Ambil modul praktikum
$stmt = $pdo->prepare("SELECT * FROM modul WHERE praktikum_id = ? ORDER BY urutan");
$stmt->execute([$praktikum_id]);
$modul = $stmt->fetchAll();

// Ambil laporan untuk praktikum ini
$stmt = $pdo->prepare("SELECT l.*, m.judul_modul 
                      FROM laporan l
                      JOIN modul m ON l.modul_id = m.id
                      JOIN pendaftaran p ON l.pendaftaran_id = p.id
                      WHERE p.praktikum_id = ? AND p.user_id = ?
                      ORDER BY l.created_at DESC");
$stmt->execute([$praktikum_id, $user_id]);
$laporan = $stmt->fetchAll();

$title = $praktikum['nama_praktikum'] . " - SIMPRAK";
include '../header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?= $praktikum['nama_praktikum'] ?></h1>
                <a href="praktikum_saya.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Deskripsi Praktikum</h5>
                            <p class="card-text"><?= $praktikum['deskripsi'] ?></p>
                            
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Semester:</strong> <?= $praktikum['semester'] ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Tahun Ajaran:</strong> <?= $praktikum['tahun_ajaran'] ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Status Pendaftaran:</strong> 
                                    <?php if ($praktikum['status_pendaftaran'] == 'diterima'): ?>
                                        <span class="badge bg-success">Diterima</span>
                                    <?php elseif ($praktikum['status_pendaftaran'] == 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <?php if ($praktikum['thumbnail']): ?>
                                <img src="../uploads/thumbnails/<?= $praktikum['thumbnail'] ?>" alt="Thumbnail" class="img-fluid rounded mb-3">
                            <?php endif; ?>
                            <h5 class="card-title"><?= $praktikum['nama_praktikum'] ?></h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Modul Praktikum</h5>
                </div>
                <div class="card-body">
                    <?php if ($modul): ?>
                        <div class="list-group">
                            <?php foreach ($modul as $m): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= $m['judul_modul'] ?></h6>
                                        <small>Modul #<?= $m['urutan'] ?></small>
                                    </div>
                                    <p class="mb-1"><?= substr($m['deskripsi'], 0, 100) ?>...</p>
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <a href="../uploads/modul/<?= $m['file_modul'] ?>" class="btn btn-sm btn-outline-primary" download>
                                            <i class="bi bi-download"></i> Download Modul
                                        </a>
                                        <?php if ($praktikum['status_pendaftaran'] == 'diterima'): ?>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadLaporanModal<?= $m['id'] ?>">
                                                <i class="bi bi-upload"></i> Upload Laporan
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Modal Upload Laporan -->
                                <div class="modal fade" id="uploadLaporanModal<?= $m['id'] ?>" tabindex="-1" aria-labelledby="uploadLaporanModalLabel<?= $m['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="uploadLaporanModalLabel<?= $m['id'] ?>">Upload Laporan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="upload_laporan.php" enctype="multipart/form-data">
                                                <input type="hidden" name="modul_id" value="<?= $m['id'] ?>">
                                                <input type="hidden" name="praktikum_id" value="<?= $praktikum_id ?>">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="file_laporan<?= $m['id'] ?>" class="form-label">File Laporan (PDF/DOCX)</label>
                                                        <input class="form-control" type="file" id="file_laporan<?= $m['id'] ?>" name="file_laporan" accept=".pdf,.doc,.docx" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="catatan<?= $m['id'] ?>" class="form-label">Catatan (Opsional)</label>
                                                        <textarea class="form-control" id="catatan<?= $m['id'] ?>" name="catatan" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-primary">Upload</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Belum ada modul untuk praktikum ini</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>Riwayat Laporan</h5>
                </div>
                <div class="card-body">
                    <?php if ($laporan): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Modul</th>
                                        <th>Tanggal Upload</th>
                                        <th>Status</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($laporan as $index => $l): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= $l['judul_modul'] ?></td>
                                            <td><?= date('d M Y H:i', strtotime($l['created_at'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $l['status'] == 'dinilai' ? 'success' : 'warning' ?>">
                                                    <?= $l['status'] == 'dinilai' ? 'Dinilai' : 'Belum Dinilai' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $l['nilai'] ? $l['nilai'] : '-' ?>
                                            </td>
                                            <td>
                                                <a href="lihat_nilai.php?id=<?= $l['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Belum ada laporan untuk praktikum ini</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>