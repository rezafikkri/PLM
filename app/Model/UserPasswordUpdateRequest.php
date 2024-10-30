<?php

namespace RezaFikkri\PLM\Model;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class UserPasswordUpdateRequest implements IteratorAggregate
{
    private ?int $id;
    private ?string $oldPassword;
    private ?string $newPassword;

    public function getIterator(): Traversable
    {
        return new ArrayIterator([
            'oldPassword' => $this->oldPassword,
            'newPassword' => $this->newPassword,
        ]);
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setOldPassword(?string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }
}
