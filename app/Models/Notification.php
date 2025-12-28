<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification as BaseNotification;

class Notification extends BaseNotification
{
    // Kita extends BaseNotification agar tetap bisa menggunakan fitur bawaan
    // seperti markAsRead(), read(), unread(), dll.

    // Contoh: Jika Anda ingin menambahkan helper khusus
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
