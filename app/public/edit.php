<?php
require_once __DIR__ . '/../src/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: admin.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->execute([$id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    die("Recipe not found");
}

$uploadDir = __DIR__ . '/uploads';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    $title = $_POST['title'];
    $slug = create_slug($title);
    $category_id = (int)$_POST['category_id'];
    $description = $_POST['description'];
    $cook_time = (int)$_POST['cook_time'];
    $servings = (int)$_POST['servings'];
    $difficulty = $_POST['difficulty'];
    $instructions = $_POST['instructions'];
    
    // Check if new image was uploaded
    $imagePath = $recipe['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['image']['size'] < 2 * 1024 * 1024) {
            $newName = uniqid() . '.' . $ext;
            $dest = $uploadDir . '/' . $newName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                // Delete old image if it was a local file (in uploads/)
                if ($imagePath && strpos($imagePath, 'uploads/') === 0 && file_exists(__DIR__ . '/' . $imagePath)) {
                    unlink(__DIR__ . '/' . $imagePath);
                }
                $imagePath = 'uploads/' . $newName;
            }
        }
    }

    $updateStmt = $pdo->prepare("UPDATE recipes SET title=?, slug=?, description=?, image=?, cook_time=?, servings=?, difficulty=?, category_id=?, instructions=? WHERE id=?");
    $updateStmt->execute([$title, $slug, $description, $imagePath, $cook_time, $servings, $difficulty, $category_id, $instructions, $id]);
    
    app_log('INFO', "Admin updated recipe ID: $id");
    header('Location: admin.php?msg=updated');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa - Bếp Ngon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-utensils me-2"></i>Bếp Ngon</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text me-3 fw-medium"><i class="fa-regular fa-circle-user me-1"></i> Xin chào, <span class="text-primary"><?= htmlspecialchars($_SESSION['username']) ?></span></span>
                <a href="admin.php" class="btn btn-outline-secondary btn-sm rounded-pill me-2">Quay lại</a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0" style="color: var(--text-main);"><i class="fa-solid fa-pen-to-square me-2 text-warning"></i>Chỉnh sửa công thức</h2>
        </div>
        
        <div class="card admin-card mx-auto" style="max-width: 800px;">
            <div class="card-body bg-white rounded-4 p-4">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="edit">
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium text-muted small">Tên món</label>
                        <input type="text" name="title" class="form-control bg-light border-0" value="<?= htmlspecialchars($recipe['title']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium text-muted small">Danh mục</label>
                        <select name="category_id" class="form-select bg-light border-0" required>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $recipe['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium text-muted small">Ảnh minh họa hiện tại</label>
                        <div class="mb-2">
                            <?php if($recipe['image']): ?>
                                <img src="<?= htmlspecialchars($recipe['image']) ?>" alt="Current Image" style="height: 100px; border-radius: 8px; object-fit: cover;">
                            <?php else: ?>
                                <span class="text-muted">Chưa có ảnh</span>
                            <?php endif; ?>
                        </div>
                        <label class="form-label fw-medium text-muted small">Thay đổi ảnh (Để trống nếu giữ nguyên)</label>
                        <input type="file" name="image" class="form-control bg-light border-0" accept="image/*">
                    </div>
                    
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label class="form-label fw-medium text-muted small">Tg nấu (phút)</label>
                            <input type="number" name="cook_time" class="form-control bg-light border-0" value="<?= htmlspecialchars($recipe['cook_time']) ?>" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label fw-medium text-muted small">Khẩu phần</label>
                            <input type="number" name="servings" class="form-control bg-light border-0" value="<?= htmlspecialchars($recipe['servings']) ?>" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label fw-medium text-muted small">Độ khó</label>
                            <select name="difficulty" class="form-select bg-light border-0 px-2">
                                <option <?= $recipe['difficulty'] == 'Dễ' ? 'selected' : '' ?>>Dễ</option>
                                <option <?= $recipe['difficulty'] == 'Trung bình' || $recipe['difficulty'] == 'TB' ? 'selected' : '' ?>>Trung bình</option>
                                <option <?= $recipe['difficulty'] == 'Khó' ? 'selected' : '' ?>>Khó</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium text-muted small">Mô tả ngắn</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="3" required><?= htmlspecialchars($recipe['description']) ?></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-medium text-muted small">Các bước thực hiện</label>
                        <textarea name="instructions" class="form-control bg-light border-0" rows="6" required><?= htmlspecialchars($recipe['instructions']) ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-warning w-100 fw-bold text-white"><i class="fa-solid fa-floppy-disk me-2"></i>Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



