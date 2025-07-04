<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3)]
    #[Assert\Regex(pattern: '/^[a-z0-9]+$/')]
    public string $login;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 6)]
    #[Assert\Regex(
        pattern: '/^(?=.*[-_#$])[a-zA-Z0-9-_#$*!]+$/',
        message: 'Password must contain at least one special character: - _ # $ * !'
    )]
    public string $password;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;
}
