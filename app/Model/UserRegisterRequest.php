<?php

namespace RezaFikkri\PLM\Model;

// Ini adalah model atau disebut juga DTO
class UserRegisterRequest
{
    private ?string $username = null;
    private ?string $password = null;

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
