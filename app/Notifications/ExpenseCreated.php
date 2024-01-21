<?php

namespace App\Notifications;

use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ExpenseCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Expense $expense)
    {
        //$this->expense->load('user');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        //Log::debug($this->expense->amount.' expense - '.$this->expense->category->name);
        return (new WebPushMessage)
            ->title($this->expense->formatted_amount.' expense - '.$this->expense->category->name)
            ->icon('/images/icons/icon-512x512.png')
            ->body('Created by '.$this->expense->user->name)
            ->action('View account', 'view_account')
            ->options(['TTL' => 1000]);
        // ->data(['id' => $notification->id])
        // ->badge()
        // ->dir()
        // ->image()
        // ->lang()
        // ->renotify()
        // ->requireInteraction()
        // ->tag()
        // ->vibrate()
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
