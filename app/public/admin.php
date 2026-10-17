<?php
require_once __DIR__ . '/../src/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

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

    $pdo->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM recipes WHERE id = ?")->execute([$id]);
    
    app_log('INFO', "Admin deleted recipe ID: $id");
    header('Location: admin.php?msg=deleted');
    exit;
}

// Xử lý thêm công thức
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    $title = trim($_POST['title']);
    $slug = create_slug($title);
    $category_id = (int)$_POST['category_id'];
    $description = trim($_POST['description']);
    $cook_time = (int)$_POST['cook_time'];
    $servings = (int)$_POST['servings'];
    $difficulty = $_POST['difficulty'];
    $instructions = trim($_POST['instructions']);
    
    // Xử lý upload ảnh hoặc đường dẫn URL
    $imagePath = '';
    if (!empty($_POST['image_url'])) {
        $imagePath = trim($_POST['image_url']);
    }
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['image']['size'] < 2 * 1024 * 1024) {
            $newName = uniqid() . '.' . $ext;
            $dest = $uploadDir . '/' . $newName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $imagePath = 'uploads/' . $newName;
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO recipes (title, slug, description, image, cook_time, servings, difficulty, category_id, instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $slug, $description, $imagePath, $cook_time, $servings, $difficulty, $category_id, $instructions]);
    $newRecipeId = $pdo->lastInsertId();

    // Xử lý lưu nguyên liệu
    if (!empty($_POST['ingredients']) && is_array($_POST['ingredients'])) {
        $stmtFindIng = $pdo->prepare("SELECT id FROM ingredients WHERE name = ?");
        $stmtInsertIng = $pdo->prepare("INSERT INTO ingredients (name) VALUES (?)");
        $stmtInsertRI = $pdo->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity, unit) VALUES (?, ?, ?, ?)");

        foreach ($_POST['ingredients'] as $ing) {
            $ingName = trim($ing['name'] ?? '');
            $quantity = trim($ing['quantity'] ?? '');
            $unit = trim($ing['unit'] ?? '');

            if ($ingName !== '') {
                $stmtFindIng->execute([$ingName]);
                $ingId = $stmtFindIng->fetchColumn();

                if (!$ingId) {
                    $stmtInsertIng->execute([$ingName]);
                    $ingId = $pdo->lastInsertId();
                }

                $stmtInsertRI->execute([$newRecipeId, $ingId, $quantity, $unit]);
            }
        }
    }
    
    app_log('INFO', "Admin added new recipe ID $newRecipeId: $title");
    header('Location: admin.php?msg=added');
    exit;
}

$recipes = $pdo->query("SELECT r.id, r.title, r.image, r.cook_time, r.difficulty, c.name as cat_name FROM recipes r LEFT JOIN categories c ON r.category_id = c.id ORDER BY r.id DESC")->fetchAll();
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
<body style="background-color: #FAFAFA;">
    <!-- HeroUI Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top border-bottom" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-color: var(--border-color) !important;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php" style="color: var(--primary-color) !important;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--primary-50); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-utensils text-primary"></i>
                </div>
                <span class="fw-bold tracking-tight">Bếp Ngon</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 bg-light px-3 py-1 rounded-pill border">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-size: 11px;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <span class="small fw-semibold text-secondary"><?= htmlspecialchars($_SESSION['username']) ?></span>
                </div>
                <a href="logout.php" class="heroui-btn-danger text-decoration-none">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Header Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--text-main); letter-spacing: -0.02em;">Bảng điều khiển quản trị</h3>
                <p class="text-muted small m-0 mt-1">Quản lý và cập nhật toàn bộ công thức, nguyên liệu và danh mục</p>
            </div>
            <span class="heroui-badge heroui-badge-primary">
                <i class="fa-solid fa-layer-group"></i> <?= count($recipes) ?> Công thức
            </span>
        </div>
        
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm rounded-4 mb-4" style="background: rgba(23, 201, 100, 0.12); color: #12A150;">
                <i class="fa-solid fa-circle-check fs-5"></i>
                <span class="fw-medium">Thao tác thành công! Dữ liệu đã được cập nhật đồng bộ vào hệ thống.</span>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Cột Thêm công thức mới -->
            <div class="col-lg-5">
                <div class="heroui-card">
                    <div class="heroui-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2 text-primary">
                            <i class="fa-solid fa-plus-circle fs-5"></i>
                            <span class="fw-bold">Thêm công thức mới</span>
                        </div>
                        <span class="heroui-badge heroui-badge-primary">Mới</span>
                    </div>
                    <div class="p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="add">
                            
                            <div class="heroui-input-group mb-3">
                                <label class="heroui-label">Tên món ăn <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control heroui-input" placeholder="Ví dụ: Phở Bò Tái Lăn" required autocomplete="off">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="heroui-input-group">
                                        <label class="heroui-label">Danh mục <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-select heroui-input" required>
                                            <?php foreach($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="heroui-input-group">
                                        <label class="heroui-label">Độ khó</label>
                                        <select name="difficulty" class="form-select heroui-input">
                                            <option value="Dễ">Dễ</option>
                                            <option value="Trung bình" selected>Trung bình</option>
                                            <option value="Khó">Khó</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="heroui-input-group">
                                        <label class="heroui-label">Thời gian (phút)</label>
                                        <input type="number" name="cook_time" class="form-control heroui-input" placeholder="45" value="30" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="heroui-input-group">
                                        <label class="heroui-label">Khẩu phần (người)</label>
                                        <input type="number" name="servings" class="form-control heroui-input" placeholder="4" value="4" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Ảnh minh họa: Hỗ trợ cả file lẫn URL -->
                            <div class="heroui-input-group mb-3">
                                <label class="heroui-label">Ảnh minh họa (Upload tệp hoặc nhập URL)</label>
                                <input type="file" name="image" class="form-control heroui-input mb-2" accept="image/*">
                                <input type="url" name="image_url" class="form-control heroui-input" placeholder="Hoặc dán link ảnh trực tuyến (https://...)">
                            </div>

                            <!-- Khối Nguyên liệu động chuẩn HeroUI -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="heroui-label m-0"><i class="fa-solid fa-carrot text-warning me-1"></i> Danh sách nguyên liệu</label>
                                    <button type="button" class="btn btn-sm heroui-btn-light py-1 px-2" id="btnAddIngredient">
                                        <i class="fa-solid fa-plus text-primary me-1"></i> Thêm nguyên liệu
                                    </button>
                                </div>
                                <div id="ingredientsList">
                                    <div class="ingredient-row d-flex align-items-center gap-2">
                                        <input type="text" name="ingredients[0][name]" class="form-control heroui-input" placeholder="Tên nguyên liệu (VD: Thịt bò)" style="flex: 2;">
                                        <div class="heroui-stepper" style="flex: 1.6;">
                                            <button type="button" class="heroui-stepper-btn btn-step-down" title="Giảm định lượng">−</button>
                                            <input type="text" name="ingredients[0][quantity]" class="form-control heroui-input ing-qty-input" placeholder="Số lượng" value="100">
                                            <button type="button" class="heroui-stepper-btn btn-step-up" title="Tăng định lượng">+</button>
                                        </div>
                                        <input type="text" name="ingredients[0][unit]" class="form-control heroui-input" placeholder="Đơn vị (g, ml)" style="flex: 1;">
                                        <button type="button" class="btn btn-sm btn-link text-danger p-1 btn-remove-ing" title="Xóa"><i class="fa-solid fa-trash-can"></i></button>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size: 0.775rem;">Bạn có thể bấm "Thêm nguyên liệu" để mở rộng danh sách linh hoạt.</small>
                            </div>

                            <div class="heroui-input-group mb-3">
                                <label class="heroui-label">Mô tả ngắn <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control heroui-input" rows="2" placeholder="Tóm tắt nét đặc sắc của món ăn..." required></textarea>
                            </div>

                            <div class="heroui-input-group mb-4">
                                <label class="heroui-label">Các bước thực hiện <span class="text-danger">*</span></label>
                                <textarea name="instructions" class="form-control heroui-input" rows="5" placeholder="Bước 1: Sơ chế...&#10;Bước 2: Nấu nước dùng..." required></textarea>
                            </div>

                            <button type="submit" class="heroui-btn-primary w-100 py-3">
                                <i class="fa-solid fa-floppy-disk"></i> Lưu công thức vào hệ thống
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Cột Danh sách công thức hiện có -->
            <div class="col-lg-7">
                <div class="heroui-card h-100">
                    <div class="heroui-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-table-list text-primary fs-5"></i>
                            <span class="fw-bold">Danh sách công thức hiện có</span>
                        </div>
                        <span class="text-muted small">Cập nhật thời gian thực</span>
                    </div>
                    <div class="p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead>
                                    <tr style="border-bottom: 2px solid var(--border-color); background: var(--content2);">
                                        <th class="ps-4 py-3 text-muted small fw-bold">MÓN ĂN</th>
                                        <th class="py-3 text-muted small fw-bold">DANH MỤC</th>
                                        <th class="py-3 text-muted small fw-bold">THÔNG TIN</th>
                                        <th class="text-end pe-4 py-3 text-muted small fw-bold">THAO TÁC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recipes as $r): ?>
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if(!empty($r['image'])): ?>
                                                    <img src="<?= htmlspecialchars($r['image']) ?>" alt="thumb" class="avatar-preview">
                                                <?php else: ?>
                                                    <div class="avatar-preview d-flex align-items-center justify-content-center bg-light text-muted">
                                                        <i class="fa-solid fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-bold" style="color: var(--text-main);"><?= htmlspecialchars($r['title']) ?></div>
                                                    <small class="text-muted">Mã: #<?= $r['id'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="heroui-badge heroui-badge-primary">
                                                <?= htmlspecialchars($r['cat_name'] ?? 'Chưa phân loại') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small text-muted"><i class="fa-regular fa-clock me-1 text-warning"></i> <?= $r['cook_time'] ?> phút</div>
                                            <div class="small text-muted mt-1"><i class="fa-solid fa-fire text-danger me-1"></i> <?= htmlspecialchars($r['difficulty'] ?? 'TB') ?></div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2">
                                                <a href="recipe.php?id=<?= $r['id'] ?>" class="btn btn-sm heroui-btn-light p-2" target="_blank" title="Xem chi tiết">
                                                    <i class="fa-regular fa-eye text-primary"></i>
                                                </a>
                                                <a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-sm heroui-btn-light p-2" title="Chỉnh sửa công thức & nguyên liệu">
                                                    <i class="fa-regular fa-pen-to-square text-warning"></i>
                                                </a>
                                                <a href="?delete=<?= $r['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" class="btn btn-sm heroui-btn-danger p-2" onclick="return confirm('Bạn có chắc chắn muốn xóa công thức này khỏi hệ thống?');" title="Xóa">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </a>
                                            </div>
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
    <script>
        // JS Thêm / Xóa / Tăng giảm định lượng nguyên liệu
        let ingredientIndex = 1;
        document.getElementById('btnAddIngredient').addEventListener('click', function() {
            const container = document.getElementById('ingredientsList');
            const row = document.createElement('div');
            row.className = 'ingredient-row d-flex align-items-center gap-2';
            row.innerHTML = `
                <input type="text" name="ingredients[${ingredientIndex}][name]" class="form-control heroui-input" placeholder="Tên nguyên liệu" style="flex: 2;">
                <div class="heroui-stepper" style="flex: 1.6;">
                    <button type="button" class="heroui-stepper-btn btn-step-down" title="Giảm định lượng">−</button>
                    <input type="text" name="ingredients[${ingredientIndex}][quantity]" class="form-control heroui-input ing-qty-input" placeholder="Số lượng" value="100">
                    <button type="button" class="heroui-stepper-btn btn-step-up" title="Tăng định lượng">+</button>
                </div>
                <input type="text" name="ingredients[${ingredientIndex}][unit]" class="form-control heroui-input" placeholder="Đơn vị" style="flex: 1;">
                <button type="button" class="btn btn-sm btn-link text-danger p-1 btn-remove-ing" title="Xóa"><i class="fa-solid fa-trash-can"></i></button>
            `;
            container.appendChild(row);
            ingredientIndex++;
            attachIngredientEvents();
        });

        function adjustQuantity(input, delta) {
            let val = parseFloat(input.value);
            if (isNaN(val)) {
                val = 0;
            }
            let step = (val >= 100) ? 50 : ((val >= 10) ? 5 : 1);
            let newVal = val + (delta * step);
            if (newVal < 0) newVal = 0;
            input.value = newVal;
        }

        function attachIngredientEvents() {
            // Nút xóa
            document.querySelectorAll('.btn-remove-ing').forEach(btn => {
                btn.onclick = function() {
                    if (document.querySelectorAll('.ingredient-row').length > 1) {
                        this.closest('.ingredient-row').remove();
                    } else {
                        // Nếu còn 1 dòng thì chỉ xóa rỗng giá trị
                        const inputs = this.closest('.ingredient-row').querySelectorAll('input');
                        inputs.forEach(input => input.value = '');
                    }
                };
            });

            // Nút tăng định lượng (+)
            document.querySelectorAll('.btn-step-up').forEach(btn => {
                btn.onclick = function() {
                    const input = this.closest('.heroui-stepper').querySelector('.ing-qty-input');
                    adjustQuantity(input, 1);
                };
            });

            // Nút giảm định lượng (-)
            document.querySelectorAll('.btn-step-down').forEach(btn => {
                btn.onclick = function() {
                    const input = this.closest('.heroui-stepper').querySelector('.ing-qty-input');
                    adjustQuantity(input, -1);
                };
            });
        }
        attachIngredientEvents();
    </script>
</body>
</html>
