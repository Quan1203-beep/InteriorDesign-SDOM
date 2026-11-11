<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ProjectRepository
{
    public function __construct(private PDO $pdo) {}

    public function published(?string $style = null): array
    {
        if ($style !== null && $style !== '') {
            $stmt = $this->pdo->prepare(
                'SELECT id,title,slug,style,room_type,description,cover_image
                 FROM design_projects WHERE is_published=1 AND style=:style
                 ORDER BY created_at DESC'
            );
            $stmt->execute(['style' => $style]);
            return $stmt->fetchAll();
        }

        return $this->pdo->query(
            'SELECT id,title,slug,style,room_type,description,cover_image
             FROM design_projects WHERE is_published=1 ORDER BY created_at DESC'
        )->fetchAll();
    }
}
