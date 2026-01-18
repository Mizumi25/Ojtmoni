<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Announcement;
use App\Models\AnnouncementImage;
use Illuminate\Support\Facades\Auth;
use App\Events\AnnouncementPosted;

class Noticeboard extends Component
{
    use WithFileUploads;

    public $title = '';
    public $body = '';
    public $images = [];
    public $filter = 'all';

    public function updatedImages()
    {
        $this->validate([
            'images.*' => 'image|max:2048',
        ]);
    }

    public function removePreview($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    public function postAnnouncement()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $announcement = Announcement::create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'body' => $this->body,
        ]);

        foreach ($this->images as $image) {
            $path = $image->store('announcements', 'public');
            $announcement->images()->create(['path' => $path]);
        }

        broadcast(new AnnouncementPosted($announcement))->toOthers();

        $this->reset(['title', 'body', 'images']);
    }

    public function render()
    {
        $user = Auth::user();

        $announcements = Announcement::with(['user', 'images'])
            ->when($this->filter === 'coordinators', fn($q) => $q->whereHas('user', fn($q) => $q->where('role', 'coordinator')->where('course_id', $user->course_id)))
            ->when($this->filter === 'admins', fn($q) => $q->whereHas('user', fn($q) => $q->where('role', 'admin')))
            ->when($this->filter === 'me', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->get();

        return view('livewire.noticeboard', [
            'announcements' => $announcements,
            'user' => $user
        ]);
    }
}
