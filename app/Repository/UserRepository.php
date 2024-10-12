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

    public function findById(int $id): ?User
    {
        $stmt = $this->dbc->prepare('SELECT id, username, password FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute(); 

        try {
            if ($userDb = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user = new User;
                $user->setId($userDb['id']);
                $user->setUsername($userDb['username']);
                $user->setPassword($userDb['password']);
                return $user;
            }
            return null;
        } finally {
            $stmt->closeCursor();
        }
    }


    public function deleteAll(): bool
    {
        return $this->dbc->exec('DELETE FROM users');
    }
}
