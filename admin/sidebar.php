<?php
require_once '../koneksi.php';
redirectIfNotAdmin();
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
                <a class="nav-link" href="kelola_praktikum.php">
                    <i class="bi bi-journal-bookmark"></i>
                    Kelola Praktikum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="laporan_masuk.php">
                    <i class="bi bi-file-earmark-arrow-up"></i>
                    Laporan Masuk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="kelola_akun.php">
                    <i class="bi bi-people"></i>
                    Kelola Akun
                </a>
            </li>
        </ul>
    </div>
</div>