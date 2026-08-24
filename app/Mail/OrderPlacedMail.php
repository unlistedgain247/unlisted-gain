<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $type, // 'buy' | 'sell'
        public string $stockName,
        public int $quantity,
        public float $pricePerShare,
        public float $amount,
        public int $orderId,
    ) {
    }

    public function build(): self
    {
        $verb = $this->type === 'sell' ? 'Sell' : 'Buy';

        return $this->subject("Your {$verb} Order Request — UnlistedGain")
            ->view('emails.order-placed');
    }
}
