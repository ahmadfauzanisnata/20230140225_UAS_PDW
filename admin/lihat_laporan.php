<?php
require_once '../koneksi.php';
redirectIfNotAdmin();

if (!isset($_GET['user_id']) || !isset($_GET['praktikum_id'])) {
    header('Location: laporan_masuk.php');
    exit();
}

$user_id = (int)$_GET['user_id'];
$praktikum_id = (int)$_GET['praktikum_id'];

// Ambil data mahasiswa
$stmt = $pdo->prepare("SELECT u.*, p.status FROM users u JOIN pendaftaran p ON u.id = p.user_id WHERE u.id = ? AND p.praktikum_id = ?");
$stmt->execute([$user_id, $praktikum_id]);
$mahasiswa = $stmt->fetch();

// Ambil data praktikum
$stmt = $pdo->prepare("SELECT * FROM praktikum WHERE id = ?");
$stmt->execute([$praktikum_id]);
$praktikum = $stmt->fetch();

// Ambil laporan mahasiswa
$stmt = $pdo->prepare("SELECT l.*, m.judul_modul FROM laporan l 
                      JOIN modul m ON l.modul_id = m.id 
                      JOIN pendaftaran p ON l.pendaftaran_id = p.id
                      WHERE p.user_id = ? AND p.praktikum_id = ?
                      ORDER BY l.created_at DESC");
$stmt->execute([$user_id, $praktikum_id]);
$laporan = $stmt->fetchAll();

$title = "Laporan " . $mahasiswa['nama_lengkap'] . " - " . $praktikum['nama_praktikum'];
include '../header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Laporan Mahasiswa</h1>
                <a href="praktikum_detail.php?id=<?= $praktikum_id ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nama Mahasiswa</th>
                                    <td><?= htmlspecialchars($mahasiswa['nama_lengkap']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= htmlspecialchars($mahasiswa['email']) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Praktikum</th>
                                    <td><?= htmlspecialchars($praktikum['nama_praktikum']) ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($mahasiswa['status'] == 'diterima'): ?>
                                            <span class="badge bg-success">Diterima</span>
                                        <?php elseif ($mahasiswa['status'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Daftar Laporan</h5>
                </div>
                <div class="card-body">
                    <?php if ($laporan): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Modul</th>
                                        <th>Tanggal Upload</th>
                                        <th>File</th>
                                        <th>Status</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($laporan as $index => $l): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($l['judul_modul']) ?></td>
                                            <td><?= date('d M Y H:i', strtotime($l['created_at'])) ?></td>
                                            <td>
                                                <a href="../uploads/laporan/<?= htmlspecialchars($l['file_laporan']) ?>" 
                                                   class="btn btn-sm btn-outline-primary"
                                                   download>
                                                    <i class="bi bi-download"></i> Unduh
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ($l['status'] == 'dinilai'): ?>
                                                    <span class="badge bg-success">Dinilai</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Belum Dinilai</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $l['nilai'] ?? '-' ?></td>
                                            <td>
                                                <a href="beri_nilai.php?id=<?= $l['id'] ?>" 
                                                   class="btn btn-sm btn-primary"
                                                   title="Beri Nilai">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">Belum ada laporan dari mahasiswa ini</div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>