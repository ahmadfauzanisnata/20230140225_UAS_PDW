<?php
require_once '../koneksi.php';
redirectIfNotAdmin(); // Pastikan hanya admin yang bisa akses

$title = "Profil Admin";
include '../header.php';

// Ambil data admin
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Profil Admin</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="edit_profil.php" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil-square"></i> Edit Profil
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <img src="../uploads/profil/<?= $admin['images.jpeg'] ?? 'images.jpeg' ?>" 
                                 class="img-thumbnail rounded-circle mb-3" 
                                 width="200" 
                                 alt="Foto Profil">
                            <h4><?= htmlspecialchars($admin['nama_lengkap']) ?></h4>
                            <span class="badge bg-primary">Administrator</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Informasi Akun</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr>
                                    <th width="30%">Username</th>
                                    <td><?= htmlspecialchars($admin['username']) ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <td><?= htmlspecialchars($admin['nama_lengkap']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= htmlspecialchars($admin['email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Terdaftar Pada</th>
                                    <td><?= date('d F Y H:i', strtotime($admin['created_at'])) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5>Keamanan Akun</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="ubah_password.php" class="btn btn-warning">
                                    <i class="bi bi-shield-lock"></i> Ubah Password
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>