<?php

namespace App\Infrastructure\Services;

use App\Domain\Ports\EmailServiceInterface;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Exception;

class LaravelEmailService implements EmailServiceInterface
{
    public function sendVerificationEmail(string $email, string $code): void
    {
        try {
            Mail::html($this->getVerificationEmailTemplate($code), function (Message $message) use ($email) {
                $message->to($email)
                        ->subject('Código de Verificación - Sistema de Usuarios')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            \Log::info("Email de verificación enviado a: {$email} con código: {$code}");

        } catch (Exception $e) {
            \Log::error('Error al enviar email de verificación: ' . $e->getMessage());
            throw new Exception('Error al enviar email de verificación');
        }
    }

    public function sendWelcomeEmail(string $email, string $userName): void
    {
        try {
            Mail::html($this->getWelcomeEmailTemplate($userName), function (Message $message) use ($email) {
                $message->to($email)
                        ->subject('¡Bienvenido! - Sistema de Usuarios')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            \Log::info("Email de bienvenida enviado a: {$email}");

        } catch (Exception $e) {
            \Log::error('Error al enviar email de bienvenida: ' . $e->getMessage());
            // No lanzamos error aquí porque es menos crítico
        }
    }

    private function getVerificationEmailTemplate(string $code): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; text-align: center;'>
            <h2 style='color: #333;'>Código de Verificación</h2>
            <p>¡Hola! Gracias por registrarte en nuestro sistema.</p>
            <p>Para completar tu registro, ingresa el siguiente código de verificación:</p>
            
            <div style='background-color: #f8f9fa; padding: 30px; margin: 30px 0; border-radius: 10px;'>
                <h1 style='font-size: 48px; color: #007bff; margin: 0; letter-spacing: 8px; font-weight: bold;'>
                    {$code}
                </h1>
            </div>
            
            <p style='color: #666; font-size: 14px;'>
                Este código expirará en <strong>15 minutos</strong>.
            </p>
            <p style='color: #666; font-size: 14px;'>
                Si no solicitaste esta verificación, puedes ignorar este email.
            </p>
        </div>";
    }

    private function getWelcomeEmailTemplate(string $userName): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2>¡Bienvenido, {$userName}!</h2>
            <p>Tu email ha sido verificado exitosamente.</p>
            <p>Ya puedes comenzar a usar todas las funcionalidades de nuestro sistema.</p>
            <p>Gracias por unirte a nosotros.</p>
        </div>";
    }

    public function sendPasswordResetEmail(string $email, string $code): void
    {
        try {
            Mail::html($this->getPasswordResetEmailTemplate($code), function (Message $message) use ($email) {
                $message->to($email)
                        ->subject('Código para Cambio de Contraseña - Sistema de Usuarios')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            \Log::info("Email de reset de contraseña enviado a: {$email} con código: {$code}");

        } catch (Exception $e) {
            \Log::error('Error al enviar email de reset de contraseña: ' . $e->getMessage());
            throw new Exception('Error al enviar email de reset de contraseña');
        }
    }

    private function getPasswordResetEmailTemplate(string $code): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; text-align: center;'>
            <h2 style='color: #dc3545;'>🔐 Cambio de Contraseña</h2>
            <p>Hemos recibido una solicitud para cambiar tu contraseña.</p>
            <p>Para continuar, ingresa el siguiente código de verificación:</p>
            
            <div style='background-color: #f8f9fa; padding: 30px; margin: 30px 0; border-radius: 10px; border: 2px solid #dc3545;'>
                <h1 style='font-size: 48px; color: #dc3545; margin: 0; letter-spacing: 8px; font-weight: bold;'>
                    {$code}
                </h1>
            </div>
            
            <p style='color: #666; font-size: 14px;'>
                Este código expirará en <strong>15 minutos</strong>.
            </p>
            <p style='color: #dc3545; font-size: 14px; font-weight: bold;'>
                Si no solicitaste este cambio, ignora este email y tu contraseña permanecerá sin cambios.
            </p>
        </div>";
    }
}
