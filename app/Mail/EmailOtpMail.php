<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code, public string $purpose = 'registration')
    {
    }

    public function build(): self
    {
        $subject = $this->purpose === 'login'
            ? 'Your UnlistedGain sign-in code'
            : 'Your UnlistedGain verification code';

        return $this->subject($subject)->view('emails.email-otp');
    }
}
