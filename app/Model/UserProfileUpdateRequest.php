<?php

namespace RezaFikkri\PLM\Model;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class UserProfileUpdateRequest implements IteratorAggregate
{
    private ?int $id;
    private ?string $username;
    private ?string $password;

    public function getIterator(): Traversable
    {
        return new ArrayIterator([
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
        ]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
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
