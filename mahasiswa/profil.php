<?php
require_once '../koneksi.php';
redirectIfNotLoggedIn();

$title = "Profil Pengguna";
include '../header.php';

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Debugging - tampilkan path foto
$fotoPath = '../uploads/profil/' . ($user['foto_profil'] ?? 'default.jpg');
if (!file_exists($fotoPath)) {
    $fotoPath = '../assets/images/default-profile.jpg'; // Fallback image
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Profil Saya</h1>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="row">
                      <div class="col-md-4 text-center">
                <img src="<?= $fotoPath ?>" 
                    class="img-thumbnail rounded-circle mb-3" 
                     width="200" 
                    alt="Foto Profil">
         <form action="upload_profil.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
            <label for="foto_profil" class="form-label">Pilih Foto Profil</label>
            <input type="file" class="form-control" id="foto_profil" name="foto_profil" required>
            <div class="form-text">Format: JPG, PNG (Maks. 2MB)</div>
    </div>
    <button type="submit" class="btn btn-primary">Upload Foto</button>
        </form>
    </div>
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Username</th>
                                    <td><?= $user['username'] ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <td><?= $user['nama_lengkap'] ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= $user['email'] ?></td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td><?= ucfirst($user['role']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../footer.php'; ?>