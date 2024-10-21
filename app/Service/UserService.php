<?php

namespace RezaFikkri\PLM\Service;

use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\{UserLoginRequest, UserLoginResponse, UserRegisterRequest, UserRegisterResponse};
use RezaFikkri\PLM\Repository\UserRepository;
use RezaFikkri\PLM\Validator\IsUnique;
use Symfony\Component\Validator\Constraints\Collection;
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

        $input = $request->getIterator()->getArrayCopy();
        $constraint = new Collection([
            'username' => new Sequentially([
                new NotBlank([
                    'message' => 'Username should not be blank.'
                ]),
                new Length([
                    'min' => 4,
                    'minMessage' => 'Username is too short. It should have {{ limit }} characters or more.',
                ]),
                new IsUnique('users', 'username'),
            ]),
            'password' => new Sequentially([
                new NotBlank([
                    'message' => 'Password should not be blank.',
                ]),
                new PasswordStrength(),
            ]),
        ]);
        $violations = $validator->validate($input, $constraint);

        if (count($violations) > 0) {
            // mengapa melakukan throw? karena bagusnya, jika terjadi error, misalnya seperti
            // ada validasi yang tidak lolos, maka sebaiknya kita melakukan throw Exception
            throw new ValidationException($violations);
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->ValidateUserLoginRequest($request);

        $user = $this->userRepository->findByUsername($request->getUsername());
        if (is_null($user)) {
            throw new ValidationException(['Username or password is wrong.']);
        }

        if (password_verify($request->getPassword(), $user->getPassword())) {
            $response = new UserLoginResponse;
            $response->setUser($user);
            return $response;
        }

        throw new ValidationException(['Username or password is wrong.']);
    }

    private function ValidateUserLoginRequest(UserLoginRequest $request): void
    {
        // validate username
        $validator = Validation::createValidator();

        $input = $request->getIterator()->getArrayCopy();
        $constraint = new Collection([
            'username' => new NotBlank([
                'message' => 'Username should not be blank.'
            ]),
            'password' => new NotBlank([
                'message' => 'Password should not be blank.',
            ]),
        ]);
        $violations = $validator->validate($input, $constraint);

        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }
    }
}
