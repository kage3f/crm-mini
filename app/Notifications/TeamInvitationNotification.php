<?php

namespace App\Notifications;

use App\Models\TeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TeamInvitation $invitation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('invitations.accept', $this->invitation->token);

        return (new MailMessage)
            ->subject('Você foi convidado para o MiniCRM')
            ->greeting('Olá!')
            ->line('Você recebeu um convite para fazer parte de uma equipe no MiniCRM.')
            ->action('Aceitar convite', $url)
            ->line('Este link expira em 7 dias.')
            ->line('Se você não esperava este convite, ignore este email.');
    }
}
