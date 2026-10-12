<?php
require_once __DIR__ . '/../src/db.php';

// Pagination
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search and Filter
$search = isset($_GET['q']) ? $_GET['q'] : '';
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(REPLACE(r.title, ' ', '') COLLATE utf8mb4_unicode_ci LIKE ? OR REPLACE(r.description, ' ', '') COLLATE utf8mb4_unicode_ci LIKE ?)";
    $searchParam = "%" . str_replace(' ', '', $search) . "%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if ($categoryId > 0) {
    $whereClauses[] = "r.category_id = ?";
    $params[] = $categoryId;
}

$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(' AND ', $whereClauses);
}

// Count total
$countSql = "SELECT COUNT(*) FROM recipes r $whereSql";
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute($params);
$totalRecipes = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecipes / $limit);

// Fetch recipes
$sql = "SELECT r.*, c.name as category_name FROM recipes r 
        LEFT JOIN categories c ON r.category_id = c.id 
        $whereSql 
        ORDER BY r.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll();

// Fetch categories for filter
$stmtCat = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmtCat->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bếp Ngon - Chia sẻ Công Thức Nấu Ăn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-utensils me-2"></i>Bếp Ngon</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fa-solid fa-user-gear me-1"></i>Quản trị</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php"><i class="fa-solid fa-right-to-bracket me-1"></i>Đăng nhập</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        
        <div class="hero-section" onclick="window.location.href='index.php'" style="cursor: pointer;" title="Trở về trang chủ">
            <h1 class="hero-title">Khám Phá Thế Giới Ẩm Thực</h1>
            <p class="hero-subtitle">Tìm kiếm, lưu trữ và chia sẻ những công thức nấu ăn tuyệt vời nhất cho gia đình bạn.</p>
        </div>

        <div class="row mb-5 justify-content-center">
            <div class="col-lg-8">
                <form class="d-flex search-form" method="GET" action="index.php">
                    <input class="form-control me-2" type="search" name="q" placeholder="Tìm món phở, canh chua..." value="<?= htmlspecialchars($search) ?>" id="searchInput" autofocus>
                    <select class="form-select me-2" name="category" style="max-width: 200px;" onchange="this.form.submit()">
                        <option value="0">Tất cả danh mục</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary-custom" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <?php if(count($recipes) > 0): ?>
                <?php foreach($recipes as $recipe): ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="recipe.php?id=<?= $recipe['id'] ?>" class="text-decoration-none">
                            <div class="card recipe-card">
                                <?php if($recipe['image']): ?>
                                    <img src="<?= htmlspecialchars($recipe['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($recipe['title']) ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/400x250.png?text=No+Image" class="card-img-top" alt="No image">
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <div>
                                        <span class="badge-category"><?= htmlspecialchars($recipe['category_name'] ?? 'Chưa phân loại') ?></span>
                                        <h5 class="recipe-title"><?= htmlspecialchars($recipe['title']) ?></h5>
                                        <p class="recipe-desc text-truncate-2"><?= htmlspecialchars($recipe['description']) ?></p>
                                    </div>
                                    <div class="mt-auto recipe-meta">
                                        <div class="meta-item"><i class="fa-regular fa-clock"></i> <?= $recipe['cook_time'] ?> phút</div>
                                        <div class="meta-item"><i class="fa-solid fa-fire-burner"></i> <?= htmlspecialchars($recipe['difficulty']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-plate-wheat fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Không tìm thấy công thức nào phù hợp.</h4>
                </div>
            <?php endif; ?>
        </div>

        <?php if($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&category=<?= $categoryId ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('searchInput');
            if(searchInput) {
                // Move cursor to the end of the text
                const val = searchInput.value;
                if (val) {
                    searchInput.value = '';
                    searchInput.value = val;
                }

                let timeout = null;
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        this.form.submit();
                    }, 500); // Wait 500ms after user stops typing to auto-submit
                });
            }
        });
    </script>
</body>
</html>
