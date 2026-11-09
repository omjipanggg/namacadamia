<?php

namespace App\Notifications;

use App\Models\Order;

use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Order $order)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            "title" => "New Order",
            "message" => "Order #{$this->order->invoice_number} has been placed",
            "icon" => 'cart-check',
            'variant' => 'success',
            'url' => "/dashboard/orders/{$this->order->id}",
            'category' => 'order',
            'meta' => [
                'order_id' => $this->order->id,
                'invoice_number' => $this->order->invoice_number,
                'total' => $this->order->grand_total,
            ],
        ];
    }
}
