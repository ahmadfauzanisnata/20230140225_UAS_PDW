<?php
require_once '../koneksi.php';
redirectIfNotAdmin();

$title = "Dashboard Admin - SIMPRAK";
include '../header.php';

// Hitung jumlah data
$stmt = $pdo->query("SELECT COUNT(*) FROM praktikum");
$total_praktikum = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'mahasiswa'");
$total_mahasiswa = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status = 'diterima'");
$total_pendaftaran = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM laporan WHERE status = 'dikirim'");
$total_laporan_baru = $stmt->fetchColumn();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Praktikum</h5>
                            <h2 class="card-text"><?= $total_praktikum ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Mahasiswa</h5>
                            <h2 class="card-text"><?= $total_mahasiswa ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pendaftaran</h5>
                            <h2 class="card-text"><?= $total_pendaftaran ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h5 class="card-title">Laporan Baru</h5>
                            <h2 class="card-text"><?= $total_laporan_baru ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Praktikum Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt = $pdo->query("SELECT * FROM praktikum ORDER BY created_at DESC LIMIT 5");
                            $praktikum = $stmt->fetchAll();
                            
                            if ($praktikum): ?>
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
                            <?php else: ?>
                                <p class="text-muted">Belum ada praktikum</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Laporan Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt = $pdo->query("SELECT l.*, u.nama_lengkap, p.nama_praktikum 
                                                FROM laporan l
                                                JOIN pendaftaran pf ON l.pendaftaran_id = pf.id
                                                JOIN praktikum p ON pf.praktikum_id = p.id
                                                JOIN users u ON pf.user_id = u.id
                                                ORDER BY l.created_at DESC LIMIT 5");
                            $laporan = $stmt->fetchAll();
                            
                            if ($laporan): ?>
                                <div class="list-group">
                                    <?php foreach ($laporan as $l): ?>
                                        <a href="beri_nilai.php?id=<?= $l['id'] ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?= $l['nama_praktikum'] ?></h6>
                                                <small><?= $l['status'] ?></small>
                                            </div>
                                            <p class="mb-1"><?= $l['nama_lengkap'] ?></p>
                                            <small><?= date('d M Y', strtotime($l['created_at'])) ?></small>
                                        </a>
                                    <?php endforeach; ?>
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