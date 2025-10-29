<?php
session_start();
require_once __DIR__ . '/../config/db.php';
$pdo = getPDO();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    if ($u && $p) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$u]); 
        $user = $stmt->fetch();
        if ($user && $user['password'] === md5($p)) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // ✅ Tambahan: tandai user aktif dan catat waktu login
    $pdo->prepare("UPDATE users SET is_active = 1, last_login = NOW() WHERE id = ?")
        ->execute([$user['id']]);

    header($user['role'] === 'admin' ? 'Location: ../admin/index.php' : 'Location: ../public/ads.php');
    exit;
}

        } else {
            $error = 'Username atau password salah';
        }
    } else {
        $error = 'Isi semua kolom';
    }

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="icon" type="png" href="../assets/img/Icon1_Hik.png">
  <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="login-container">
  <h2>Masuk ke Akun Anda</h2>
  
  <?php if($error): ?>
    <p class="error-msg"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" class="login-form">
    <div class="input-group">
      <label for="username">Username</label>
      <input id="username" name="username" placeholder="Masukkan username" required>
    </div>

    <div class="input-group">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" placeholder="Masukkan password" required>
    </div>

    <button type="submit" class="btn-login">Login</button>
  </form>

  <p class="footer-text">Belum punya akun? <a href="../auth/register.php">Daftar Sekarang</a></p>
</div>

</body>
</html>
