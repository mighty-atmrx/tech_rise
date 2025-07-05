<?php

namespace App\Service;

use App\Email\ForgotPasswordEmail;
use App\Entity\PasswordReset;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use HttpResponseException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;

class ForgotPasswordService
{
    protected $userRepository;
    protected $logger;
    protected $passwordResetRepository;
    protected $mailer;

    public function __construct(UserRepository $userRepository,
                                LoggerInterface $logger,
                                PasswordResetRepository $passwordResetRepository,
                                MailerInterface $mailer
    ){
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->mailer = $mailer;
    }

    public function forgotPassword(string $email): void
    {
        $user =  $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $this->logger->error('Password reset attempted for non-existent email.');
            throw new HttpResponseException('User not found. Please try again.');
        }

        $token = bin2hex(random_bytes(32));

        $passwordReset = new PasswordReset();
        $passwordReset->setEmail($email);
        $passwordReset->setToken($token);
        $passwordReset->setCreatedAt(new \DateTimeImmutable());

        $this->passwordResetRepository->create($passwordReset);

        $message = (new ForgotPasswordEmail($email, $token));
        $this->mailer->send($message);
    }
}
