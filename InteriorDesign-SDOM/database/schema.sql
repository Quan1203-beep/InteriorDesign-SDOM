CREATE DATABASE IF NOT EXISTS interior_design
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE interior_design;

CREATE TABLE IF NOT EXISTS design_projects (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(160) NOT NULL,
  slug VARCHAR(180) NOT NULL UNIQUE,
  style VARCHAR(80) NOT NULL,
  room_type VARCHAR(80) NOT NULL,
  description TEXT NOT NULL,
  cover_image VARCHAR(500) NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_projects_style_published (style, is_published)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS consultation_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  room_type VARCHAR(80) NOT NULL,
  budget DECIMAL(12,2) NULL,
  message TEXT NULL,
  status ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_requests_status_created (status, created_at)
) ENGINE=InnoDB;

INSERT INTO design_projects
(title, slug, style, room_type, description, cover_image, is_published)
VALUES
('Căn hộ tối giản ấm áp', 'can-ho-toi-gian-am-ap', 'Minimalist', 'Apartment',
 'Không gian căn hộ tối giản, ưu tiên ánh sáng tự nhiên và vật liệu gỗ.',
 NULL, 1),
('Phòng khách hiện đại', 'phong-khach-hien-dai', 'Modern', 'Living room',
 'Phòng khách hiện đại với bảng màu trung tính và đường nét gọn gàng.',
 NULL, 1)
ON DUPLICATE KEY UPDATE title=VALUES(title);
