<?php
declare(strict_types=1);
require dirname(__DIR__).'/src/Domain/ConsultationValidator.php';

use App\Domain\ConsultationValidator;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

$root = dirname(__DIR__);
$env = [];
if (is_file($root.'/.env')) {
    $parsed = parse_ini_file($root.'/.env', false, INI_SCANNER_RAW);
    if (is_array($parsed)) $env = $parsed;
}
$dbHost = (string)($env['DB_HOST'] ?? '127.0.0.1');
$dbPort = (string)($env['DB_PORT'] ?? '3306');
$dbName = (string)($env['DB_DATABASE'] ?? 'interior_design');
$dbUser = (string)($env['DB_USERNAME'] ?? 'root');
$dbPass = (string)($env['DB_PASSWORD'] ?? '');
$basePath = rtrim(str_replace('\\\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($basePath === '.' || $basePath === '/') $basePath = '';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($basePath !== '' && str_starts_with($path, $basePath)) $path = substr($path, strlen($basePath)) ?: '/';
$connect = static function () use ($dbHost, $dbPort, $dbName, $dbUser, $dbPass): PDO {
    return new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
};

if ($path === '/health') {
    header('Content-Type: application/json');
    try {
        $pdo = new PDO(
            'mysql:host='.($dbHost).';port='.($dbPort).';dbname='.($dbName).';charset=utf8mb4',
            $dbUser,
            $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $pdo->query('SELECT 1');
        
        echo json_encode(['status' => 'ok', 'database' => 'connected']);
    } catch (Throwable $e) {
        error_log('Health check failed: '.$e->getMessage());
        http_response_code(503);
        echo json_encode(['status' => 'degraded', 'database' => 'unavailable']);
    }
    exit;
}

if ($path === '/consultation' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $errors = (new ConsultationValidator())->validate($input);
    if ($errors) {
        
        http_response_code(422);
        echo json_encode(['ok' => false, 'errors' => $errors]);
        exit;
    }
    try {
        $pdo = new PDO(
            'mysql:host='.($_ENV['DB_HOST'] ?? '127.0.0.1').';port='.($_ENV['DB_PORT'] ?? '3306').';dbname='.($_ENV['DB_DATABASE'] ?? 'interior_design').';charset=utf8mb4',
            $_ENV['DB_USERNAME'] ?? 'root',
            $_ENV['DB_PASSWORD'] ?? '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $stmt = $pdo->prepare('INSERT INTO consultation_requests(customer_name,email,phone,message) VALUES(?,?,?,?)');
        $stmt->execute([trim($input['customer_name']), trim($input['email']), trim($input['phone'] ?? ''), trim($input['message'] ?? '')]);
        
        echo json_encode(['ok' => true, 'message' => 'Đã nhận yêu cầu tư vấn.']);
    } catch (Throwable $e) {
        error_log('Consultation persistence failed: '.$e->getMessage());
        http_response_code(503);
        echo json_encode(['ok' => false, 'message' => 'Dịch vụ tạm thời không khả dụng.']);
    }
    exit;
}
?><!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Interior Studio</title><link rel="stylesheet" href="<?= htmlspecialchars($basePath, ENT_QUOTES) ?>/assets/style.css"></head><body><header><b>INTERIOR STUDIO</b><nav><a href="#projects">Dự án</a> <a href="#contact">Tư vấn</a></nav></header><main><section class="hero"><small>THIẾT KẾ KHÔNG GIAN SỐNG</small><h1>Không gian đẹp,<br>đúng chất riêng.</h1><p>Giải pháp nội thất tinh tế cho ngôi nhà của bạn.</p><a class="button" href="#contact">Nhận tư vấn</a></section><section id="projects"><h2>Dự án tiêu biểu</h2><div class="cards"><article><div class="sample"></div><h3>Căn hộ hiện đại</h3><p>Tối giản · Ấm áp</p></article><article><div class="sample second"></div><h3>Nhà phố đương đại</h3><p>Tự nhiên · Thoáng sáng</p></article><article><div class="sample third"></div><h3>Scandinavian</h3><p>Thanh lịch · Tiện nghi</p></article></div></section><section id="contact"><h2>Chia sẻ ý tưởng của bạn</h2><form id="consultation"><label>Họ tên<input name="customer_name" required maxlength="120"></label><label>Email<input name="email" type="email" required></label><label>Điện thoại<input name="phone"></label><label>Nhu cầu<textarea name="message"></textarea></label><button class="button">Gửi yêu cầu</button><p id="result"></p></form></section></main><footer>© Interior Studio · SDOM</footer><script>window.INTERIOR_BASE_PATH = <?= json_encode($basePath, JSON_UNESCAPED_SLASHES) ?>;</script>
<script src="<?= htmlspecialchars($basePath, ENT_QUOTES) ?>/assets/app.js"></script></body></html>
