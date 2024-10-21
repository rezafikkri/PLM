<?php

namespace RezaFikkri\PLM\Service;

use RezaFikkri\PLM\Entity\Session;
use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Repository\SessionRepository;
use RezaFikkri\PLM\Repository\UserRepository;

// Session service dibuat mengikuti format sessionnya. ex. create session, delete session, dsb.
class SessionService
{
    public function __construct(
        private SessionRepository $sessionRepository,
        private UserRepository $userRepository,
    ) {

    }

    public function create(): Session
    {
        $session = new Session;
        $session->setId(uniqid());
        $session->setUserId(12);

        $this->sessionRepository->save($session);
        setcookie($_ENV['SESSION_NAME'], $session->getId(), time() + (3600 * 24 * 2), '/');

        return $session;
    }

    public function destroy(): bool
    {
        $sessionId = $_COOKIE[$_ENV['SESSION_NAME']] ?? '';
        if ($this->sessionRepository->deleteById($sessionId)) {
            // delete session cookie
            setcookie($_ENV['SESSION_NAME'], expires_or_options: 1, path: '/');
        }
        return false;
    }

    public function current(): ?User
    {
        $sessionId = $_COOKIE[$_ENV['SESSION_NAME']] ?? '';
        $session = $this->sessionRepository->findById($sessionId);
        if ($session) {
            return $this->userRepository->findById($session->getUserId());
        }
        return null;
    }
}
