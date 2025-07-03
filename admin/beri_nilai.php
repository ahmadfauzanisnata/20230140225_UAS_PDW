<?php
require_once '../koneksi.php';
redirectIfNotAdmin();

if (!isset($_GET['id'])) {
    header('Location: laporan_masuk.php');
    exit();
}

$laporan_id = (int)$_GET['id'];

// Ambil data laporan
$stmt = $pdo->prepare("SELECT l.*, u.nama_lengkap, p.nama_praktikum, m.judul_modul, pf.id as pendaftaran_id
                      FROM laporan l
                      JOIN pendaftaran pf ON l.pendaftaran_id = pf.id
                      JOIN praktikum p ON pf.praktikum_id = p.id
                      JOIN users u ON pf.user_id = u.id
                      JOIN modul m ON l.modul_id = m.id
                      WHERE l.id = ?");
$stmt->execute([$laporan_id]);
$laporan = $stmt->fetch();

if (!$laporan) {
    header('Location: laporan_masuk.php');
    exit();
}

// Beri nilai
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['beri_nilai'])) {
    $nilai = (float)$_POST['nilai'];
    $feedback = sanitize($_POST['feedback']);
    
    $stmt = $pdo->prepare("UPDATE laporan SET nilai = ?, feedback = ?, status = 'dinilai', updated_at = NOW() WHERE id = ?");
    $stmt->execute([$nilai, $feedback, $laporan_id]);
    
    $_SESSION['success'] = 'Nilai berhasil diberikan!';
    header("Location: laporan_masuk.php");
    exit();
}

$title = "Beri Nilai - " . $laporan['nama_praktikum'];
include '../header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Beri Nilai Laporan</h1>
                <a href="laporan_masuk.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Detail Laporan</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Mahasiswa</th>
                                    <td><?= $laporan['nama_lengkap'] ?></td>
                                </tr>
                                <tr>
                                    <th>Praktikum</th>
                                    <td><?= $laporan['nama_praktikum'] ?></td>
                                </tr>
                                <tr>
                                    <th>Modul</th>
                                    <td><?= $laporan['judul_modul'] ?></td>
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
                                <?php if ($laporan['nilai']): ?>
                                    <tr>
                                        <th>Nilai</th>
                                        <td><?= $laporan['nilai'] ?></td>
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
                            <h5>Form Penilaian</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="nilai" class="form-label">Nilai (0-100)</label>
                                    <input type="number" class="form-control" id="nilai" name="nilai" 
                                           min="0" max="100" step="0.01" 
                                           value="<?= $laporan['nilai'] ? $laporan['nilai'] : '' ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="feedback" class="form-label">Feedback</label>
                                    <textarea class="form-control" id="feedback" name="feedback" rows="5" required><?= $laporan['feedback'] ? $laporan['feedback'] : '' ?></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="beri_nilai" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Simpan Nilai
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>