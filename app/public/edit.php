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
    die("Công thức không tồn tại!");
}

$uploadDir = __DIR__ . '/uploads';

// Xử lý cập nhật công thức và nguyên liệu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
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
    
    // Xử lý ảnh: ưu tiên URL mới nếu nhập, hoặc tệp tải lên
    $imagePath = $recipe['image'];
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
                if ($imagePath && strpos($imagePath, 'uploads/') === 0 && file_exists(__DIR__ . '/' . $imagePath)) {
                    unlink(__DIR__ . '/' . $imagePath);
                }
                $imagePath = 'uploads/' . $newName;
            }
        }
    }

    $updateStmt = $pdo->prepare("UPDATE recipes SET title=?, slug=?, description=?, image=?, cook_time=?, servings=?, difficulty=?, category_id=?, instructions=? WHERE id=?");
    $updateStmt->execute([$title, $slug, $description, $imagePath, $cook_time, $servings, $difficulty, $category_id, $instructions, $id]);

    // Cập nhật nguyên liệu: Xóa các nguyên liệu cũ của recipe và thêm lại danh sách mới
    $pdo->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?")->execute([$id]);

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

                $stmtInsertRI->execute([$id, $ingId, $quantity, $unit]);
            }
        }
    }
    
    app_log('INFO', "Admin updated recipe ID: $id ($title)");
    header('Location: admin.php?msg=updated');
    exit;
}

// Lấy danh sách nguyên liệu hiện tại của công thức
$stmtIng = $pdo->prepare("SELECT i.name, ri.quantity, ri.unit FROM recipe_ingredients ri JOIN ingredients i ON ri.ingredient_id = i.id WHERE ri.recipe_id = ?");
$stmtIng->execute([$id]);
$currentIngredients = $stmtIng->fetchAll();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa: <?= htmlspecialchars($recipe['title']) ?> - Bếp Ngon</title>
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
                <span class="small fw-semibold text-secondary d-none d-sm-inline">
                    <i class="fa-solid fa-user-shield text-primary me-1"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                </span>
                <a href="admin.php" class="heroui-btn-light text-decoration-none">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                </a>
                <a href="logout.php" class="heroui-btn-danger text-decoration-none">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mx-auto" style="max-width: 860px;">
            <div>
                <h3 class="fw-bold m-0" style="color: var(--text-main); letter-spacing: -0.02em;">Chỉnh sửa công thức</h3>
                <p class="text-muted small m-0 mt-1">Cập nhật thông tin chi tiết, hình ảnh và danh sách nguyên liệu</p>
            </div>
            <a href="recipe.php?id=<?= $id ?>" target="_blank" class="heroui-btn-light text-decoration-none">
                <i class="fa-regular fa-eye text-primary me-1"></i> Xem trực tiếp
            </a>
        </div>
        
        <div class="heroui-card mx-auto shadow-md" style="max-width: 860px;">
            <div class="heroui-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 text-primary">
                    <i class="fa-solid fa-pen-to-square fs-5"></i>
                    <span class="fw-bold">Thông tin món: <?= htmlspecialchars($recipe['title']) ?></span>
                </div>
                <span class="heroui-badge heroui-badge-primary">ID #<?= $recipe['id'] ?></span>
            </div>

            <div class="p-4">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="edit">
                    
                    <div class="heroui-input-group mb-3">
                        <label class="heroui-label">Tên món ăn <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control heroui-input" value="<?= htmlspecialchars($recipe['title']) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="heroui-input-group">
                                <label class="heroui-label">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select heroui-input" required>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $recipe['category_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heroui-input-group">
                                <label class="heroui-label">Độ khó</label>
                                <select name="difficulty" class="form-select heroui-input">
                                    <option value="Dễ" <?= $recipe['difficulty'] == 'Dễ' ? 'selected' : '' ?>>Dễ</option>
                                    <option value="Trung bình" <?= ($recipe['difficulty'] == 'Trung bình' || $recipe['difficulty'] == 'TB') ? 'selected' : '' ?>>Trung bình</option>
                                    <option value="Khó" <?= $recipe['difficulty'] == 'Khó' ? 'selected' : '' ?>>Khó</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="heroui-input-group">
                                <label class="heroui-label">Thời gian nấu (phút)</label>
                                <input type="number" name="cook_time" class="form-control heroui-input" value="<?= htmlspecialchars($recipe['cook_time']) ?>" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="heroui-input-group">
                                <label class="heroui-label">Khẩu phần (người)</label>
                                <input type="number" name="servings" class="form-control heroui-input" value="<?= htmlspecialchars($recipe['servings']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Ảnh minh họa hiện tại & Đổi ảnh -->
                    <div class="mb-3 p-3 rounded-3" style="background: var(--content2); border: 1px solid var(--border-color);">
                        <label class="heroui-label mb-2 d-block">Ảnh minh họa</label>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <?php if(!empty($recipe['image'])): ?>
                                <img src="<?= htmlspecialchars($recipe['image']) ?>" alt="Thumb" class="avatar-preview shadow-sm" style="width: 80px; height: 80px;">
                                <div>
                                    <div class="small fw-semibold text-secondary">Ảnh hiện tại đang sử dụng</div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 400px;"><?= htmlspecialchars($recipe['image']) ?></small>
                                </div>
                            <?php else: ?>
                                <div class="avatar-preview d-flex align-items-center justify-content-center bg-white text-muted" style="width: 80px; height: 80px;">
                                    <i class="fa-solid fa-image fs-3"></i>
                                </div>
                                <span class="text-muted small">Chưa có hình ảnh minh họa</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">Tải file ảnh mới (nếu muốn thay thế)</label>
                                <input type="file" name="image" class="form-control heroui-input" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">Hoặc nhập link URL ảnh trực tuyến mới</label>
                                <input type="url" name="image_url" class="form-control heroui-input" placeholder="https://..." value="<?= (strpos($recipe['image'], 'http') === 0) ? htmlspecialchars($recipe['image']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <!-- KHỐI QUẢN LÝ NGUYÊN LIỆU ĐỘNG CHUẨN HEROUI -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <label class="heroui-label m-0"><i class="fa-solid fa-carrot text-warning me-1"></i> Danh sách nguyên liệu</label>
                                <small class="text-muted d-block">Bạn có thể thêm, bớt hoặc chỉnh sửa định lượng từng món</small>
                            </div>
                            <button type="button" class="btn btn-sm heroui-btn-light py-1 px-3" id="btnAddIngredient">
                                <i class="fa-solid fa-plus text-primary me-1"></i> Thêm nguyên liệu
                            </button>
                        </div>

                        <div id="ingredientsList">
                            <?php if(!empty($currentIngredients)): ?>
                                <?php foreach($currentIngredients as $idx => $ing): ?>
                                    <div class="ingredient-row d-flex align-items-center gap-2">
                                        <input type="text" name="ingredients[<?= $idx ?>][name]" class="form-control heroui-input" value="<?= htmlspecialchars($ing['name']) ?>" placeholder="Tên nguyên liệu" style="flex: 2;">
                                        
                                        <!-- Bộ điều khiển tăng giảm định lượng HeroUI Stepper -->
                                        <div class="heroui-stepper" style="flex: 1.6;">
                                            <button type="button" class="heroui-stepper-btn btn-step-down" title="Giảm định lượng">−</button>
                                            <input type="text" name="ingredients[<?= $idx ?>][quantity]" class="form-control heroui-input ing-qty-input" value="<?= htmlspecialchars($ing['quantity']) ?>" placeholder="Số lượng">
                                            <button type="button" class="heroui-stepper-btn btn-step-up" title="Tăng định lượng">+</button>
                                        </div>

                                        <input type="text" name="ingredients[<?= $idx ?>][unit]" class="form-control heroui-input" value="<?= htmlspecialchars($ing['unit']) ?>" placeholder="Đơn vị (g, ml...)" style="flex: 1;">
                                        <button type="button" class="btn btn-sm btn-link text-danger p-1 btn-remove-ing" title="Xóa"><i class="fa-solid fa-trash-can"></i></button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
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
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="heroui-input-group mb-3">
                        <label class="heroui-label">Mô tả ngắn <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control heroui-input" rows="2" required><?= htmlspecialchars($recipe['description']) ?></textarea>
                    </div>

                    <div class="heroui-input-group mb-4">
                        <label class="heroui-label">Các bước thực hiện <span class="text-danger">*</span></label>
                        <textarea name="instructions" class="form-control heroui-input" rows="7" required><?= htmlspecialchars($recipe['instructions']) ?></textarea>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="admin.php" class="heroui-btn-light w-50 py-3 text-center text-decoration-none">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="heroui-btn-primary w-50 py-3">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi công thức
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JS Thêm / Xóa / Tăng giảm định lượng nguyên liệu
        let ingredientIndex = <?= count($currentIngredients) > 0 ? count($currentIngredients) : 1 ?>;
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
            // Nếu giá trị lớn (>= 50 như gam/ml) thì tăng giảm bước nhảy 50 hoặc 10, nếu nhỏ thì bước 1
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
