<?php

namespace RezaFikkri\PLM\Model;

use RezaFikkri\PLM\Entity\User;

class UserRegisterResponse
{
    // Isi dari model response bisa data apapun, tergantung nanti ketika
    // setelah register kita mau melakukan apa?
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
