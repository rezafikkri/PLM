<?php

namespace RezaFikkri\PLM\Model;

class UserProfileUpdateRequest
{
    private ?int $id = null;
    private ?string $username = null;

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
}
