<?php

namespace App\Http\Controllers;

use Gate;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate as FacadesGate;

class NotificationSeenController extends Controller
{
    public function __invoke(DatabaseNotification $notification)
    {
        // $this->authorize('update', $notification);
        FacadesGate::authorize('update', $notification);
        $notification->markAsRead();

        return redirect()->back()
            ->with('success', 'Notification marked as read');
    }
}
