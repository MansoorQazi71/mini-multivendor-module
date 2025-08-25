<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProductPendingApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New product pending approval')
            ->line('A new product was submitted and is pending approval.')
            ->line('Product: '.$this->product->name.' ('.$this->product->code.')')
            ->action('Review Pending Products', url('/admin/products/pending'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id'   => $this->product->id,
            'product_name' => $this->product->name,
            'code'         => $this->product->code,
            'message'      => 'New product pending approval.',
        ];
    }
}
