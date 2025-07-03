<?php
require_once('../koneksi.php');


// Redirect jika sudah login
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: mahasiswa/dashboard.php');
    }
    exit();
}

$title = "SIMPRAK - Sistem Informasi Praktikum";
include 'header.php';
?>

<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        line-height: 1.6;
        color: var(--dark-color);
        overflow-x: hidden;
    }
    
    .hero-section {
        background: var(--gradient);
        color: white;
        padding: 6rem 0;
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        z-index: 0;
    }
    
    .hero-section .row {
        position: relative;
        z-index: 1;
    }
    
    .hero-section h1 {
        font-weight: 700;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .hero-section p {
        font-size: 1.1rem;
        opacity: 0.9;
    }
    
    .hero-section img {
        border-radius: 10px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        transform: perspective(1000px) rotateY(-10deg);
        transition: all 0.3s ease;
    }
    
    .hero-section img:hover {
        transform: perspective(1000px) rotateY(0deg);
    }
    
    .btn-primary {
        background-color: white;
        color: var(--primary-color);
        border: none;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }
    
    .btn-primary:hover {
        background-color: rgba(255,255,255,0.9);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }
    
    .btn-outline-primary {
        border: 2px solid white;
        color: white;
        background: transparent;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background: white;
        color: var(--primary-color);
        transform: translateY(-2px);
    }
    
    .features-section {
        padding: 5rem 0;
    }
    
    .feature-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        background: white;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }
    
    .feature-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gradient);
        border-radius: 50%;
        color: white;
        font-size: 2rem;
    }
    
    .feature-card h3 {
        color: var(--dark-color);
        margin: 1rem 0;
        font-weight: 600;
    }
    
    .feature-card p {
        color: #6c757d;
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 4rem 0;
            text-align: center;
        }
        
        .hero-section .d-flex {
            justify-content: center;
        }
        
        .hero-section img {
            margin-top: 2rem;
            max-width: 80%;
        }
    }
</style>

<div class="container-fluid hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Selamat Datang di SIMPRAK</h1>
                <p class="lead mb-4">Sistem Informasi Manajemen Praktikum yang memudahkan mahasiswa dan asisten dalam mengelola kegiatan praktikum.</p>
                <div class="d-flex gap-3">
                    <a href="login.php" class="btn btn-primary btn-lg px-4">Masuk</a>
                    <a href="register.php" class="btn btn-outline-primary btn-lg px-4">Daftar</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="images.jpeg" alt="Ilustrasi Praktikum" class="img-fluid hero-image">
            </div>
        </div>
    </div>
</div>

<div class="container features-section">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-3">Fitur Unggulan</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">Temukan kemudahan dalam mengelola praktikum dengan fitur-fitur modern kami</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card feature-card h-100">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <h3>Katalog Praktikum</h3>
                    <p class="mb-0">Akses semua praktikum dengan informasi lengkap dan modul terstruktur dalam satu platform.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card h-100">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>
                    <h3>Upload Laporan</h3>
                    <p class="mb-0">Upload laporan praktikum dengan sistem yang cepat, aman, dan terintegrasi.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card h-100">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3>Penilaian Online</h3>
                    <p class="mb-0">Dapatkan penilaian dan umpan balik dari asisten secara real-time dan transparan.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>