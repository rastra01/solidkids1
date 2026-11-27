<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
    <div class="brand">
        <img src="../foto/logo4.png" alt="Solid Kids">
    </div>

    <nav aria-label="Navigasi mahasiswa">
        <ul class="nav nav-pills nav-sidebar flex-column">
            <li class="nav-item">
                <a href="Dashbordmahasiswa.php" class="nav-link<?php echo $currentPage === 'Dashbordmahasiswa.php' ? ' active' : ''; ?>">
                    <i class="fa-solid fa-gauge"></i><p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="Riwayat.php" class="nav-link<?php echo $currentPage === 'Riwayat.php' ? ' active' : ''; ?>">
                    <i class="fa-solid fa-clock-rotate-left"></i><p>Riwayat Konten</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="upload.php" class="nav-link<?php echo $currentPage === 'upload.php' ? ' active' : ''; ?>">
                    <i class="fa-solid fa-cloud-arrow-up"></i><p>Upload Konten</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="setting.php" class="nav-link<?php echo $currentPage === 'setting.php' ? ' active' : ''; ?>">
                    <i class="fa-solid fa-gears"></i><p>Pengaturan</p>
                </a>
            </li>
        </ul>
    </nav>

    <div class="logout">
        <a href="../logout.php">
            <i class="fa-solid fa-power-off"></i><p>Logout</p>
        </a>
    </div>
</aside>
