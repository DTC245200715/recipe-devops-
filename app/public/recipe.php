<?php
require_once __DIR__ . '/../src/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Xử lý khi người dùng gửi đánh giá sao
$flashSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rate') {
    $ratingVal = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
    if ($ratingVal < 1) $ratingVal = 1;
    if ($ratingVal > 5) $ratingVal = 5;
    $userName = !empty($_POST['user_name']) ? trim($_POST['user_name']) : 'Thực khách Bếp Ngon';
    $comment = !empty($_POST['comment']) ? trim($_POST['comment']) : '';

    // Lưu vào bảng recipe_reviews
    $stmtInsRev = $pdo->prepare("INSERT INTO recipe_reviews (recipe_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
    $stmtInsRev->execute([$id, $userName, $ratingVal, $comment]);

    // Tính toán lại điểm món = trung bình cộng các sao mà cộng đồng đánh giá
    $stmtAgg = $pdo->prepare("SELECT ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as total_reviews FROM recipe_reviews WHERE recipe_id = ?");
    $stmtAgg->execute([$id]);
    $aggData = $stmtAgg->fetch();
    
    $newRating = !empty($aggData['avg_rating']) ? (float)$aggData['avg_rating'] : (float)$ratingVal;
    $newCount = !empty($aggData['total_reviews']) ? (int)$aggData['total_reviews'] : 1;

    $stmtUpd = $pdo->prepare("UPDATE recipes SET rating = ?, rating_count = ? WHERE id = ?");
    $stmtUpd->execute([$newRating, $newCount, $id]);

    $_SESSION['flash_rating'] = "Cảm ơn bạn đã đánh giá món ăn này {$ratingVal} sao!";
    header("Location: recipe.php?id=$id#reviewsSection");
    exit;
}

if (!empty($_SESSION['flash_rating'])) {
    $flashSuccess = $_SESSION['flash_rating'];
    unset($_SESSION['flash_rating']);
}

// Lấy thông tin công thức cùng điểm trung bình cộng thực tế từ bảng đánh giá cộng đồng
$stmt = $pdo->prepare("
    SELECT r.*, c.name as category_name,
           ROUND(AVG(rv.rating), 1) as avg_rating,
           COUNT(rv.id) as review_count
    FROM recipes r 
    LEFT JOIN categories c ON r.category_id = c.id 
    LEFT JOIN recipe_reviews rv ON r.id = rv.recipe_id
    WHERE r.id = ?
    GROUP BY r.id
");
$stmt->execute([$id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header('Location: index.php');
    exit;
}

// Lấy nguyên liệu
$stmtIng = $pdo->prepare("SELECT i.name, ri.quantity, ri.unit FROM recipe_ingredients ri JOIN ingredients i ON ri.ingredient_id = i.id WHERE ri.recipe_id = ?");
$stmtIng->execute([$id]);
$ingredients = $stmtIng->fetchAll();

// Lấy danh sách đánh giá từ người dùng
$stmtReviews = $pdo->prepare("SELECT * FROM recipe_reviews WHERE recipe_id = ? ORDER BY created_at DESC LIMIT 15");
$stmtReviews->execute([$id]);
$reviewsList = $stmtReviews->fetchAll();

// Lấy thông số dinh dưỡng (Nutritional Information)
$stmtNutr = $pdo->prepare("SELECT * FROM recipe_nutrition WHERE recipe_id = ?");
$stmtNutr->execute([$id]);
$nutrition = $stmtNutr->fetch();

if ($nutrition) {
    // Giá trị tham chiếu chuẩn hàng ngày (Daily Value - 2000 kcal)
    $pctCalories = min(100, round(($nutrition['calories'] / 2000) * 100));
    $pctProtein = min(100, round(($nutrition['protein'] / 50) * 100));
    $pctFat = min(100, round(($nutrition['fat'] / 78) * 100));
    $pctCarbs = min(100, round(($nutrition['carbs'] / 275) * 100));
    $pctFiber = min(100, round(($nutrition['fiber'] / 28) * 100));
    $pctSodium = min(100, round(($nutrition['sodium'] / 2300) * 100));

    // Tính tỷ lệ Macro Calories (Protein 4kcal/g, Carbs 4kcal/g, Fat 9kcal/g)
    $calP = $nutrition['protein'] * 4;
    $calC = $nutrition['carbs'] * 4;
    $calF = $nutrition['fat'] * 9;
    $totalMacro = max(1, $calP + $calC + $calF);
    $macroPPct = round(($calP / $totalMacro) * 100);
    $macroCPct = round(($calC / $totalMacro) * 100);
    $macroFPct = max(0, 100 - $macroPPct - $macroCPct);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recipe['title']) ?> - Bếp Ngon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <span class="brand-icon-box"><i class="fa-solid fa-utensils"></i></span>
                <span>Bếp Ngon</span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2"><i class="fa-solid fa-arrow-left me-1"></i> Trở về trang chủ</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <?php if(!empty($flashSuccess)): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                <strong>Tuyệt vời!</strong> <?= htmlspecialchars($flashSuccess) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="detail-header">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <?php if($recipe['image']): ?>
                        <img src="<?= htmlspecialchars($recipe['image']) ?>" class="detail-img" alt="<?= htmlspecialchars($recipe['title']) ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1498837167922-ddd27525d352?q=80&w=800&auto=format&fit=crop';">
                    <?php else: ?>
                        <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?q=80&w=800&auto=format&fit=crop" class="detail-img" alt="Bếp Ngon">
                    <?php endif; ?>
                </div>
                <div class="col-lg-6 px-lg-4">
                    <?php 
                    $score = !empty($recipe['avg_rating']) ? (float)$recipe['avg_rating'] : (!empty($recipe['rating']) ? (float)$recipe['rating'] : 5.0);
                    $reviews = !empty($recipe['review_count']) ? (int)$recipe['review_count'] : (!empty($recipe['rating_count']) ? (int)$recipe['rating_count'] : 0);
                    ?>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge-category m-0"><?= htmlspecialchars($recipe['category_name'] ?? 'Món chính') ?></span>
                        <div class="heroui-rating-badge" title="Đánh giá chất lượng: <?= number_format($score, 1) ?> / 5 sao (<?= $reviews ?> đánh giá)">
                            <i class="fa-solid fa-star star-icon"></i>
                            <span class="rating-num"><?= number_format($score, 1) ?></span>
                            <span class="rating-reviews">(<?= $reviews ?> đánh giá)</span>
                        </div>
                    </div>
                    <h1 class="recipe-title" style="font-size: 2.2rem;"><?= htmlspecialchars($recipe['title']) ?></h1>
                    <p class="recipe-desc" style="font-size: 1.05rem;"><?= htmlspecialchars($recipe['description']) ?></p>
                    
                    <div class="detail-meta-box">
                        <div class="meta-box-item">
                            <i class="fa-regular fa-clock fa-2x mb-2 text-warning"></i>
                            <strong><?= $recipe['cook_time'] ?>p</strong>
                            <span>Thời gian</span>
                        </div>
                        <div class="meta-box-item border-start border-end px-3">
                            <i class="fa-solid fa-users fa-2x mb-2 text-info"></i>
                            <strong><?= $recipe['servings'] ?></strong>
                            <span>Khẩu phần</span>
                        </div>
                        <div class="meta-box-item <?= !empty($nutrition['calories']) ? 'border-end pe-3' : '' ?>">
                            <i class="fa-solid fa-fire-burner fa-2x mb-2 text-danger"></i>
                            <strong><?= htmlspecialchars($recipe['difficulty']) ?></strong>
                            <span>Độ khó</span>
                        </div>
                        <?php if(!empty($nutrition['calories'])): ?>
                        <div class="meta-box-item ps-3">
                            <i class="fa-solid fa-bolt fa-2x mb-2 text-success"></i>
                            <strong><?= number_format($nutrition['calories']) ?></strong>
                            <span>Kcal / phần</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- HÀNG 1: NGUYÊN LIỆU & CÁC BƯỚC THỰC HIỆN (KHÔNG KHOẢNG TRẮNG THỪA) -->
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <div class="content-card h-100 mb-0">
                    <h4 class="content-title"><i class="fa-solid fa-basket-shopping"></i> Nguyên liệu</h4>
                    <ul class="list-group ingredient-list">
                        <?php if(count($ingredients) > 0): ?>
                            <?php foreach($ingredients as $ing): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($ing['name']) ?></span>
                                    <span class="badge bg-light text-dark border fw-bold px-3 py-2 rounded-pill">
                                        <?= htmlspecialchars($ing['quantity'] . ' ' . $ing['unit']) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-muted">Chưa có thông tin nguyên liệu chi tiết.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="content-card h-100 mb-0">
                    <h4 class="content-title"><i class="fa-solid fa-list-check"></i> Các bước thực hiện</h4>
                    <div class="instructions-wrapper mt-3">
                        <?php 
                            $instructions = explode("\n", $recipe['instructions']);
                            $stepNum = 1;
                            foreach($instructions as $step) {
                                if(trim($step) !== '') {
                                    echo '<div class="instruction-step mb-3">';
                                    echo '<div class="step-number">' . $stepNum . '</div>';
                                    echo '<div class="step-text">' . nl2br(htmlspecialchars(trim($step))) . '</div>';
                                    echo '</div>';
                                    $stepNum++;
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- HÀNG 2: BẢNG THÔNG SỐ DINH DƯỠNG (NUTRITIONAL INFORMATION) DẠNG NGANG CHUẨN HEROUI -->
        <?php if($nutrition): ?>
        <div class="content-card mb-5">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-4 text-danger"><i class="fa-solid fa-heart-pulse"></i></span>
                    <div>
                        <h4 class="fw-bold m-0 text-dark" style="font-size: 1.25rem;">Bảng thông số Dinh dưỡng (Nutritional Information)</h4>
                        <small class="text-muted">Ước tính tiêu chuẩn dinh dưỡng dựa trên 1 khẩu phần ăn của món</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
                        <i class="fa-solid fa-utensils me-1"></i> 1 Khẩu phần (Serving)
                    </span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-semibold">
                        <i class="fa-solid fa-users me-1"></i> Món ăn: <?= $recipe['servings'] ?> phần
                    </span>
                </div>
            </div>

            <div class="row g-4 align-items-center">
                <!-- Cột trái: Năng lượng (Calories) + Macro Distribution Bar -->
                <div class="col-lg-4 border-end-lg pe-lg-4">
                    <div class="p-3 rounded-4" style="background: linear-gradient(135deg, rgba(0, 111, 238, 0.08) 0%, rgba(245, 165, 36, 0.1) 100%); border: 1px solid rgba(0, 111, 238, 0.15);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tổng Năng Lượng</span>
                                <div class="display-6 fw-bold text-dark m-0" style="line-height: 1.1;">
                                    <?= number_format($nutrition['calories']) ?> <span class="fs-6 fw-semibold text-secondary">kcal</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge rounded-pill text-white px-3 py-2 fw-bold" style="background: #F5A524; font-size: 0.85rem;" title="Phần trăm năng lượng khuyến nghị hàng ngày">
                                    <?= $pctCalories ?>% DV*
                                </span>
                            </div>
                        </div>

                        <!-- Macro Ratio Distribution Bar -->
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 0.75rem;">
                                <span><i class="fa-solid fa-circle text-primary me-1" style="font-size: 8px;"></i>Đạm <?= $macroPPct ?>%</span>
                                <span><i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i>Carbs <?= $macroCPct ?>%</span>
                                <span><i class="fa-solid fa-circle text-warning me-1" style="font-size: 8px;"></i>Béo <?= $macroFPct ?>%</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 999px; overflow: hidden; background: #e4e4e7;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $macroPPct ?>%" title="Protein <?= $macroPPct ?>%"></div>
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $macroCPct ?>%" title="Carbs <?= $macroCPct ?>%"></div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $macroFPct ?>%" title="Fat <?= $macroFPct ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: 5 ô chỉ số Dinh dưỡng Grid chuẩn HeroUI -->
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-4 col-6">
                            <div class="p-3 rounded-4 bg-light border text-center h-100 d-flex flex-column justify-content-between">
                                <span class="text-muted small fw-semibold">Chất đạm (Protein)</span>
                                <div class="fs-4 fw-bold text-dark my-1"><?= $nutrition['protein'] ?>g</div>
                                <div><span class="badge bg-primary-subtle text-primary border rounded-pill px-2 py-1" style="font-size: 0.7rem;"><?= $pctProtein ?>% Daily Value</span></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="p-3 rounded-4 bg-light border text-center h-100 d-flex flex-column justify-content-between">
                                <span class="text-muted small fw-semibold">Chất béo (Lipid / Fat)</span>
                                <div class="fs-4 fw-bold text-dark my-1"><?= $nutrition['fat'] ?>g</div>
                                <div><span class="badge bg-warning-subtle text-warning border rounded-pill px-2 py-1" style="font-size: 0.7rem;"><?= $pctFat ?>% Daily Value</span></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="p-3 rounded-4 bg-light border text-center h-100 d-flex flex-column justify-content-between">
                                <span class="text-muted small fw-semibold">Tinh bột (Carbs)</span>
                                <div class="fs-4 fw-bold text-dark my-1"><?= $nutrition['carbs'] ?>g</div>
                                <div><span class="badge bg-success-subtle text-success border rounded-pill px-2 py-1" style="font-size: 0.7rem;"><?= $pctCarbs ?>% Daily Value</span></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="p-3 rounded-4 bg-light border text-center h-100 d-flex flex-column justify-content-between">
                                <span class="text-muted small fw-semibold">Chất xơ (Dietary Fiber)</span>
                                <div class="fs-4 fw-bold text-dark my-1"><?= $nutrition['fiber'] ?>g</div>
                                <div><span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1" style="font-size: 0.7rem;"><?= $pctFiber ?>% Daily Value</span></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="p-3 rounded-4 bg-light border text-center h-100 d-flex flex-column justify-content-between">
                                <span class="text-muted small fw-semibold">Natri (Sodium)</span>
                                <div class="fs-4 fw-bold text-dark my-1"><?= $nutrition['sodium'] ?>mg</div>
                                <div><span class="badge bg-danger-subtle text-danger border rounded-pill px-2 py-1" style="font-size: 0.7rem;"><?= $pctSodium ?>% Daily Value</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                <span>* % Giá trị Dinh dưỡng Hàng ngày (% Daily Value) dựa trên nhu cầu dinh dưỡng chuẩn 2.000 calo/ngày.</span>
                <span class="text-secondary"><i class="fa-solid fa-circle-info me-1"></i>Chuẩn hóa theo Viện Dinh Dưỡng Quốc Gia</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- PHẦN ĐÁNH GIÁ SAO VÀ BÌNH LUẬN DÀNH CHO NGƯỜI DÙNG -->
        <div id="reviewsSection" class="content-card mb-5">
            <div class="row g-4 align-items-start">
                <!-- Cột trái: Form đánh giá sao tương tác -->
                <div class="col-lg-5 border-end pe-lg-4">
                    <h4 class="content-title mb-2">
                        <i class="fa-solid fa-star text-warning"></i> Đánh giá món ăn này
                    </h4>
                    <p class="text-muted small mb-3">Bạn đã làm thử món này chưa? Hãy để lại số sao và nhận xét của bạn nhé!</p>

                    <form method="POST" action="recipe.php?id=<?= $recipe['id'] ?>">
                        <input type="hidden" name="action" value="rate">

                        <!-- Bộ chọn sao trực quan 1-5 sao -->
                        <div class="mb-3 text-center p-3 rounded-4" style="background: #FFFBEB; border: 1px solid #FDE68A;">
                            <label class="d-block text-dark fw-bold mb-2">Chọn mức độ hài lòng của bạn:</label>
                            <div class="rating-stars-input mb-2">
                                <input type="radio" id="star5" name="rating" value="5" checked>
                                <label for="star5" title="5 sao - Xuất sắc, tuyệt vời!"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" id="star4" name="rating" value="4">
                                <label for="star4" title="4 sao - Rất ngon, dễ làm!"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" id="star3" name="rating" value="3">
                                <label for="star3" title="3 sao - Ăn vừa miệng"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" id="star2" name="rating" value="2">
                                <label for="star2" title="2 sao - Hơi khó làm"><i class="fa-solid fa-star"></i></label>
                                <input type="radio" id="star1" name="rating" value="1">
                                <label for="star1" title="1 sao - Cần cải thiện"><i class="fa-solid fa-star"></i></label>
                            </div>
                            <div id="starHint" class="small fw-semibold text-warning" style="min-height: 20px;">
                                ⭐⭐⭐⭐⭐ Xuất sắc tuyệt vời!
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="heroui-label">Tên của bạn <span class="text-danger">*</span></label>
                            <input type="text" name="user_name" class="form-control heroui-input" placeholder="Ví dụ: Hoàng Anh..." required>
                        </div>

                        <div class="mb-3">
                            <label class="heroui-label">Nhận xét hoặc mẹo nấu ăn của bạn</label>
                            <textarea name="comment" class="form-control heroui-input" rows="3" placeholder="Món này ăn giòn, nước dùng rất đậm đà..."></textarea>
                        </div>

                        <button type="submit" class="btn heroui-btn-primary w-100 py-3">
                            <i class="fa-solid fa-paper-plane me-1"></i> Gửi đánh giá món ăn
                        </button>
                    </form>
                </div>

                <!-- Cột phải: Tổng quan điểm & Danh sách nhận xét gần đây -->
                <div class="col-lg-7 ps-lg-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold m-0 text-dark">Nhận xét từ cộng đồng</h5>
                            <span class="text-muted small">Có <?= count($reviewsList) ?> nhận xét nổi bật gần đây</span>
                        </div>
                        <div class="text-end">
                            <div class="display-6 fw-bold text-dark m-0"><?= number_format($score, 1) ?><span class="fs-5 text-muted">/5</span></div>
                            <div class="text-warning small">
                                <?php 
                                for ($s = 1; $s <= 5; $s++) {
                                    if ($score >= $s) {
                                        echo '<i class="fa-solid fa-star"></i>';
                                    } elseif ($score >= ($s - 0.5)) {
                                        echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                    } else {
                                        echo '<i class="fa-regular fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="reviews-list" style="max-height: 420px; overflow-y: auto;">
                        <?php if(count($reviewsList) > 0): ?>
                            <?php foreach($reviewsList as $rev): ?>
                                <div class="review-item">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="review-avatar">
                                                <?= strtoupper(mb_substr($rev['user_name'], 0, 1, 'UTF-8')) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($rev['user_name']) ?></div>
                                                <small class="text-muted" style="font-size: 0.75rem;"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></small>
                                            </div>
                                        </div>
                                        <div class="text-warning" style="font-size: 0.85rem;">
                                            <?php for($k = 1; $k <= 5; $k++): ?>
                                                <i class="fa-<?= $k <= $rev['rating'] ? 'solid' : 'regular' ?> fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <?php if(!empty($rev['comment'])): ?>
                                        <p class="m-0 text-secondary" style="font-size: 0.88rem; line-height: 1.5;">
                                            <?= nl2br(htmlspecialchars($rev['comment'])) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fa-regular fa-comment-dots fa-3x mb-2 opacity-50"></i>
                                <p class="small">Chưa có đánh giá nào cho món này. Hãy là người đầu tiên trải nghiệm và chấm sao nhé!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CHÂN TRANG ĐỒNG BỘ -->
    <footer class="heroui-footer pt-4 pb-3 mt-5">
        <div class="container text-center">
            <div class="text-muted small">
                © 2026 <strong>Bếp Ngon Platform</strong>. Tất cả quyền được bảo lưu. Phát triển trên nền tảng Recipe DevOps & HeroUI System.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cập nhật nhãn đánh giá khi người dùng click chọn số sao
        const hints = {
            1: "⭐ Cần cải thiện công thức",
            2: "⭐⭐ Hơi khó làm / Chưa hợp khẩu vị",
            3: "⭐⭐⭐ Ăn vừa miệng, ổn áp",
            4: "⭐⭐⭐⭐ Rất ngon, dễ làm tại nhà!",
            5: "⭐⭐⭐⭐⭐ Xuất sắc tuyệt vời, chuẩn vị nhà hàng!"
        };

        document.querySelectorAll('.rating-stars-input input').forEach(radio => {
            radio.addEventListener('change', function() {
                const hintEl = document.getElementById('starHint');
                if (hintEl && hints[this.value]) {
                    hintEl.textContent = hints[this.value];
                }
            });
        });
    </script>
</body>
</html>
