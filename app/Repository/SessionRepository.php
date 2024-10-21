<?php

namespace RezaFikkri\PLM\Repository;

use PDO;
use RezaFikkri\PLM\Entity\Session;

class SessionRepository
{
    public function __construct(
        private PDO $dbc,
    ) {
        
    }

    public function save(Session $session): Session
    {
        $stmt = $this->dbc->prepare('INSERT INTO sessions (id, user_id) VALUES (:id, :user_id)');
        $stmt->execute([':id' => $session->getId(), ':user_id' => $session->getUserId()]);
        return $session;
    }

    public function findById(string $id): ?Session
    {
        $stmt = $this->dbc->prepare('SELECT id, user_id FROM sessions WHERE id = :id');
        $stmt->execute([':id' => $id]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $session = new Session;
            $session->setId($row['id']);
            $session->setUserId($row['user_id']);
            return $session;
        }
        return null;
    }

    public function deleteById(string $id): bool
    {
        $stmt = $this->dbc->prepare('DELETE FROM sessions WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public function deleteAll(): bool
    {
        return $this->dbc->exec('DELETE FROM sessions');
    }
}
