<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ConsultationRepository
{
    public function __construct(private PDO $pdo) {}

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO consultation_requests
             (customer_name,email,phone,room_type,budget,message)
             VALUES (:customer_name,:email,:phone,:room_type,:budget,:message)'
        );
        $stmt->execute([
            'customer_name' => trim($data['customer_name']),
            'email' => trim($data['email']),
            'phone' => trim($data['phone']),
            'room_type' => trim($data['room_type']),
            'budget' => ($data['budget'] ?? '') !== '' ? $data['budget'] : null,
            'message' => trim((string)($data['message'] ?? '')),
        ]);
        return (int)$this->pdo->lastInsertId();
    }
}
