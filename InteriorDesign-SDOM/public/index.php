<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Config\Database;
use App\Domain\ConsultationValidator;
use App\Repository\ConsultationRepository;
use App\Repository\ProjectRepository;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$base = dirname(__DIR__);
Dotenv::createImmutable($base)->safeLoad();

$logger = new Logger('interior-design');
$logger->pushHandler(new StreamHandler($base . '/logs/app.log', Logger::toMonologLevel($_ENV['APP_LOG_LEVEL'] ?? 'info')));

$config = [
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'database' => $_ENV['DB_DATABASE'] ?? 'interior_design',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
];

try {
    $pdo = Database::connect($config);
    $projects = new ProjectRepository($pdo);
    $consultations = new ConsultationRepository($pdo);
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

    if ($path === '/health') {
        $pdo->query('SELECT 1');
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'database' => 'connected']);
        exit;
    }

    if ($path === '/consultation' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = ConsultationValidator::validate($_POST);
        if ($errors) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['errors' => $errors], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $id = $consultations->create($_POST);
        $logger->info('Consultation request created', ['request_id' => $id]);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['message' => 'Đã nhận yêu cầu tư vấn.', 'id' => $id], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $style = isset($_GET['style']) ? trim((string)$_GET['style']) : null;
    $items = $projects->published($style);
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!doctype html>
    <html lang="vi">
    <head>
      <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
      <title>Interior Studio | Thiết kế nội thất</title>
      <link rel="stylesheet" href="/assets/style.css">
    </head>
    <body>
      <header class="hero">
        <nav><a class="brand" href="/">INTERIOR STUDIO</a><a href="#projects">Dự án</a><a href="#consult">Tư vấn</a></nav>
        <div class="hero-copy"><p class="eyebrow">DESIGN YOUR WAY OF LIVING</p>
          <h1>Không gian đẹp<br> bắt đầu từ ý tưởng.</h1>
          <p>Thiết kế nội thất tinh tế, tiện nghi và phù hợp với phong cách sống của bạn.</p>
          <a class="button" href="#consult">Nhận tư vấn</a>
        </div>
      </header>
      <main>
        <section id="projects"><p class="eyebrow">SELECTED WORKS</p><h2>Dự án nổi bật</h2>
          <div class="grid">
          <?php foreach ($items as $item): ?>
            <article class="project">
              <div class="placeholder"></div>
              <p class="eyebrow"><?= htmlspecialchars($item['style']) ?> · <?= htmlspecialchars($item['room_type']) ?></p>
              <h3><?= htmlspecialchars($item['title']) ?></h3>
              <p><?= htmlspecialchars($item['description']) ?></p>
            </article>
          <?php endforeach; ?>
          </div>
        </section>
        <section id="consult" class="consult"><p class="eyebrow">LET'S TALK</p><h2>Đăng ký tư vấn thiết kế</h2>
          <form id="consult-form">
            <input name="customer_name" placeholder="Họ và tên" required maxlength="120">
            <input name="email" type="email" placeholder="Email" required>
            <input name="phone" placeholder="Số điện thoại" required>
            <select name="room_type" required><option value="">Loại không gian</option>
              <option>Căn hộ</option><option>Nhà phố</option><option>Phòng khách</option><option>Phòng ngủ</option>
            </select>
            <textarea name="message" placeholder="Mô tả nhu cầu của bạn"></textarea>
            <button class="button" type="submit">Gửi yêu cầu</button>
            <p id="form-message" role="status"></p>
          </form>
        </section>
      </main>
      <footer>© <?= date('Y') ?> Interior Studio</footer>
      <script src="/assets/app.js"></script>
    </body></html>
    <?php
} catch (Throwable $e) {
    $logger->error('Application request failed', ['exception' => $e::class]);
    http_response_code(503);
    echo 'Hệ thống đang tạm thời không khả dụng. Vui lòng thử lại sau.';
}
