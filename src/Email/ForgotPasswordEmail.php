<?php

namespace App\Email;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ForgotPasswordEmail extends TemplatedEmail
{
    public function __construct(string $to, string $token)
    {
        parent::__construct();

        $this->from('laravel_programmer01@gmail.com')
            ->to($to)
            ->subject('Reset Password')
            ->htmlTemplate('emails/forgot_password.html.twig')
            ->context([
                'token' => $token
            ]);
    }
}
