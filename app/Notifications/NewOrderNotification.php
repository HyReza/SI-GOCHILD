<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\ServiceOrder;

class NewOrderNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct(ServiceOrder $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database']; // Simpan ke database agar muncul di lonceng notifikasi
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'title' => 'Pesanan Layanan Baru',
            'message' => 'Pesanan baru dari ' . $this->order->student->student_name . ' perlu diperiksa/diverifikasi.',
            'url' => route('orders.show', $this->order->id),
            'icon' => 'shopping-cart',
            'color' => 'blue'
        ];
    }
}
