<?php
include '../../config.php';

// Cek apakah sudah login
if (!isset($_SESSION['level_id'])) {
    // Jika belum login, redirect ke halaman login
    header("Location: ../../index.php");
    exit();
}

// Routing berdasarkan level_id
if ($_SESSION['level_id'] == 1) {
    // Jika level manager
    include 'index_manager.php';
} elseif ($_SESSION['level_id'] == 2 ) {
    // Jika level operator
    include 'index_operator.php';
} elseif ($_SESSION['level_id'] == 6 ) {
    // Jika level staf
    include 'index_staf.php'; 
} elseif ($_SESSION['level_id'] == 5 ) {
    // Jika level staf
    include 'index_manager.php';     
} else {
    // Jika level tidak dikenali
    echo "<script>alert('Maaf! Anda tidak memiliki akses ke halaman ini'); window.location.href='logout.php';</script>";
    exit();
}
?>
