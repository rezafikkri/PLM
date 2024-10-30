<?php

namespace RezaFikkri\PLM\Service;

use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\{
    UserLoginRequest,
    UserLoginResponse,
    UserPasswordUpdateRequest,
    UserPasswordUpdateResponse,
    UserProfileUpdateRequest,
    UserProfileUpdateResponse,
    UserRegisterRequest,
    UserRegisterResponse,
};
use RezaFikkri\PLM\Repository\UserRepository;
use RezaFikkri\PLM\Validator\IsUnique;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Validation;

// biasanya function/method pada service dibuat sesuai api (Application Programming Interface)-nya
// karena aplikasi ini kita buat dalam bentuk tampilan web
// maka anggap saja method service-nya adalah action-nya, ex. register.
class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
        
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        $user = new User;
        $user->setUsername($request->getUsername());
        $user->setPassword(password_hash($request->getPassword(), PASSWORD_BCRYPT));
        $this->userRepository->save($user);

        $response = new UserRegisterResponse;
        $response->setUser($user);
        return $response;
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request): void
    {
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
        $this->validateUserLoginRequest($request);

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

    private function validateUserLoginRequest(UserLoginRequest $request): void
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

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($request);

        $user = $this->userRepository->findById($request->getId());
        if (is_null($user)) {
            throw new ValidationException(['User is not found.']);
        }
        $user->setUsername($request->getUsername());

        $this->userRepository->update($user);

        $response = new UserProfileUpdateResponse;
        $response->setUser($user);
        return $response;
    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request): void
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($request->getUsername(), new Sequentially([
            new NotBlank([
                'message' => 'Username should not be blank.'
            ]),
            new Length([
                'min' => 4,
                'minMessage' => 'Username is too short. It should have {{ limit }} characters or more.',
            ]),
            new IsUnique('users', 'username', 'id', $request->getId()),
        ]));

        if (count($violations) > 0) {
            // mengapa melakukan throw? karena bagusnya, jika terjadi error, misalnya seperti
            // ada validasi yang tidak lolos, maka sebaiknya kita melakukan throw Exception
            throw new ValidationException($violations);
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
    {
        $this->validateUserPasswordUpdateRequest($request);

        $user = $this->userRepository->findById($request->getId());
        if (is_null($user)) {
            throw new ValidationException(['User is not found.']);
        }
        if (!password_verify($request->getOldPassword(), $user->getPassword())) {
            throw new ValidationException(['Old Password is wrong.']);
        }

        $user->setPassword(password_hash($request->getNewPassword(), PASSWORD_BCRYPT));
        $this->userRepository->update($user);

        $response = new UserPasswordUpdateResponse;
        $response->setUser($user);
        return $response;
    }

    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request): void
    {
        $validator = Validation::createValidator();

        $input = $request->getIterator()->getArrayCopy();
        $constraint = new Collection([
            'oldPassword' => new NotBlank([
                'message' => 'Old Password should not be blank.',
            ]),
            'newPassword' => new Sequentially([
                new NotBlank([
                    'message' => 'New Password should not be blank.',
                ]),
                new PasswordStrength([
                    'message' => 'The New Password strength is too low. Please use a stronger New Password.',
                ]),
            ]),
        ]);
        $violations = $validator->validate($input, $constraint);

        if (count($violations) > 0) {
            // mengapa melakukan throw? karena bagusnya, jika terjadi error, misalnya seperti
            // ada validasi yang tidak lolos, maka sebaiknya kita melakukan throw Exception
            throw new ValidationException($violations);
        }
    }
}
