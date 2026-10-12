<?php
require_once __DIR__ . '/../src/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        app_log('INFO', "User login successful: $username");
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Sai tên đăng nhập hoặc mật khẩu.';
        app_log('ERROR', "User login failed: $username");
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin - Bếp Ngon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container auth-wrapper">
        <div class="auth-card">
            <div class="text-center mb-4">
                <i class="fa-solid fa-utensils fa-3x" style="color: var(--primary-color);"></i>
            </div>
            <h3 class="text-center auth-title">Đăng nhập Bếp Ngon</h3>
            
            <?php if($error): ?>
                <div class="alert alert-danger rounded-3"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-regular fa-user text-muted"></i></span>
                        <input type="text" name="username" class="form-control border-start-0 ps-0" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 ps-0" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100 py-2 fs-5 mt-2">Đăng nhập <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i></button>
                <div class="mt-4 text-center">
                    <a href="index.php" class="text-decoration-none text-muted hover-primary"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại trang chủ</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
