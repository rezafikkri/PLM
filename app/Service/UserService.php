<?php

namespace RezaFikkri\PLM\Service;

use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\{UserRegisterRequest, UserRegisterResponse};
use RezaFikkri\PLM\Repository\UserRepository;
use RezaFikkri\PLM\Validator\IsUnique;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Validation;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
        
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->ValidateUserRegistrationRequest($request);

        $user = new User;
        $user->setUsername($request->getUsername());
        $user->setPassword(password_hash($request->getPassword(), PASSWORD_BCRYPT));
        $this->userRepository->save($user);

        $response = new UserRegisterResponse;
        $response->setUser($user);
        return $response;
    }

    private function ValidateUserRegistrationRequest(UserRegisterRequest $request): void
    {
        // validate username
        $validator = Validation::createValidator();
        $violations = []; 
        $userViolations = $validator->validate($request->getUsername(), new Sequentially([
            new NotBlank([
                'message' => 'Username should not be blank.'
            ]),
            new Length([
                'min' => 4,
                'minMessage' => 'Username is too short. It should have {{ limit }} characters or more.',
            ]),
            new IsUnique('users', 'username'),
        ]));
        if (count($userViolations) > 0) {
            $violations[] = $userViolations[0]->getMessage();
        }

        // validate password
        $passwordViolations = $validator->validate($request->getPassword(), new Sequentially([
            new NotBlank([
                'message' => 'Password should not be blank.',
            ]),
            new PasswordStrength(),
        ]));
        if (count($passwordViolations) > 0) {
            $violations[] = $passwordViolations[0]->getMessage();
        }

        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }
    }
}
