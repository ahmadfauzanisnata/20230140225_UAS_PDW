<?php
require_once '../koneksi.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}
?>

<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3 sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="katalog_praktikum.php">
                    <i class="bi bi-journal-bookmark"></i>
                    Katalog Praktikum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="praktikum_saya.php">
                    <i class="bi bi-collection"></i>
                    Praktikum Saya
                </a>
            </li>
        </ul>
        
    </div>
</div>