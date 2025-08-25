<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via(object $notifiable): array
    {
        // If you only want DB, return ['database']
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your product was approved')
            ->line('Good news! Your product has been approved.')
            ->line('Product: '.$this->product->name.' ('.$this->product->code.')')
            ->action('View My Products', url('/products'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id'   => $this->product->id,
            'product_name' => $this->product->name,
            'code'         => $this->product->code,
            'status'       => 'approved',
            'message'      => 'Your product was approved.',
        ];
    }
}
