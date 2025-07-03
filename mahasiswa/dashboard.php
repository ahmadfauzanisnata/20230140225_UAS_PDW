<?php
require_once '../koneksi.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

$title = "Dashboard Mahasiswa - SIMPRAK";
include '../header.php';

// Hitung jumlah data untuk mahasiswa ini
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE user_id = ? AND status = 'diterima'");
$stmt->execute([$user_id]);
$total_praktikum = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM laporan l
                      JOIN pendaftaran p ON l.pendaftaran_id = p.id
                      WHERE p.user_id = ?");
$stmt->execute([$user_id]);
$total_laporan = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM laporan l
                      JOIN pendaftaran p ON l.pendaftaran_id = p.id
                      WHERE p.user_id = ? AND l.status = 'dinilai'");
$stmt->execute([$user_id]);
$total_dinilai = $stmt->fetchColumn();

// Ambil praktikum yang diikuti
$stmt = $pdo->prepare("SELECT pr.* FROM praktikum pr
                      JOIN pendaftaran pe ON pr.id = pe.praktikum_id
                      WHERE pe.user_id = ? AND pe.status = 'diterima'
                      ORDER BY pr.semester, pr.tahun_ajaran DESC
                      LIMIT 5");
$stmt->execute([$user_id]);
$praktikum = $stmt->fetchAll();

// Ambil laporan terakhir
$stmt = $pdo->prepare("SELECT l.*, p.nama_praktikum, m.judul_modul 
                      FROM laporan l
                      JOIN pendaftaran pf ON l.pendaftaran_id = pf.id
                      JOIN praktikum p ON pf.praktikum_id = p.id
                      JOIN modul m ON l.modul_id = m.id
                      WHERE pf.user_id = ?
                      ORDER BY l.created_at DESC
                      LIMIT 5");
$stmt->execute([$user_id]);
$laporan = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Praktikum Diikuti</h5>
                            <h2 class="card-text"><?= $total_praktikum ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Laporan Dikirim</h5>
                            <h2 class="card-text"><?= $total_laporan ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Laporan Dinilai</h5>
                            <h2 class="card-text"><?= $total_dinilai ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Praktikum Saya</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($praktikum): ?>
                                <div class="list-group">
                                    <?php foreach ($praktikum as $p): ?>
                                        <a href="praktikum_detail.php?id=<?= $p['id'] ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?= $p['nama_praktikum'] ?></h6>
                                                <small><?= $p['semester'] ?></small>
                                            </div>
                                            <small><?= $p['tahun_ajaran'] ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-3 text-end">
                                    <a href="praktikum_saya.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Anda belum mengikuti praktikum</p>
                                <a href="katalog_praktikum.php" class="btn btn-primary">Daftar Praktikum</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Laporan Terakhir</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($laporan): ?>
                                <div class="list-group">
                                    <?php foreach ($laporan as $l): ?>
                                        <a href="lihat_nilai.php?id=<?= $l['id'] ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?= $l['nama_praktikum'] ?></h6>
                                                <small><?= $l['status'] == 'dinilai' ? 'Dinilai' : 'Belum Dinilai' ?></small>
                                            </div>
                                            <p class="mb-1"><?= $l['judul_modul'] ?></p>
                                            <small><?= date('d M Y', strtotime($l['created_at'])) ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-3 text-end">
                                    <a href="praktikum_saya.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Belum ada laporan</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>