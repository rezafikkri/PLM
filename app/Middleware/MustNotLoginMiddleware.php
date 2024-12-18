<?php

namespace RezaFikkri\PLM\Middleware;

// use RezaFikkri\PLM\Config\Database;
// use RezaFikkri\PLM\Repository\SessionRepository;
// use RezaFikkri\PLM\Repository\UserRepository;
use RezaFikkri\PLM\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
    public function __construct(
        private SessionService $sessionService,
    ) {
        // $sessionRepository = new SessionRepository(Database::getConnection());
        // $userRepository = new UserRepository(Database::getConnection());
        // $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function before(): void
    {
        $user = $this->sessionService->current();
        if (!is_null($user)) {
            redirect()->to('/');
        }
    }
}
