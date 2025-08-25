<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Models\User;
use App\Notifications\NewProductPendingApproval;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendProductPendingApproval implements ShouldQueue
{
    public function handle(ProductCreated $event): void
    {
        User::where('role', 'admin')->each(function ($admin) use ($event) {
            $admin->notify(new NewProductPendingApproval($event->product));
        });
    }
}
