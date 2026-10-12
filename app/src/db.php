<?php
session_start();

$host = 'db';
$db   = getenv('MYSQL_DATABASE') ?: 'recipe_db';
$user = getenv('MYSQL_USER') ?: 'recipe_user';
$pass = getenv('MYSQL_PASSWORD') ?: 'recipe_pass';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Log error to stderr for Docker
    file_put_contents('php://stderr', "[" . date('Y-m-d H:i:s') . "] ERROR: Database connection failed: " . $e->getMessage() . "\n");
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Hàm khởi tạo admin user nếu chưa có
function initAdminUser(\PDO $pdo): void {
    $adminUser = getenv('ADMIN_USERNAME') ?: 'admin';
    $adminPass = getenv('ADMIN_PASSWORD') ?: 'admin123';
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$adminUser]);
    if (!$stmt->fetch()) {
        $hash = password_hash($adminPass, PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
        $insert->execute([$adminUser, $hash]);
        file_put_contents('php://stderr', "[" . date('Y-m-d H:i:s') . "] INFO: Admin user initialized.\n");
    }
}

// Gọi hàm khởi tạo
initAdminUser($pdo);

// Hàm ghi log cơ bản
function app_log(string $level, string $message): void {
    $date = date('Y-m-d H:i:s');
    $logLine = "[$date] $level: $message\n";
    if ($level === 'ERROR') {
        file_put_contents('php://stderr', $logLine);
    } else {
        file_put_contents('php://stderr', $logLine); // Changed to stderr to avoid breaking HTTP response
    }
}

// Hàm tạo slug tiếng Việt
function create_slug(string $string): string {
    $search = array(
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
        '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
        '#(ì|í|ị|ỉ|ĩ)#',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
        '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
        '#(ỳ|ý|ỵ|ỷ|ỹ)#',
        '#(đ)#',
        '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
        '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
        '#(Ì|Í|Ị|Ỉ|Ĩ)#',
        '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
        '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
        '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
        '#(Đ)#'
    );
    $replace = array('a', 'e', 'i', 'o', 'u', 'y', 'd', 'A', 'E', 'I', 'O', 'U', 'Y', 'D');
    $string = preg_replace($search, $replace, $string);
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]+/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

// Tạo CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

