<?php

namespace RezaFikkri\PLM\Service;

use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\{UserRegisterRequest, UserRegisterResponse};
use RezaFikkri\PLM\Repository\UserRepository;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
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
        $userViolations = $validator->validate($request->getUsername(), [
            new NotBlank([
                'message' => 'Username should not be blank.'
            ]),
            new Length([
                'min' => 4,
                'minMessage' => 'Username is too short. It should have {{ limit }} characters or more.',
            ]),
        ]);
        if (count($userViolations) > 0) {
            throw new ValidationException($userViolations[0]->getMessage());
        }

        $user = $this->userRepository->findByUsername($request->getUsername());
        if (!is_null($user)) {
            throw new ValidationException('Username already exist. Please choose another username.');
        }

        // validate password
        $passwordViolations = $validator->validate($request->getPassword(), [
            new NotBlank([
                'message' => 'Password should not be blank.',
            ]),
            new PasswordStrength(),
        ]);
        if (count($passwordViolations) > 0) {
            throw new ValidationException($passwordViolations[0]->getMessage());
        }

    }
}
