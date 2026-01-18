<?php

namespace App\Livewire;

use App\Models\Notification as NotificationModel;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notification extends Component
{
    public $notifications = [];

    public function getListeners()
    {
        return [
            'echo:notifications.' . Auth::id() . ',new-notification' => 'receiveNotification',
        ];
    }

    public function mount()
    {
        $this->notifications = NotificationModel::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();
    }

    public function receiveNotification($data)
    {
        // Assuming $data['notification'] is an array or object with notification data
        array_unshift($this->notifications, $data['notification']);
    }

    public function markAsRead($id)
    {
        $notification = NotificationModel::find($id);
        $notification->update(['read' => true]);
    }

    public function deleteNotification($id)
    {
        $notification = NotificationModel::find($id);
        if ($notification) {
            $notification->delete();
            // Remove the notification from the list in the component
            $this->notifications = $this->notifications->filter(fn($n) => $n->id !== $id);
        }
    }

    public function render()
    {
        return view('livewire.notification');
    }
}
