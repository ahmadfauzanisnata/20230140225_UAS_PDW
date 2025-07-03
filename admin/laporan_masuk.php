<?php
require_once '../koneksi.php';
redirectIfNotAdmin();

$title = "Laporan Masuk - SIMPRAK";
include '../header.php';

// Filter berdasarkan praktikum
$praktikum_id = isset($_GET['praktikum_id']) ? (int)$_GET['praktikum_id'] : 0;
$status = isset($_GET['status']) ? sanitize($_GET['status']) : 'dikirim';

// Query untuk laporan
$sql = "SELECT l.*, u.nama_lengkap, p.nama_praktikum, m.judul_modul 
        FROM laporan l
        JOIN pendaftaran pf ON l.pendaftaran_id = pf.id
        JOIN praktikum p ON pf.praktikum_id = p.id
        JOIN users u ON pf.user_id = u.id
        JOIN modul m ON l.modul_id = m.id
        WHERE l.status = ?";

$params = [$status];

if ($praktikum_id > 0) {
    $sql .= " AND pf.praktikum_id = ?";
    $params[] = $praktikum_id;
}

$sql .= " ORDER BY l.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$laporan = $stmt->fetchAll();

// Ambil daftar praktikum untuk filter
$stmt = $pdo->query("SELECT * FROM praktikum ORDER BY nama_praktikum");
$praktikum_list = $stmt->fetchAll();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Laporan Masuk</h1>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label for="praktikum_id" class="form-label">Filter Praktikum</label>
                            <select class="form-select" id="praktikum_id" name="praktikum_id">
                                <option value="0">Semua Praktikum</option>
                                <?php foreach ($praktikum_list as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $praktikum_id == $p['id'] ? 'selected' : '' ?>>
                                        <?= $p['nama_praktikum'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="dikirim" <?= $status == 'dikirim' ? 'selected' : '' ?>>Belum Dinilai</option>
                                <option value="dinilai" <?= $status == 'dinilai' ? 'selected' : '' ?>>Sudah Dinilai</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mahasiswa</th>
                            <th>Praktikum</th>
                            <th>Modul</th>
                            <th>Tanggal Upload</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($laporan): ?>
                            <?php foreach ($laporan as $index => $l): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $l['nama_lengkap'] ?></td>
                                    <td><?= $l['nama_praktikum'] ?></td>
                                    <td><?= $l['judul_modul'] ?></td>
                                    <td><?= date('d M Y H:i', strtotime($l['created_at'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $l['status'] == 'dinilai' ? 'success' : 'warning' ?>">
                                            <?= $l['status'] == 'dinilai' ? 'Dinilai' : 'Belum Dinilai' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="beri_nilai.php?id=<?= $l['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada laporan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>