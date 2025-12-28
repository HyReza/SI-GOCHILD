<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\ServiceOrder;

class OrderReadyToProcessNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct(ServiceOrder $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'title' => 'Tugas Layanan Baru',
            'message' => 'Layanan ' . $this->order->extraService->name . ' untuk ' . $this->order->student->student_name . ' siap dikerjakan.',
            'url' => route('orders.show', $this->order->id),
            'icon' => 'clipboard-check',
            'color' => 'yellow'
        ];
    }
}
