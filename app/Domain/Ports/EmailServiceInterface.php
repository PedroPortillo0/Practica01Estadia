<?php

namespace App\Domain\Ports;

interface EmailServiceInterface
{
    public function sendVerificationEmail(string $email, string $code): void;
    public function sendWelcomeEmail(string $email, string $userName): void;
    public function sendPasswordResetEmail(string $email, string $code): void;
}
