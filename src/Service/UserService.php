<?php

namespace App\Service;

use App\Dto\RegistrationRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        UserRepository $repository,
        private UserPasswordHasherInterface $passwordHasher,
    ){
        $this->repository = $repository;
    }

    public function registration(RegistrationRequest $dto)
    {
        $user = new User();
        $user->setLogin($dto->login);
        $user->setEmail($dto->email);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
        $user->setPassword($hashedPassword);

        $this->repository->create($user);
    }
}
