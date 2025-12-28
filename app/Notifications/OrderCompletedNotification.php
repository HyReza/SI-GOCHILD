<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\ServiceOrder;

class OrderCompletedNotification extends Notification
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
        // Sesuaikan route history siswa Anda
        $url = route('service-orders.history') ?? '#';

        return [
            'order_id' => $this->order->id,
            'title' => 'Layanan Selesai',
            'message' => 'Layanan ' . $this->order->extraService->name . ' telah selesai dikerjakan. Lihat bukti foto.',
            'url' => $url,
            'icon' => 'check-circle',
            'color' => 'green'
        ];
    }
}
