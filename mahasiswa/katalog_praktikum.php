<?php
require_once '../koneksi.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

$title = "Katalog Praktikum - SIMPRAK";
include '../header.php';

// Filter
$semester = isset($_GET['semester']) ? sanitize($_GET['semester']) : '';
$tahun_ajaran = isset($_GET['tahun_ajaran']) ? sanitize($_GET['tahun_ajaran']) : '';

// Query untuk praktikum
$sql = "SELECT p.*, 
       (SELECT COUNT(*) FROM pendaftaran WHERE praktikum_id = p.id AND user_id = ?) AS sudah_daftar,
       (SELECT status FROM pendaftaran WHERE praktikum_id = p.id AND user_id = ? LIMIT 1) AS status_daftar
       FROM praktikum p WHERE 1=1";
$params = [$_SESSION['user_id'], $_SESSION['user_id']];

if (!empty($semester)) {
    $sql .= " AND p.semester = ?";
    $params[] = $semester;
}

if (!empty($tahun_ajaran)) {
    $sql .= " AND p.tahun_ajaran = ?";
    $params[] = $tahun_ajaran;
}

$sql .= " ORDER BY p.semester, p.tahun_ajaran DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$praktikum = $stmt->fetchAll();

// Daftar semester dan tahun ajaran untuk filter
$stmt = $pdo->query("SELECT DISTINCT semester FROM praktikum ORDER BY semester");
$semesters = $stmt->fetchAll();

$stmt = $pdo->query("SELECT DISTINCT tahun_ajaran FROM praktikum ORDER BY tahun_ajaran DESC");
$tahun_ajarans = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Katalog Praktikum</h1>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-select" id="semester" name="semester">
                                <option value="">Semua Semester</option>
                                <?php foreach ($semesters as $s): ?>
                                    <option value="<?= $s['semester'] ?>" <?= $semester == $s['semester'] ? 'selected' : '' ?>>
                                        Semester <?= $s['semester'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran">
                                <option value="">Semua Tahun</option>
                                <?php foreach ($tahun_ajarans as $t): ?>
                                    <option value="<?= $t['tahun_ajaran'] ?>" <?= $tahun_ajaran == $t['tahun_ajaran'] ? 'selected' : '' ?>>
                                        <?= $t['tahun_ajaran'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="row">
                <?php if ($praktikum): ?>
                    <?php foreach ($praktikum as $p): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if ($p['thumbnail']): ?>
                                    <img src="../uploads/thumbnails/<?= $p['thumbnail'] ?>" class="card-img-top" alt="<?= $p['nama_praktikum'] ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= $p['nama_praktikum'] ?></h5>
                                    <p class="card-text text-muted"><?= substr($p['deskripsi'], 0, 100) ?>...</p>
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item">
                                            <small>Semester: <?= $p['semester'] ?></small>
                                        </li>
                                        <li class="list-group-item">
                                            <small>Tahun Ajaran: <?= $p['tahun_ajaran'] ?></small>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <?php if ($p['sudah_daftar']): ?>
                                        <?php if ($p['status_daftar'] == 'pending'): ?>
                                            <button class="btn btn-warning w-100" disabled>Menunggu Persetujuan</button>
                                        <?php elseif ($p['status_daftar'] == 'diterima'): ?>
                                            <a href="praktikum_detail.php?id=<?= $p['id'] ?>" class="btn btn-success w-100">Sudah Diterima</a>
                                        <?php else: ?>
                                            <button class="btn btn-danger w-100" disabled>Ditolak</button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <form method="POST" action="daftar_praktikum.php">
                                            <input type="hidden" name="praktikum_id" value="<?= $p['id'] ?>">
                                            <button type="submit" class="btn btn-primary w-100">Daftar Praktikum</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">Tidak ada praktikum yang tersedia</div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>