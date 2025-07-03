<?php
require_once '../koneksi.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

$title = "Praktikum Saya - SIMPRAK";
include '../header.php';

$user_id = $_SESSION['user_id'];

// Ambil praktikum yang diikuti
$stmt = $pdo->prepare("SELECT p.*, pe.status AS status_pendaftaran 
                      FROM praktikum p
                      JOIN pendaftaran pe ON p.id = pe.praktikum_id
                      WHERE pe.user_id = ?
                      ORDER BY p.semester, p.tahun_ajaran DESC");
$stmt->execute([$user_id]);
$praktikum = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Praktikum Saya</h1>
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
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($praktikum): ?>
                            <?php foreach ($praktikum as $index => $p): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $p['nama_praktikum'] ?></td>
                                    <td><?= $p['semester'] ?></td>
                                    <td><?= $p['tahun_ajaran'] ?></td>
                                    <td>
                                        <?php if ($p['status_pendaftaran'] == 'diterima'): ?>
                                            <span class="badge bg-success">Diterima</span>
                                        <?php elseif ($p['status_pendaftaran'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="praktikum_detail.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Anda belum mengikuti praktikum</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>