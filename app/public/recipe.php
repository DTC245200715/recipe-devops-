<?php
require_once __DIR__ . '/../src/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Lấy thông tin công thức
$stmt = $pdo->prepare("SELECT r.*, c.name as category_name FROM recipes r LEFT JOIN categories c ON r.category_id = c.id WHERE r.id = ?");
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
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-utensils me-2"></i>Bếp Ngon</a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fa-solid fa-arrow-left me-1"></i> Trở về</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="detail-header">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <?php if($recipe['image']): ?>
                        <img src="<?= htmlspecialchars($recipe['image']) ?>" class="detail-img" alt="<?= htmlspecialchars($recipe['title']) ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/600x400.png?text=No+Image" class="detail-img" alt="No image">
                    <?php endif; ?>
                </div>
                <div class="col-lg-6 px-lg-4">
                    <?php 
                    $score = !empty($recipe['rating']) ? (float)$recipe['rating'] : round(4.5 + (($recipe['id'] * 7) % 6) * 0.1, 1);
                    $reviews = !empty($recipe['rating_count']) ? (int)$recipe['rating_count'] : (int)($recipe['id'] * 23 + 47);
                    ?>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge-category m-0"><?= htmlspecialchars($recipe['category_name'] ?? 'Chưa phân loại') ?></span>
                        <div class="heroui-rating-badge" title="Đánh giá chất lượng: <?= number_format($score, 1) ?> / 5 sao (<?= $reviews ?> đánh giá)">
                            <i class="fa-solid fa-star star-icon"></i>
                            <span class="rating-num"><?= number_format($score, 1) ?></span>
                            <span class="rating-reviews">(<?= $reviews ?> đánh giá)</span>
                        </div>
                    </div>
                    <h1 class="recipe-title" style="font-size: 2.5rem;"><?= htmlspecialchars($recipe['title']) ?></h1>
                    <p class="recipe-desc" style="font-size: 1.1rem;"><?= htmlspecialchars($recipe['description']) ?></p>
                    
                    <div class="detail-meta-box">
                        <div class="meta-box-item">
                            <i class="fa-regular fa-clock fa-2x mb-2 text-warning"></i>
                            <strong><?= $recipe['cook_time'] ?>p</strong>
                            <span>Thời gian</span>
                        </div>
                        <div class="meta-box-item border-start border-end px-4">
                            <i class="fa-solid fa-users fa-2x mb-2 text-info"></i>
                            <strong><?= $recipe['servings'] ?></strong>
                            <span>Khẩu phần</span>
                        </div>
                        <div class="meta-box-item">
                            <i class="fa-solid fa-fire-burner fa-2x mb-2 text-danger"></i>
                            <strong><?= htmlspecialchars($recipe['difficulty']) ?></strong>
                            <span>Độ khó</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="content-card">
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
                            <li class="list-group-item text-muted">Chưa có thông tin nguyên liệu.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="content-card">
                    <h4 class="content-title"><i class="fa-solid fa-list-check"></i> Các bước thực hiện</h4>
                    <div class="instructions-wrapper mt-4">
                        <?php 
                            $instructions = explode("\n", $recipe['instructions']);
                            $stepNum = 1;
                            foreach($instructions as $step) {
                                if(trim($step) !== '') {
                                    echo '<div class="instruction-step">';
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
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
