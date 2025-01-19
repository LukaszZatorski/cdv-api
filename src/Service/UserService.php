<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function createUser(string $email, string $name, string $surname, array $roles): User
    {
        $user = new User();
        $password = $this->generatePassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setName($name);
        $user->setSurname($surname);
        $user->setRoles($roles);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function generatePassword(): string
    {
        if ($_ENV['APP_ENV'] === 'dev') {
            return 't4jn3h4slo';
        }

        return bin2hex(random_bytes(8));
    }
}
