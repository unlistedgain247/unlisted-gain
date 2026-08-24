<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $status, // 'Completed' | 'Cancelled'
        public string $type,   // 'buy' | 'sell'
        public string $stockName,
        public int $quantity,
        public float $pricePerShare,
        public float $amount,
        public int $orderId,
    ) {
    }

    public function build(): self
    {
        $subject = $this->status === 'Cancelled'
            ? 'Your Order Has Been Cancelled — UnlistedGain'
            : 'Your Order Is Completed — UnlistedGain';

        return $this->subject($subject)->view('emails.order-status');
    }
}
