<?php
require 'databasekey.php'; 

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Koneksi ke database (gunakan PDO untuk keamanan)
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query untuk mencari pengguna berdasarkan username
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Periksa status verifikasi akun
                if ($user['verified'] == 1) {
                    // Akun sudah diverifikasi, set session untuk login
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
        
                    // Redirect ke halaman setelah login sukses
                    header('Location: Dashbordmahasiswa.php');
                    exit;
                } elseif ($user['verified'] == 0) {
                    // Akun belum diverifikasi
                    $error_message = "Akun Anda belum diverifikasi oleh admin.";
                } elseif ($user['verified'] == 2) {
                    // Akun tidak lolos verifikasi
                    $error_message = "Akun Anda tidak lolos verifikasi.";
                } else {
                    // Status verifikasi tidak dikenal
                    $error_message = "Status verifikasi akun tidak dikenal.";
                }
            } else {
                // Kombinasi username dan password salah
                $error_message = "Kombinasi username dan password salah.";
            }
        } else {
            $error_message = "Kombinasi username dan password salah.";
        }
        
    } catch(PDOException $e) {
        $error_message = "Koneksi database gagal: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Solid Kids</title>
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="container">
        <div class="form-image">
            <img src="../img/login3.gif" alt="" aria-hidden="true">
            <div class="hero-copy">
                <p class="eyebrow">RUANG BERKARYA MAHASISWA</p>
                <h2>Ide baik hari ini,<br>inspirasi belajar esok.</h2>
                <p class="hero-description">Bagikan konten edukatif dan bantu anak-anak belajar dengan cara yang menyenangkan.</p>
            </div>
        </div>
        <div class="login-form">
            <div class="login-heading">
                <p class="eyebrow">PORTAL KREATOR</p>
                <h1>Selamat datang kembali</h1>
                <p>Masuk untuk melanjutkan perjalanan berkarya Anda.</p>
            </div>
            <form id="login-form" method="post" action="">
                <?php if (isset($error_message)) : ?>
                <p class="form-error" role="alert"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <div class="form-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" autocomplete="username" required>
                </div>
                <div class="form-field">
                    <label for="password">Kata sandi</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                </div>
                <button type="submit" name="login">
                    <span>Masuk ke akun</span>
                    <span class="button-arrow" aria-hidden="true">→</span>
                </button>
            </form>
            <div class="create-account">Belum memiliki akun? <a href="registrasi.php">Daftar sekarang</a></div>
        </div>
    </div>
</body>

</html>