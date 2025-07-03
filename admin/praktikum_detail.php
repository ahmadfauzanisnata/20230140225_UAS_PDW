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

// Ambil modul terkait
$stmt = $pdo->prepare("SELECT * FROM modul WHERE praktikum_id = ? ORDER BY urutan");
$stmt->execute([$praktikum_id]);
$modul = $stmt->fetchAll();

// Ambil mahasiswa yang terdaftar
$stmt = $pdo->prepare("SELECT u.id, u.nama_lengkap, u.email, p.status 
                      FROM users u
                      JOIN pendaftaran p ON u.id = p.user_id
                      WHERE p.praktikum_id = ?");
$stmt->execute([$praktikum_id]);
$peserta = $stmt->fetchAll();

$title = "Detail Praktikum - " . $praktikum['nama_praktikum'];
include '../header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Detail Praktikum: <?= htmlspecialchars($praktikum['nama_praktikum']) ?></h1>
                <div>
                    <a href="kelola_praktikum.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <a href="edit_praktikum.php?id=<?= $praktikum_id ?>" class="btn btn-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Deskripsi Praktikum</h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($praktikum['deskripsi'])) ?></p>
                            
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Semester:</strong> <?= htmlspecialchars($praktikum['semester']) ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Tahun Ajaran:</strong> <?= htmlspecialchars($praktikum['tahun_ajaran']) ?>
                                </li>
                                <?php if ($praktikum['thumbnail']): ?>
                                <li class="list-group-item">
                                    <strong>Thumbnail:</strong> 
                                    <img src="../uploads/thumbnails/<?= htmlspecialchars($praktikum['thumbnail']) ?>" 
                                         class="img-thumbnail mt-2" 
                                         width="150" 
                                         alt="Thumbnail">
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Statistik</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Modul Praktikum
                                    <span class="badge bg-primary rounded-pill"><?= count($modul) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Peserta Terdaftar
                                    <span class="badge bg-success rounded-pill"><?= count($peserta) ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Modul Praktikum</h5>
                            <a href="kelola_modul.php?praktikum_id=<?= $praktikum_id ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus"></i> Tambah Modul
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if ($modul): ?>
                                <div class="list-group">
                                    <?php foreach ($modul as $m): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h6><?= htmlspecialchars($m['judul_modul']) ?></h6>
                                                    <small><?= substr(htmlspecialchars($m['deskripsi']), 0, 50) ?>...</small>
                                                </div>
                                                <div>
                                                    <a href="../uploads/modul/<?= htmlspecialchars($m['file_modul']) ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       download>
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <a href="kelola_modul.php?praktikum_id=<?= $praktikum_id ?>&hapus=<?= $m['id'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Hapus modul ini?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">Belum ada modul untuk praktikum ini</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Daftar Peserta</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($peserta): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($peserta as $index => $p): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <?= htmlspecialchars($p['nama_lengkap']) ?>
                                                        <br><small><?= htmlspecialchars($p['email']) ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if ($p['status'] == 'diterima'): ?>
                                                            <span class="badge bg-success">Diterima</span>
                                                        <?php elseif ($p['status'] == 'pending'): ?>
                                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Ditolak</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="lihat_laporan.php?user_id=<?= $p['id'] ?>&praktikum_id=<?= $praktikum_id ?>" 
                                                           class="btn btn-sm btn-info"
                                                           title="Lihat Laporan">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">Belum ada peserta yang terdaftar</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>