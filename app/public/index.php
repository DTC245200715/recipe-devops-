<?php
require_once __DIR__ . '/../src/db.php';

// Pagination
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Search and Filter
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';

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

if ($filter === 'quick15') {
    // Lọc các món nấu nhanh <= 15 phút hoặc thuộc danh mục món nhanh
    $whereClauses[] = "(r.cook_time <= 15 OR r.category_id = 12)";
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

// Fetch recipes with rating
$sql = "SELECT r.*, c.name as category_name,
               COALESCE(rev.avg_rating, r.rating, 5.0) as dynamic_rating,
               COALESCE(rev.review_count, r.rating_count, 0) as dynamic_rating_count
        FROM recipes r 
        LEFT JOIN categories c ON r.category_id = c.id 
        LEFT JOIN (
            SELECT recipe_id, ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as review_count
            FROM recipe_reviews
            GROUP BY recipe_id
        ) rev ON r.id = rev.recipe_id
        $whereSql 
        ORDER BY r.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll();

// Fetch all categories for filter dropdown & mapping
$stmtCat = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmtCat->fetchAll();

$catMap = [];
foreach ($categories as $cat) {
    $catMap[$cat['slug']] = (int)$cat['id'];
}

// Cấu hình danh sách thẻ Icon / Lưới (Grid) trực quan
$visualCategories = [
    [
        'title' => 'Tất cả món',
        'slug' => 'all',
        'icon' => 'fa-utensils',
        'color' => '#006FEE',
        'bg' => 'rgba(0, 111, 238, 0.1)',
        'tag' => 'Đa dạng',
        'link' => 'index.php',
        'active' => ($categoryId === 0 && empty($filter) && empty($search))
    ],
    [
        'title' => 'Bữa sáng',
        'slug' => 'bua-sang',
        'icon' => 'fa-egg',
        'color' => '#F5A524',
        'bg' => 'rgba(245, 165, 36, 0.12)',
        'tag' => 'Năng lượng',
        'link' => 'index.php?category=' . ($catMap['bua-sang'] ?? 8),
        'active' => ($categoryId === ($catMap['bua-sang'] ?? 8))
    ],
    [
        'title' => 'Bữa trưa',
        'slug' => 'bua-trua',
        'icon' => 'fa-bowl-rice',
        'color' => '#17C964',
        'bg' => 'rgba(23, 201, 100, 0.12)',
        'tag' => 'Thịnh soạn',
        'link' => 'index.php?category=' . ($catMap['bua-trua'] ?? 9),
        'active' => ($categoryId === ($catMap['bua-trua'] ?? 9))
    ],
    [
        'title' => 'Bữa tối',
        'slug' => 'bua-toi',
        'icon' => 'fa-bowl-food',
        'color' => '#7828C8',
        'bg' => 'rgba(120, 40, 200, 0.12)',
        'tag' => 'Ấm cúng',
        'link' => 'index.php?category=' . ($catMap['bua-toi'] ?? 10),
        'active' => ($categoryId === ($catMap['bua-toi'] ?? 10))
    ],
    [
        'title' => 'Món Á',
        'slug' => 'mon-a',
        'icon' => 'fa-dragon',
        'color' => '#E11D48',
        'bg' => 'rgba(225, 29, 72, 0.12)',
        'tag' => 'Truyền thống',
        'link' => 'index.php?category=' . ($catMap['mon-a'] ?? 6),
        'active' => ($categoryId === ($catMap['mon-a'] ?? 6))
    ],
    [
        'title' => 'Món Âu',
        'slug' => 'mon-au',
        'icon' => 'fa-pizza-slice',
        'color' => '#0284C7',
        'bg' => 'rgba(2, 132, 199, 0.12)',
        'tag' => 'Sang trọng',
        'link' => 'index.php?category=' . ($catMap['mon-au'] ?? 7),
        'active' => ($categoryId === ($catMap['mon-au'] ?? 7))
    ],
    [
        'title' => 'Ăn kiêng / Keto',
        'slug' => 'an-kieng-keto',
        'icon' => 'fa-leaf',
        'color' => '#10B981',
        'bg' => 'rgba(16, 185, 129, 0.12)',
        'tag' => 'Healthy',
        'link' => 'index.php?category=' . ($catMap['an-kieng-keto'] ?? 11),
        'active' => ($categoryId === ($catMap['an-kieng-keto'] ?? 11))
    ],
    [
        'title' => 'Nhanh < 15 phút',
        'slug' => 'mon-nhanh',
        'icon' => 'fa-bolt-lightning',
        'color' => '#F97316',
        'bg' => 'rgba(249, 115, 22, 0.12)',
        'tag' => 'Cấp tốc',
        'link' => 'index.php?filter=quick15',
        'active' => ($filter === 'quick15' || $categoryId === ($catMap['mon-nhanh-15p'] ?? 12))
    ]
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bếp Ngon - Chia sẻ Công Thức Nấu Ăn Chuẩn Vị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <span class="brand-icon-box"><i class="fa-solid fa-utensils"></i></span>
                <span>Bếp Ngon</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fa-solid fa-user-gear me-1"></i>Quản trị</a></li>
                        <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i>Đăng xuất</a></li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-sm heroui-btn-primary px-3 py-2 text-decoration-none" href="login.php">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Đăng nhập
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <!-- Hero Banner Section -->
        <div class="hero-section" onclick="window.location.href='index.php'" style="cursor: pointer;" title="Bấm để xem tất cả công thức">
            <h1 class="hero-title">Khám Phá Thế Giới Ẩm Thực</h1>
            <p class="hero-subtitle">Tìm kiếm, lưu trữ và chia sẻ những công thức nấu ăn tuyệt vời nhất cho gia đình bạn.</p>
        </div>

        <!-- Search Bar with Live Filter (Đã bỏ dropdown danh mục theo yêu cầu, đồng bộ với lưới Icon bên dưới) -->
        <div class="row mb-4 justify-content-center">
            <div class="col-lg-7 col-md-9">
                <form method="GET" action="index.php">
                    <div class="input-group search-input-group">
                        <span class="input-group-text">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input class="form-control" type="search" name="q" placeholder="Tìm kiếm món phở, bún chả, mỳ ý, canh chua..." value="<?= htmlspecialchars($search) ?>" id="searchInput" autofocus autocomplete="off">
                        <?php if($categoryId > 0): ?>
                            <input type="hidden" name="category" value="<?= $categoryId ?>">
                        <?php endif; ?>
                        <?php if(!empty($filter)): ?>
                            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                        <?php endif; ?>
                        <button class="btn btn-primary-custom" type="submit">Tìm kiếm</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- PHẦN DANH MỤC HIỂN THỊ DẠNG THẺ ICON / LƯỚI (GRID) TRỰC QUAN -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="category-section-title m-0">
                    <i class="fa-solid fa-layer-group text-primary me-2"></i>Danh mục món ăn
                </h5>
                <span class="text-muted small">Bấm chọn để lọc món nhanh</span>
            </div>
            <div class="category-grid">
                <?php foreach($visualCategories as $vCat): ?>
                    <a href="<?= $vCat['link'] ?>" class="category-card <?= $vCat['active'] ? 'active' : '' ?>">
                        <div class="category-icon-box" style="background: <?= $vCat['bg'] ?>; color: <?= $vCat['color'] ?>;">
                            <i class="fa-solid <?= $vCat['icon'] ?>"></i>
                        </div>
                        <div class="category-name"><?= htmlspecialchars($vCat['title']) ?></div>
                        <div class="category-tag"><?= htmlspecialchars($vCat['tag']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Section Title & Results Count -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0 text-dark">
                <?php 
                if ($search !== '') {
                    echo 'Kết quả tìm kiếm cho: "<span class="text-primary">' . htmlspecialchars($search) . '</span>"';
                } elseif ($categoryId > 0) {
                    $currentCatName = 'Danh mục';
                    foreach($categories as $c) {
                        if ($c['id'] == $categoryId) { $currentCatName = $c['name']; break; }
                    }
                    echo '<i class="fa-solid fa-tag text-primary me-2"></i>' . htmlspecialchars($currentCatName);
                } elseif ($filter === 'quick15') {
                    echo '<i class="fa-solid fa-bolt-lightning text-warning me-2"></i>Món nấu nhanh dưới 15 phút';
                } else {
                    echo '<i class="fa-solid fa-fire text-danger me-2"></i>Công thức món ăn mới nhất';
                }
                ?>
            </h4>
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                <strong><?= $totalRecipes ?></strong> món ăn
            </span>
        </div>

        <!-- Danh sách thẻ món ăn (Recipe Cards Grid) -->
        <div class="row g-4">
            <?php if(count($recipes) > 0): ?>
                <?php foreach($recipes as $recipe): ?>
                    <?php 
                    $score = !empty($recipe['dynamic_rating']) ? (float)$recipe['dynamic_rating'] : (!empty($recipe['rating']) ? (float)$recipe['rating'] : 5.0);
                    $reviews = !empty($recipe['dynamic_rating_count']) ? (int)$recipe['dynamic_rating_count'] : (!empty($recipe['rating_count']) ? (int)$recipe['rating_count'] : 0);
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="recipe.php?id=<?= $recipe['id'] ?>" class="text-decoration-none">
                            <div class="card recipe-card">
                                <?php if($recipe['image']): ?>
                                    <img src="<?= htmlspecialchars($recipe['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($recipe['title']) ?>" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1498837167922-ddd27525d352?q=80&w=600&auto=format&fit=crop';">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?q=80&w=600&auto=format&fit=crop" class="card-img-top" alt="Bếp Ngon">
                                <?php endif; ?>
                                
                                <div class="card-body d-flex flex-column">
                                    <div>
                                        <!-- Danh mục & Đánh giá sao -->
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge-category m-0"><?= htmlspecialchars($recipe['category_name'] ?? 'Món chính') ?></span>
                                            
                                            <!-- THIẾT KẾ ĐÁNH GIÁ SAO CHUẨN HEROUI -->
                                            <div class="heroui-rating-badge" title="Đánh giá chất lượng: <?= number_format($score, 1) ?> / 5 sao (<?= $reviews ?> đánh giá)">
                                                <i class="fa-solid fa-star star-icon"></i>
                                                <span class="rating-num"><?= number_format($score, 1) ?></span>
                                                <span class="rating-reviews">(<?= $reviews ?>)</span>
                                            </div>
                                        </div>

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
                    <p class="text-muted">Hãy thử tìm từ khóa khác hoặc quay lại xem tất cả món ăn.</p>
                    <a href="index.php" class="btn heroui-btn-primary px-4 py-2 mt-2">Xem tất cả công thức</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Phân trang (Pagination) -->
        <?php if($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&category=<?= $categoryId ?><?= !empty($filter) ? '&filter=' . urlencode($filter) : '' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <!-- CHÂN TRANG (FOOTER) CHUẨN HEROUI -->
    <footer class="heroui-footer mt-5 pt-5">
        <div class="container">
            <div class="row g-4 pb-5">
                <!-- Cột 1: Thông tin bản quyền, bài viết giới thiệu ngắn về website -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand mb-3">
                        <a href="index.php" class="d-inline-flex align-items-center text-decoration-none">
                            <span class="brand-icon-box me-2"><i class="fa-solid fa-utensils"></i></span>
                            <span class="fw-bold fs-4 text-primary">Bếp Ngon</span>
                        </a>
                    </div>
                    <p class="footer-intro text-muted mb-3" style="font-size: 0.9rem; line-height: 1.6;">
                        <strong>Bếp Ngon</strong> là nền tảng chia sẻ và lưu trữ công thức nấu ăn trực tuyến dành cho những người yêu ẩm thực. Từ các món ăn thuần Việt truyền thống đến tinh hoa ẩm thực Á - Âu, chúng tôi mang đến bí quyết và niềm cảm hứng vào từng gian bếp gia đình.
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <span class="heroui-badge heroui-badge-success"><i class="fa-solid fa-shield-halved me-1"></i> DevOps Verified</span>
                        <span class="heroui-badge heroui-badge-primary"><i class="fa-solid fa-circle-check me-1"></i> Chuẩn Công Thức</span>
                    </div>
                </div>

                <!-- Cột 2: Khám phá món ngon & Danh mục -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title fw-bold mb-3 text-dark">Khám phá món ngon</h6>
                    <ul class="list-unstyled footer-menu">
                        <li><a href="index.php?category=<?= $catMap['bua-sang'] ?? 8 ?>"><i class="fa-solid fa-angle-right me-2 text-primary"></i>Bữa sáng giàu năng lượng</a></li>
                        <li><a href="index.php?category=<?= $catMap['bua-trua'] ?? 9 ?>"><i class="fa-solid fa-angle-right me-2 text-primary"></i>Bữa trưa đậm đà dinh dưỡng</a></li>
                        <li><a href="index.php?category=<?= $catMap['bua-toi'] ?? 10 ?>"><i class="fa-solid fa-angle-right me-2 text-primary"></i>Bữa tối ấm cúng gia đình</a></li>
                        <li><a href="index.php?category=<?= $catMap['mon-a'] ?? 6 ?>"><i class="fa-solid fa-angle-right me-2 text-primary"></i>Món Á truyền thống & đa dạng</a></li>
                        <li><a href="index.php?category=<?= $catMap['mon-au'] ?? 7 ?>"><i class="fa-solid fa-angle-right me-2 text-primary"></i>Món Âu tinh tế & hiện đại</a></li>
                        <li><a href="index.php?category=<?= $catMap['an-kieng-keto'] ?? 11 ?>"><i class="fa-solid fa-angle-right me-2 text-primary"></i>Món ăn kiêng / Eat Clean & Keto</a></li>
                        <li><a href="index.php?filter=quick15"><i class="fa-solid fa-angle-right me-2 text-primary"></i>Món nấu nhanh &lt; 15 phút</a></li>
                    </ul>
                </div>

                <!-- Cột 3: Liên kết nhanh (Sitemap) -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-title fw-bold mb-3 text-dark">Liên kết nhanh (Sitemap)</h6>
                    <ul class="list-unstyled footer-menu">
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal"><i class="fa-solid fa-lock me-2 text-muted"></i>Chính sách bảo mật</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#termsModal"><i class="fa-solid fa-file-contract me-2 text-muted"></i>Điều khoản sử dụng</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#contactModal"><i class="fa-solid fa-headset me-2 text-muted"></i>Liên hệ hỗ trợ</a></li>
                        <li><a href="admin.php"><i class="fa-solid fa-user-shield me-2 text-muted"></i>Cổng Quản trị viên</a></li>
                    </ul>
                </div>

                <!-- Cột 4: Mạng xã hội (Facebook, Youtube, TikTok...) & Liên hệ -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title fw-bold mb-3 text-dark">Mạng xã hội</h6>
                    <p class="text-muted small mb-3">Theo dõi Bếp Ngon trên các mạng xã hội để cập nhật mẹo vặt và video nấu ăn mới mỗi ngày:</p>
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="social-btn social-facebook" title="Theo dõi Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="social-btn social-youtube" title="Kênh YouTube"><i class="fa-brands fa-youtube"></i></a>
                        <a href="https://tiktok.com" target="_blank" rel="noopener noreferrer" class="social-btn social-tiktok" title="Kênh TikTok"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="social-btn social-instagram" title="Kênh Instagram"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                    <div class="footer-contact-info text-muted small">
                        <div class="mb-1"><i class="fa-solid fa-envelope me-2 text-primary"></i>contact@bepngon.vn</div>
                        <div class="mb-1"><i class="fa-solid fa-phone me-2 text-primary"></i>Hotline: 1900 6868</div>
                        <div><i class="fa-solid fa-location-dot me-2 text-primary"></i>Hà Nội & TP. Hồ Chí Minh</div>
                    </div>
                </div>
            </div>

            <!-- Dải Bottom Bar Bản quyền -->
            <div class="footer-bottom py-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="text-muted small">
                    © 2026 <strong>Bếp Ngon Platform</strong>. Tất cả quyền được bảo lưu. Phát triển trên nền tảng Recipe DevOps & HeroUI System.
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center gap-1 small text-success">
                        <span class="status-pulse-green"></span> Hệ thống đang vận hành ổn định
                    </span>
                    <span class="text-muted small">Phiên bản 2.5.0</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- MODAL 1: CHÍNH SÁCH BẢO MẬT -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content heroui-card border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-lock me-2"></i>Chính sách bảo mật thông tin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="line-height: 1.7; font-size: 0.95rem;">
                    <h6 class="fw-bold text-dark">1. Thu thập thông tin</h6>
                    <p class="text-muted">Bếp Ngon cam kết chỉ thu thập các thông tin tối thiểu cần thiết phục vụ quá trình trải nghiệm và quản lý công thức (như tài khoản người dùng, bình luận và đánh giá món ăn).</p>
                    
                    <h6 class="fw-bold text-dark">2. Bảo vệ và Mã hóa dữ liệu</h6>
                    <p class="text-muted">Mật khẩu và thông tin đăng nhập đều được mã hóa theo chuẩn bảo mật Bcrypt hiện đại. Hệ thống mạng và cơ sở dữ liệu được triển khai theo mô hình Container cô lập an toàn.</p>
                    
                    <h6 class="fw-bold text-dark">3. Quyền của người dùng</h6>
                    <p class="text-muted">Người dùng có quyền chỉnh sửa, cập nhật hoặc yêu cầu xóa bỏ thông tin cá nhân và công thức do mình đăng tải bất kỳ lúc nào.</p>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn heroui-btn-primary px-4 py-2" data-bs-dismiss="modal">Đã hiểu & Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: ĐIỀU KHOẢN SỬ DỤNG -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content heroui-card border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-file-contract me-2"></i>Điều khoản sử dụng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="line-height: 1.7; font-size: 0.95rem;">
                    <h6 class="fw-bold text-dark">1. Bản quyền nội dung công thức</h6>
                    <p class="text-muted">Mọi bài viết, hướng dẫn các bước nấu nướng và hình ảnh minh họa trên Bếp Ngon được chia sẻ vì mục đích phi thương mại nhằm lan tỏa niềm yêu thích nấu ăn.</p>
                    
                    <h6 class="fw-bold text-dark">2. Tiêu chuẩn cộng đồng</h6>
                    <p class="text-muted">Người dùng cần tuân thủ văn hóa ứng xử văn minh, không đăng tải nội dung sai lệch, tiêu cực hoặc vi phạm thuần phong mỹ tục Việt Nam.</p>

                    <h6 class="fw-bold text-dark">3. Trách nhiệm thực hiện</h6>
                    <p class="text-muted">Các công thức mang tính chất gợi ý và hướng dẫn kỹ thuật. Người nấu nên kiểm tra độ tươi ngon của nguyên liệu và tiền sử dị ứng thực phẩm của bản thân và gia đình.</p>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn heroui-btn-primary px-4 py-2" data-bs-dismiss="modal">Đồng ý Điều khoản</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: LIÊN HỆ & GÓP Ý -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content heroui-card border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-headset me-2"></i>Liên hệ & Hỗ trợ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="contactForm" onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã gửi liên hệ! Đội ngũ Bếp Ngon sẽ phản hồi sớm nhất qua email.'); bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();">
                        <div class="mb-3">
                            <label class="heroui-label">Họ và tên của bạn</label>
                            <input type="text" class="form-control heroui-input" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="mb-3">
                            <label class="heroui-label">Địa chỉ Email</label>
                            <input type="email" class="form-control heroui-input" placeholder="email@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="heroui-label">Nội dung góp ý / Yêu cầu</label>
                            <textarea class="form-control heroui-input" rows="4" placeholder="Chia sẻ cảm nhận hoặc công thức bạn muốn chúng tôi bổ sung..." required></textarea>
                        </div>
                        <button type="submit" class="btn heroui-btn-primary w-100 py-2">
                            <i class="fa-solid fa-paper-plane me-1"></i> Gửi tin nhắn ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('searchInput');
            if(searchInput) {
                // Di chuyển con trỏ chuột về cuối xâu khi tải trang
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
                    }, 500); // Tự động tìm sau 500ms dừng gõ
                });
            }
        });
    </script>
</body>
</html>
