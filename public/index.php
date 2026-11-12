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
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
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
        $pdo = $connect();
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
?><!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Maison | Nội thất & không gian sống</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="<?= htmlspecialchars($basePath, ENT_QUOTES) ?>/assets/style.css"></head><body>
<header class="top"><a class="logo" href="#">MAISON <small>FURNITURE & LIVING</small></a><nav><a href="#products">Sản phẩm</a><a href="#story">Về chúng tôi</a><a href="#contact">Liên hệ</a></nav><button id="open-cart">Giỏ hàng <b id="cart-count">0</b></button></header>
<main><section class="hero"><div><small>THE MAISON COLLECTION · 2026</small><h1>Không gian đẹp,<br><i>bắt đầu từ bạn.</i></h1><p>Nội thất tinh tế, tiện nghi và bền bỉ cho từng góc nhà.</p><a class="btn" href="#products">Khám phá bộ sưu tập ↗</a><p class="hero-count"><b>20+</b> món nội thất được tuyển chọn</p></div><img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1400&q=85" alt="Phòng khách hiện đại"></section>
<section class="benefits"><span>01 · Thiết kế tinh tế</span><span>02 · Chất liệu chọn lọc</span><span>03 · Giao hàng tận nơi</span><span>04 · Hỗ trợ tận tâm</span></section>
<section class="catalog" id="products"><div class="heading"><div><small>THE MAISON COLLECTION</small><h2>Sản phẩm nổi bật</h2><p>Chọn món đồ phù hợp để hoàn thiện không gian của bạn.</p></div><span id="product-total">20 sản phẩm</span></div><div class="tools"><input id="product-search" placeholder="Tìm kiếm sản phẩm..."><div id="filters"><button class="active" data-cat="all">Tất cả</button><button data-cat="sofa">Sofa</button><button data-cat="chair">Ghế</button><button data-cat="table">Bàn</button><button data-cat="storage">Tủ & kệ</button><button data-cat="lighting">Đèn</button><button data-cat="decor">Trang trí</button></div></div><div class="grid" id="products-grid"></div><p id="empty" hidden>Không tìm thấy sản phẩm phù hợp.</p></section>
<section class="story" id="story"><img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85" alt="Không gian sống"><div><small>ABOUT MAISON</small><h2>Ít hơn, nhưng<br><i>đúng chất hơn.</i></h2><p>Chúng tôi tin rằng nội thất tốt không chỉ đẹp trong ảnh. Đó là cảm giác thoải mái mỗi ngày, chất liệu đồng hành lâu dài và thiết kế khiến bạn muốn trở về nhà.</p></div></section>
<section class="contact" id="contact"><div><small>LET'S TALK</small><h2>Cùng tạo nên<br>không gian của bạn.</h2><p>Để lại thông tin, đội ngũ sẽ liên hệ tư vấn.</p></div><form id="consultation"><label>Họ tên<input name="customer_name" required maxlength="120"></label><label>Email<input name="email" type="email" required></label><label>Điện thoại<input name="phone"></label><label>Nhu cầu<textarea name="message"></textarea></label><button class="btn">Gửi yêu cầu ↗</button><p id="result"></p></form></section></main>
<footer>MAISON · FURNITURE & LIVING <span>© 2026 InteriorDesign-SDOM</span></footer>
<div class="shade" id="shade"></div><aside id="cart"><div class="cart-head"><h2>Giỏ hàng <small id="cart-n">(0)</small></h2><button id="close-cart">×</button></div><div id="cart-lines"></div><div id="cart-empty">Giỏ hàng đang trống.<br>Hãy chọn món đồ bạn yêu thích.</div><div id="cart-foot"><div class="sum"><span>Tạm tính</span><b id="subtotal">0 ₫</b></div><div class="sum total"><span>Tổng tiền</span><b id="total">0 ₫</b></div><button class="btn" id="checkout">Tiến hành đặt hàng →</button><p>Phí vận chuyển sẽ được xác nhận khi đặt hàng.</p></div></aside>
<div class="modal" id="detail-modal"><div class="modal-box"><button class="x" id="detail-close">×</button><div id="detail-body"></div></div></div>
<div class="modal" id="order-modal"><div class="modal-box order-box"><button class="x" id="order-close">×</button><small>FINAL STEP</small><h2>Thông tin đặt hàng</h2><form id="order-form"><label>Họ tên<input name="name" required></label><label>Số điện thoại<input name="phone" required></label><label>Địa chỉ nhận hàng<textarea name="address" required></textarea></label><label>Ghi chú<textarea name="note"></textarea></label><p class="sum total">Tổng thanh toán <b id="order-total">0 ₫</b></p><button class="btn">Xác nhận đơn hàng</button><small>Đặt hàng mô phỏng, chưa tích hợp thanh toán trực tuyến.</small></form></div></div>
<script>window.INTERIOR_BASE_PATH = <?= json_encode($basePath, JSON_UNESCAPED_SLASHES) ?>;</script><script src="<?= htmlspecialchars($basePath, ENT_QUOTES) ?>/assets/app.js"></script></body></html>