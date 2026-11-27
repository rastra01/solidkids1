<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
    <div class="brand">
        <img src="../foto/logo4.png" alt="Solid Kids">
    </div>

    <nav aria-label="Navigasi admin">
        <ul class="nav nav-pills nav-sidebar flex-column">
            <li class="nav-item">
                <a href="../admin/index_admin.php" class="nav-link<?php echo $currentPage === 'index_admin.php' ? ' active' : ''; ?>">
                    <i class="fa-solid fa-gauge"></i><p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="../mahasiswa/cek_konten.php" class="nav-link<?php echo $currentPage === 'cek_konten.php' ? ' active' : ''; ?>">
                    <i class="fa-solid fa-check"></i><p>Cek Konten</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="../admin/cek_pelanggan.php" class="nav-link<?php echo $currentPage === 'cek_pelanggan.php' ? ' active' : ''; ?>">
                    <i class="fa-solid fa-user-check"></i><p>Cek Pelanggan</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="../admin/cek_mahasiswa.php" class="nav-link<?php echo $currentPage === 'cek_mahasiswa.php' ? ' active' : ''; ?>">
                    <i class="fa-solid fa-file"></i><p>Cek Mahasiswa</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="../admin/setting.php" class="nav-link<?php echo $currentPage === 'setting.php' ? ' active' : ''; ?>">
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
