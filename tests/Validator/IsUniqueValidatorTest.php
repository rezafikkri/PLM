<?php

namespace RezaFikkri\PLM\Validator;

use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Repository\UserRepository;
use Symfony\Component\Validator\{
    Test\ConstraintValidatorTestCase,
    ConstraintValidatorInterface,
};

class IsUniqueValidatorTest extends ConstraintValidatorTestCase
{
    private IsUnique $isUniqueConstraint;
    private UserRepository $userRepository;

    #[Before]
    protected function clearDataInDb(): void
    {
        $this->isUniqueConstraint = new IsUnique('users', 'username');
        $this->userRepository = new UserRepository(Database::getConnection());
        // clear all data in target table (users)
        $this->userRepository->deleteAll();
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new IsUniqueValidator;
    }

    #[Test]
    #[TestDox('IsUnique is valid')]
    public function isUniqueIsValid(): void
    {
        $this->validator->validate('rezafikkri', $this->isUniqueConstraint);
        $this->assertNoViolation();
    }

    #[Test]
    #[TestDox('IsUnique is invalid')]
    public function isUniqueIsInvalid(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword('password');
        $this->userRepository->save($user);

        $this->validator->validate('rezafikkri', $this->isUniqueConstraint);

        $this->buildViolation($this->isUniqueConstraint->message)
            ->setParameter('{{ field }}', ucfirst($this->isUniqueConstraint->field))
            ->assertRaised();
    }

    #[Test]
    #[TestDox('IsUnique is invalid with ignore field')]
    public function isUniqueIsValidWithIgnoreField(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword('password');
        $this->userRepository->save($user);

        $this->isUniqueConstraint->ignoreField = 'id';
        $this->isUniqueConstraint->ignoreValue = $user->getId();

        $this->validator->validate('rezafikkri', $this->isUniqueConstraint);

        $this->assertNoViolation();
    }
}
