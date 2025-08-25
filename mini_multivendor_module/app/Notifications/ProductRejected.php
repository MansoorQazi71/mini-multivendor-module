<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductRejected extends Notification implements ShouldQueue
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
            ->subject('Your product was rejected')
            ->line('Unfortunately, your product was rejected.')
            ->line('Product: '.$this->product->name.' ('.$this->product->code.')')
            ->action('Edit Product', url('/products/'.$this->product->id.'/edit'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id'   => $this->product->id,
            'product_name' => $this->product->name,
            'code'         => $this->product->code,
            'status'       => 'rejected',
            'message'      => 'Your product was rejected.',
        ];
    }
}
