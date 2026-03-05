<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class VerifyEmailQueuedNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;
}
