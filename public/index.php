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
$route = trim((string)($_GET['route'] ?? ''), '/');
$path = $route !== '' ? '/'.$route : (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
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


if ($path === '/order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'Dữ liệu đơn hàng không hợp lệ.']);
        exit;
    }

    $name = trim((string)($input['name'] ?? ''));
    $phone = trim((string)($input['phone'] ?? ''));
    $address = trim((string)($input['address'] ?? ''));
    $note = trim((string)($input['note'] ?? ''));
    $items = $input['items'] ?? [];
    if ($name === '' || strlen($name) > 120 || $phone === '' || strlen($phone) > 30 || $address === '' || strlen($address) > 500 || !is_array($items) || count($items) < 1 || count($items) > 20) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'Vui lòng kiểm tra họ tên, số điện thoại, địa chỉ và giỏ hàng.']);
        exit;
    }

    // Giá và tên sản phẩm được xác định phía máy chủ, không lấy giá do trình duyệt gửi lên.
    $catalog = [
        1 => ['Sofa Lino 3 chỗ', 12900000], 2 => ['Sofa góc Noma', 18900000],
        3 => ['Ghế thư giãn Arco', 4950000], 4 => ['Ghế ăn Elio', 1850000],
        5 => ['Ghế lounge Miro', 6200000], 6 => ['Bàn trà Orbit', 3250000],
        7 => ['Bàn ăn Sora 6 người', 8900000], 8 => ['Bàn làm việc Kiyo', 4200000],
        9 => ['Bàn console Haven', 3750000], 10 => ['Kệ sách Forma', 5600000],
        11 => ['Tủ đầu giường Nami', 2150000], 12 => ['Tủ sideboard Luma', 7800000],
        13 => ['Đèn bàn Luna', 1250000], 14 => ['Đèn thả Mellow', 2850000],
        15 => ['Đèn sàn Atelier', 3450000], 16 => ['Gương vòm Sol', 1650000],
        17 => ['Thảm dệt Terra', 2250000], 18 => ['Bình gốm Nara', 590000],
        19 => ['Tranh canvas Dune', 980000], 20 => ['Đôn gỗ Pebble', 1150000],
    ];
    $lines = [];
    $total = 0;
    foreach ($items as $item) {
        $id = filter_var($item['id'] ?? null, FILTER_VALIDATE_INT);
        $qty = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || !isset($catalog[$id]) || !$qty || $qty < 1 || $qty > 99) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => 'Có sản phẩm hoặc số lượng không hợp lệ trong giỏ hàng.']);
            exit;
        }
        [$productName, $unitPrice] = $catalog[$id];
        $lineTotal = $unitPrice * $qty;
        $total += $lineTotal;
        $lines[] = ['name' => $productName, 'quantity' => $qty, 'unit_price' => $unitPrice, 'line_total' => $lineTotal];
    }

    $smtpHost = trim((string)($env['SMTP_HOST'] ?? 'smtp.gmail.com'));
    $smtpPort = (int)($env['SMTP_PORT'] ?? 587);
    $smtpUser = trim((string)($env['SMTP_USER'] ?? ''));
    $smtpPass = preg_replace('/\s+/', '', (string)($env['SMTP_PASS'] ?? ''));
    $mailTo = trim((string)($env['MAIL_TO'] ?? 'hongquan12032005@gmail.com'));
    $fromName = (string)($env['MAIL_FROM_NAME'] ?? 'HONGQUANN');
    if ($smtpUser === '' || $smtpPass === '' || !filter_var($mailTo, FILTER_VALIDATE_EMAIL) || $smtpPort !== 587) {
        error_log('Order email is not configured: missing SMTP settings.');
        http_response_code(503);
        echo json_encode(['ok' => false, 'message' => 'Cửa hàng chưa cấu hình email nhận đơn. Vui lòng thử lại sau.']);
        exit;
    }

    $orderCode = 'HQ-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    $money = static fn(int $amount): string => number_format($amount, 0, ',', '.') . ' ₫';
    $escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $rows = '';
    foreach ($lines as $line) {
        $rows .= '<tr><td style="padding:10px;border-bottom:1px solid #eee;">'.$escape($line['name']).'</td><td style="padding:10px;text-align:center;border-bottom:1px solid #eee;">'.$line['quantity'].'</td><td style="padding:10px;text-align:right;border-bottom:1px solid #eee;">'.$money($line['unit_price']).'</td><td style="padding:10px;text-align:right;border-bottom:1px solid #eee;">'.$money($line['line_total']).'</td></tr>';
    }
    $html = '<!doctype html><html lang="vi"><meta charset="utf-8"><body style="font-family:Arial,sans-serif;color:#292929"><div style="max-width:760px;margin:auto;padding:24px"><h1 style="letter-spacing:3px">HONGQUANN</h1><h2>HÓA ĐƠN ĐẶT HÀNG</h2><p><b>Mã đơn:</b> '.$escape($orderCode).'<br><b>Thời gian:</b> '.date('d/m/Y H:i').'</p><h3>Thông tin khách hàng</h3><p><b>Họ tên:</b> '.$escape($name).'<br><b>Điện thoại:</b> '.$escape($phone).'<br><b>Địa chỉ:</b> '.$escape($address).'<br><b>Ghi chú:</b> '.$escape($note !== '' ? $note : 'Không có').'</p><h3>Chi tiết sản phẩm</h3><table style="width:100%;border-collapse:collapse"><thead><tr style="background:#f4f1ed"><th style="padding:10px;text-align:left">Sản phẩm</th><th>Số lượng</th><th style="text-align:right">Đơn giá</th><th style="text-align:right">Thành tiền</th></tr></thead><tbody>'.$rows.'</tbody></table><h2 style="text-align:right">Tổng cộng: '.$money($total).'</h2><p>Cảm ơn quý khách đã chọn HONGQUANN.</p></div></body></html>';
    $plain = "HONGQUANN - HOA DON DAT HANG\nMa don: $orderCode\nKhach hang: $name\nDien thoai: $phone\nDia chi: $address\nGhi chu: $note\n\n";
    foreach ($lines as $line) $plain .= $line['name'].' | SL '.$line['quantity'].' | '.$money($line['unit_price']).' | '.$money($line['line_total'])."\n";
    $plain .= "\nTONG CONG: ".$money($total)."\n";

    $socket = null;
    try {
        $socket = stream_socket_client("tcp://{$smtpHost}:{$smtpPort}", $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
        if (!$socket) throw new RuntimeException("SMTP connect failed: $errstr ($errno)");
        stream_set_timeout($socket, 20);
        $readReply = static function () use ($socket): string {
            $response = '';
            while (($line = fgets($socket, 515)) !== false) {
                $response .= $line;
                if (strlen($line) < 4 || $line[3] === ' ') break;
            }
            return $response;
        };
        $expect = static function (array $codes) use ($readReply): string {
            $reply = $readReply();
            $code = (int)substr($reply, 0, 3);
            if (!in_array($code, $codes, true)) throw new RuntimeException('SMTP server rejected command: '.trim($reply));
            return $reply;
        };
        $command = static function (string $line, array $codes) use ($socket, $expect): string {
            if (fwrite($socket, $line."\r\n") === false) throw new RuntimeException('SMTP write failed.');
            return $expect($codes);
        };

        $expect([220]);
        $hostName = preg_replace('/[^a-zA-Z0-9.-]/', '', (string)($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
        $command('EHLO '.$hostName, [250]);
        $command('STARTTLS', [220]);
        if (stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT) !== true) throw new RuntimeException('SMTP TLS negotiation failed.');
        $command('EHLO '.$hostName, [250]);
        $command('AUTH LOGIN', [334]);
        $command(base64_encode($smtpUser), [334]);
        $command(base64_encode($smtpPass), [235]);
        $command('MAIL FROM:<'.$smtpUser.'>', [250]);
        $command('RCPT TO:<'.$mailTo.'>', [250, 251]);
        $command('DATA', [354]);

        $subject = '=?UTF-8?B?'.base64_encode('HONGQUANN - Hóa đơn '.$orderCode).'?=';
        $boundary = 'bnd_'.bin2hex(random_bytes(12));
        $headers = 'From: =?UTF-8?B?'.base64_encode($fromName).'?= <'.$smtpUser.">\r\n";
        $headers .= 'To: <'.$mailTo.">\r\nSubject: ".$subject."\r\n";
        $headers .= 'MIME-Version: 1.0'."\r\nContent-Type: multipart/alternative; boundary=\"".$boundary."\"\r\n";
        $body = '--'.$boundary."\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n".chunk_split(base64_encode($plain));
        $body .= '--'.$boundary."\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n".chunk_split(base64_encode($html));
        $body .= '--'.$boundary."--\r\n";
        $message = $headers."\r\n".$body;
        $message = preg_replace('/(?m)^\./', '..', str_replace(["\r\n", "\r", "\n"], "\r\n", $message));
        if (fwrite($socket, $message."\r\n.\r\n") === false) throw new RuntimeException('SMTP message write failed.');
        $expect([250]);
        $command('QUIT', [221]);
        fclose($socket);
        $socket = null;
        echo json_encode(['ok' => true, 'message' => 'Đã gửi đơn hàng và hóa đơn đến cửa hàng.', 'order_code' => $orderCode]);
    } catch (Throwable $e) {
        if (is_resource($socket)) fclose($socket);
        error_log('Order email failed: '.$e->getMessage());
        http_response_code(502);
        echo json_encode(['ok' => false, 'message' => 'Chưa gửi được hóa đơn. Vui lòng thử lại hoặc liên hệ cửa hàng qua số 0799 130 600.']);
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
?><!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>HongQuann | Nội thất & không gian sống</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="<?= htmlspecialchars($basePath, ENT_QUOTES) ?>/assets/style.css?v=20261112-4"></head><body>
<header class="top"><a class="logo" href="#">HONGQUANN <small>FURNITURE & LIVING</small></a><nav><a href="#products">Sản phẩm</a><a href="#story">Về chúng tôi</a><a href="#contact">Liên hệ</a></nav><button id="open-cart">Giỏ hàng <b id="cart-count">0</b></button></header>
<main><section class="hero"><div><small>THE HONGQUANN COLLECTION · 2026</small><h1>Không gian đẹp,<br><i>bắt đầu từ bạn.</i></h1><p>Nội thất tinh tế, tiện nghi và bền bỉ cho từng góc nhà.</p><a class="btn" href="#products">Khám phá bộ sưu tập ↗</a><p class="hero-count"><b>20+</b> món nội thất được tuyển chọn</p></div><img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1400&q=85" alt="Phòng khách hiện đại"></section>
<section class="benefits"><span>01 · Thiết kế tinh tế</span><span>02 · Chất liệu chọn lọc</span><span>03 · Giao hàng tận nơi</span><span>04 · Hỗ trợ tận tâm</span></section>
<section class="catalog" id="products"><div class="heading"><div><small>THE HONGQUANN COLLECTION</small><h2>Sản phẩm nổi bật</h2><p>Chọn món đồ phù hợp để hoàn thiện không gian của bạn.</p></div><span id="product-total">20 sản phẩm</span></div><div class="tools"><input id="product-search" placeholder="Tìm kiếm sản phẩm..."><div id="filters"><button class="active" data-cat="all">Tất cả</button><button data-cat="sofa">Sofa</button><button data-cat="chair">Ghế</button><button data-cat="table">Bàn</button><button data-cat="storage">Tủ & kệ</button><button data-cat="lighting">Đèn</button><button data-cat="decor">Trang trí</button></div></div><div class="grid" id="products-grid"></div><p id="empty" hidden>Không tìm thấy sản phẩm phù hợp.</p></section>
<section class="story" id="story"><img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85" alt="Không gian sống"><div><small>ABOUT HONGQUANN</small><h2>Ít hơn, nhưng<br><i>đúng chất hơn.</i></h2><p>Chúng tôi tin rằng nội thất tốt không chỉ đẹp trong ảnh. Đó là cảm giác thoải mái mỗi ngày, chất liệu đồng hành lâu dài và thiết kế khiến bạn muốn trở về nhà.</p></div></section>
<section class="contact" id="contact"><div><small>LET'S TALK</small><h2>Cùng tạo nên<br>không gian của bạn.</h2><p>Để lại thông tin, đội ngũ sẽ liên hệ tư vấn.</p><div class="owner-contact"><strong>Thông tin liên hệ chủ cửa hàng</strong><a href="tel:0799130600">0799 130 600</a><a href="mailto:hongquan12032005@gmail.com">hongquan12032005@gmail.com</a></div></div><form id="consultation"><label>Họ tên<input name="customer_name" required maxlength="120"></label><label>Email<input name="email" type="email" required></label><label>Điện thoại<input name="phone"></label><label>Nhu cầu<textarea name="message"></textarea></label><button class="btn">Gửi yêu cầu ↗</button><p id="result"></p></form></section></main>
<footer>HONGQUANN · FURNITURE & LIVING <span>Chủ cửa hàng: <a href="tel:0799130600">0799 130 600</a> · © 2026 InteriorDesign-SDOM</span></footer>
<div class="shade" id="shade"></div><aside id="cart"><div class="cart-head"><h2>Giỏ hàng <small id="cart-n">(0)</small></h2><button id="close-cart">×</button></div><div id="cart-lines"></div><div id="cart-empty">Giỏ hàng đang trống.<br>Hãy chọn món đồ bạn yêu thích.</div><div id="cart-foot"><div class="sum"><span>Tạm tính</span><b id="subtotal">0 ₫</b></div><div class="sum total"><span>Tổng tiền</span><b id="total">0 ₫</b></div><button class="btn" id="checkout">Tiến hành đặt hàng →</button><p>Phí vận chuyển sẽ được xác nhận khi đặt hàng.</p></div></aside>
<div class="modal" id="detail-modal"><div class="modal-box"><button class="x" id="detail-close">×</button><div id="detail-body"></div></div></div>
<div class="modal" id="order-modal"><div class="modal-box order-box"><button class="x" id="order-close">×</button><small>FINAL STEP</small><h2>Thông tin đặt hàng</h2><form id="order-form"><label>Họ tên<input name="name" required></label><label>Số điện thoại<input name="phone" required></label><label>Địa chỉ nhận hàng<textarea name="address" required></textarea></label><label>Ghi chú<textarea name="note"></textarea></label><p class="sum total">Tổng thanh toán <b id="order-total">0 ₫</b></p><button class="btn">Gửi đơn hàng & nhận hóa đơn qua email</button><p id="order-result" role="status"></p><small>Đơn hàng sẽ được gửi đến cửa hàng để xác nhận. Chưa tích hợp thanh toán trực tuyến.</small></form></div></div>
<script>window.INTERIOR_BASE_PATH = <?= json_encode($basePath, JSON_UNESCAPED_SLASHES) ?>;</script><script src="<?= htmlspecialchars($basePath, ENT_QUOTES) ?>/assets/app.js?v=20261112-5" defer></script></body></html>