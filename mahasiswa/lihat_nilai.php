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

$laporan_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil data laporan
$stmt = $pdo->prepare("SELECT l.*, p.nama_praktikum, m.judul_modul, u.nama_lengkap 
                      FROM laporan l
                      JOIN pendaftaran pf ON l.pendaftaran_id = pf.id
                      JOIN praktikum p ON pf.praktikum_id = p.id
                      JOIN modul m ON l.modul_id = m.id
                      JOIN users u ON pf.user_id = u.id
                      WHERE l.id = ? AND pf.user_id = ?");
$stmt->execute([$laporan_id, $user_id]);
$laporan = $stmt->fetch();

if (!$laporan) {
    header('Location: praktikum_saya.php');
    exit();
}

$title = "Detail Nilai - " . $laporan['nama_praktikum'];
include '../header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Detail Nilai Laporan</h1>
                <a href="praktikum_detail.php?id=<?= $laporan['id'] ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Informasi Laporan</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Praktikum</th>
                                    <td><?= $laporan['nama_praktikum'] ?></td>
                                </tr>
                                <tr>
                                    <th>Modul</th>
                                    <td><?= $laporan['judul_modul'] ?></td>
                                </tr>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <td><?= $laporan['nama_lengkap'] ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Upload</th>
                                    <td><?= date('d M Y H:i', strtotime($laporan['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-<?= $laporan['status'] == 'dinilai' ? 'success' : 'warning' ?>">
                                            <?= $laporan['status'] == 'dinilai' ? 'Dinilai' : 'Belum Dinilai' ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php if (!empty($laporan['catatan'])): ?>
                                    <tr>
                                        <th>Catatan</th>
                                        <td><?= $laporan['catatan'] ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                            
                            <div class="mt-3">
                                <a href="../uploads/laporan/<?= $laporan['file_laporan'] ?>" class="btn btn-primary" download>
                                    <i class="bi bi-download"></i> Download Laporan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Penilaian</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($laporan['status'] == 'dinilai'): ?>
                                <div class="mb-4">
                                    <h3 class="text-center <?= $laporan['nilai'] >= 75 ? 'text-success' : ($laporan['nilai'] >= 50 ? 'text-warning' : 'text-danger') ?>">
                                        <?= $laporan['nilai'] ?>
                                    </h3>
                                </div>
                                
                                <?php if (!empty($laporan['feedback'])): ?>
                                    <div class="mb-3">
                                        <h6>Feedback:</h6>
                                        <div class="p-3 bg-light rounded">
                                            <?= nl2br($laporan['feedback']) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Nilai dan feedback diberikan oleh asisten praktikum.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Laporan Anda belum dinilai oleh asisten.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>