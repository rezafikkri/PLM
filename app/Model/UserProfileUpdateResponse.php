<?php

namespace RezaFikkri\PLM\Model;

use RezaFikkri\PLM\Entity\User;

class UserProfileUpdateResponse
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
