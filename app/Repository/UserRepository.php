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

    public function update(User $user): bool
    {
        $sql = 'UPDATE users SET username = :username';
        $params = [':username' => $user->getUsername()];

        if (!empty($user->getPassword())) {
            $sql .= ', password = :password';
            $params[':password'] = $user->getPassword();
        }

        $sql .= ' WHERE id = :id';
        $params[':id'] = $user->getId();

        $stmt = $this->dbc->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->dbc->prepare('SELECT id, username, password FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute(); 

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User;
            $user->setId($row['id']);
            $user->setUsername($row['username']);
            $user->setPassword($row['password']);
            return $user;
        }
        return null;
    }


    public function deleteAll(): bool
    {
        return $this->dbc->exec('DELETE FROM users');
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->dbc->prepare('SELECT id, username, password FROM users WHERE username = :username');
        $stmt->bindValue(':username', $username);
        $stmt->execute(); 

        // kita tidak menggunakan closeCursor karena username didatabase adalah unique, jadi data
        // yang di ambil dapat dipastikan hanya 1 data
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User;
            $user->setId($row['id']);
            $user->setUsername($row['username']);
            $user->setPassword($row['password']);
            return $user;
        }
        return null;
    }
}
