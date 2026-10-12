<?php
require_once __DIR__ . '/../src/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Xử lý tạo thư mục upload nếu chưa có
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Xử lý xóa
if (isset($_GET['delete']) && isset($_GET['token']) && $_GET['token'] === $_SESSION['csrf_token']) {
    $id = (int)$_GET['delete'];
    
    // Lấy tên ảnh để xóa file vật lý
    $stmtImg = $pdo->prepare("SELECT image FROM recipes WHERE id = ?");
    $stmtImg->execute([$id]);
    $img = $stmtImg->fetchColumn();
    if ($img && file_exists(__DIR__ . '/' . $img)) {
        unlink(__DIR__ . '/' . $img);
    }

    $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt->execute([$id]);
    app_log('INFO', "Admin deleted recipe ID: $id");
    header('Location: admin.php?msg=deleted');
    exit;
}

// Xử lý thêm công thức đơn giản (chỉ có bảng recipes cho gọn trong bài này)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
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
    
    // Xử lý upload ảnh
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['image']['size'] < 2 * 1024 * 1024) { // Max 2MB
            $newName = uniqid() . '.' . $ext;
            $dest = $uploadDir . '/' . $newName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $imagePath = 'uploads/' . $newName;
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO recipes (title, slug, description, image, cook_time, servings, difficulty, category_id, instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $slug, $description, $imagePath, $cook_time, $servings, $difficulty, $category_id, $instructions]);
    
    app_log('INFO', "Admin added new recipe: $title");
    header('Location: admin.php?msg=added');
    exit;
}

$recipes = $pdo->query("SELECT r.id, r.title, c.name as cat_name FROM recipes r LEFT JOIN categories c ON r.category_id = c.id ORDER BY r.created_at DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị - Bếp Ngon</title>
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
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0" style="color: var(--text-main);"><i class="fa-solid fa-gauge-high me-2 text-primary"></i>Bảng điều khiển</h2>
        </div>
        
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success rounded-3 border-0 shadow-sm"><i class="fa-solid fa-circle-check me-2"></i>Thao tác thành công!</div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card admin-card h-100">
                    <div class="card-header d-flex align-items-center">
                        <i class="fa-solid fa-plus me-2"></i> Thêm công thức mới
                    </div>
                    <div class="card-body bg-white rounded-bottom-4">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="add">
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted small">Tên món</label>
                                <input type="text" name="title" class="form-control bg-light border-0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted small">Danh mục</label>
                                <select name="category_id" class="form-select bg-light border-0" required>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted small">Ảnh minh họa (Tối đa 2MB, JPG/PNG)</label>
                                <input type="file" name="image" class="form-control bg-light border-0" accept="image/*">
                            </div>
                            <div class="row">
                                <div class="col-4 mb-3">
                                    <label class="form-label fw-medium text-muted small">Tg nấu</label>
                                    <div class="input-group">
                                        <input type="number" name="cook_time" class="form-control bg-light border-0" required>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="form-label fw-medium text-muted small">Khẩu phần</label>
                                    <input type="number" name="servings" class="form-control bg-light border-0" required>
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="form-label fw-medium text-muted small">Độ khó</label>
                                    <select name="difficulty" class="form-select bg-light border-0 px-2">
                                        <option>Dễ</option>
                                        <option>TB</option>
                                        <option>Khó</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted small">Mô tả ngắn</label>
                                <textarea name="description" class="form-control bg-light border-0" rows="2" required></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-medium text-muted small">Các bước thực hiện (Mỗi bước 1 dòng)</label>
                                <textarea name="instructions" class="form-control bg-light border-0" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-custom w-100"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Lưu công thức</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card admin-card h-100">
                    <div class="card-header d-flex align-items-center" style="background-color: var(--text-main);">
                        <i class="fa-solid fa-list me-2"></i> Danh sách công thức
                    </div>
                    <div class="card-body bg-white rounded-bottom-4 p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-custom mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">ID</th>
                                        <th>Tên món</th>
                                        <th>Danh mục</th>
                                        <th class="text-end pe-4">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recipes as $r): ?>
                                    <tr>
                                        <td class="ps-4 text-muted">#<?= $r['id'] ?></td>
                                        <td class="fw-medium"><?= htmlspecialchars($r['title']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($r['cat_name'] ?? '') ?></span></td>
                                        <td class="text-end pe-4">
                                            <a href="recipe.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-info rounded-pill px-3 me-1" target="_blank" title="Xem"><i class="fa-regular fa-eye"></i></a>
                                            <a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3 me-1" title="Sửa"><i class="fa-regular fa-pen-to-square"></i></a>
                                            <a href="?delete=<?= $r['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Bạn có chắc chắn muốn xóa công thức này?');" title="Xóa"><i class="fa-regular fa-trash-can"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


