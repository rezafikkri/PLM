<?php

namespace RezaFikkri\PLM\Controller;

use RezaFikkri\PLM\App\View;
// use RezaFikkri\PLM\Config\Database;
// use RezaFikkri\PLM\Repository\SessionRepository;
// use RezaFikkri\PLM\Repository\UserRepository;
use RezaFikkri\PLM\Service\SessionService;

class HomeController
{
    public function __construct(
        private SessionService $sessionService,
    ) {
        // $dbc = Database::getConnection();
        // $userRepository = new UserRepository($dbc);
        // $sessionRepository = new SessionRepository($dbc);
        // $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function index(): void
    {
        
        if (!is_null($user = $this->sessionService->current())) {
            $response = [
                'title' => 'Dashboard - PHP Login Management System',
                'username' => $user->getUsername(),
            ];
            View::render('Home/dashboard', $response);
        } else {
            // model response
            $response = [
                'title' => 'PHP Login Management System',
            ];
            View::render('Home/index', $response);
        }
    }
}
