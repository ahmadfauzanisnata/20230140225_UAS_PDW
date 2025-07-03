<?php
session_start();
require_once 'koneksi.php'; 

session_destroy();
header("Location: login.php");
exit;
?>