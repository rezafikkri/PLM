<?php

namespace RezaFikkri\PLM\Repository;

use PDO;
use RezaFikkri\PLM\Entity\User;

class UserRepository
{
    public function __construct(
        private PDO $dbc,
    ) {
        
    }

    public function save(User $user): User
    {
        $sql = <<<SQL
            INSERT INTO users (username, password) VALUES(:username, :password)
            RETURNING id
        SQL;
        $stmt = $this->dbc->prepare($sql);
        $stmt->execute([
            ':username' => $user->getUsername(),
            ':password' => $user->getPassword(),
        ]);
        $id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        $user->setId($id);
        return $user;
    }
}
