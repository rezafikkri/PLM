<?php

namespace RezaFikkri\PLM\Model;

use RezaFikkri\PLM\Entity\User;

class UserPasswordUpdateResponse
{
    private User $user;

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
