<?php

namespace RezaFikkri\PLM\Model;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class UserLoginRequest implements IteratorAggregate
{
    private ?string $username = null;
    private ?string $password = null;

    public function getIterator(): Traversable
    {
        return new ArrayIterator([
            'username' => $this->username,
            'password' => $this->password,
        ]);
    }

    // Getter and Setter for $username
    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(?string $username): void {
        $this->username = $username;
    }

    // Getter and Setter for $password
    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(?string $password): void {
        $this->password = $password;
    }
}
