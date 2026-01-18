<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Message as MessageModel;
use App\Models\MessageGroup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Collection;
use Livewire\WithFileUploads;

class Message extends Component
{
    use WithFileUploads;

    public $selectedConversation = ['id' => null, 'type' => null, 'name' => null];
    public $newMessageText = '';
    public $newMessageFile;
    public $messages;
    public $users = []; // For direct messaging
    public $groupChat; // The single group chat
    public $loadingMessages = false; // Initialize to false
    public $selfId;
    public $groupId;

    protected $listeners = [
        'echo-private:chat.{selfId},MessageSent' => 'prependNewMessage',
        'echo-presence:group.{groupId},MessageSent' => 'prependNewGroupMessage',
        'echo-presence:group.{groupId},.user.joined' => '$refresh', // Refresh user list if needed
        'echo-presence:group.{groupId},.user.left' => '$refresh', // Refresh user list if needed
    ];

    public function mount()
    {
        $this->messages = collect();
        $user = Auth::user();
        $this->selfId = $user->id;
        $this->loadInitialData();

        if ($this->groupChat) {
            $this->groupId = $this->groupChat->id;
        }
    }

    public function loadInitialData()
    {
        $user = Auth::user();

        if ($user->isStudent()) {
            $this->users = User::where('role', 'student')
                ->where('course_id', $user->course_id)
                ->where('id', '!=', $user->id)
                ->get();
            $this->groupChat = MessageGroup::where('name', 'Course ' . $user->course->name)->firstOrCreate(['name' => 'Course ' . $user->course->name]);
            if ($this->groupChat->users()->where('user_id', $user->id)->doesntExist()) {
                $this->groupChat->users()->attach($user->id);
            }
            User::where('role', 'coordinator')->where('course_id', $user->course_id)->get()->each(function ($coordinator) {
                if ($this->groupChat->users()->where('user_id', $coordinator->id)->doesntExist()) {
                    $this->groupChat->users()->attach($coordinator->id);
                }
            });
        } elseif ($user->isCoordinator()) {
            $this->users = User::where('role', 'student')
                ->where('course_id', $user->course_id)
                ->get();
            $this->groupChat = MessageGroup::where('name', 'Course ' . $user->course->name)->firstOrCreate(['name' => 'Course ' . $user->course->name]);
            if ($this->groupChat->users()->where('user_id', $user->id)->doesntExist()) {
                $this->groupChat->users()->attach($user->id);
            }
            User::where('role', 'student')->where('course_id', $user->course_id)->get()->each(function ($student) {
                if ($this->groupChat->users()->where('user_id', $student->id)->doesntExist()) {
                    $this->groupChat->users()->attach($student->id);
                }
            });
        }

        if ($this->groupChat) {
            $this->joinGroupChat();
        }
    }

    public function selectConversation($conversation)
    {
        $this->messages = []; // Clear previous messages
        if (!$conversation || !isset($conversation['id'], $conversation['type'])) {
            return;
        }

        $this->selectedConversation = $conversation;
        $this->loadMessages(); // Load messages when a conversation is selected
    }

    public function loadMessages()
{
    $this->loadingMessages = true;
    $this->messages = [];

    if (!$this->selectedConversation || !isset($this->selectedConversation['id'], $this->selectedConversation['type'])) {
        $this->loadingMessages = false;
        \Log::warning('loadMessages called without valid selectedConversation');
        return;
    }

    $userId = Auth::id();
    $type = $this->selectedConversation['type'];
    $id = $this->selectedConversation['id'];

    if ($type === 'user') {
        $this->messages = MessageModel::where(function ($query) use ($userId, $id) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $id);
        })->orWhere(function ($query) use ($userId, $id) {
            $query->where('sender_id', $id)
                  ->where('receiver_id', $userId);
        })->orderBy('created_at')->get();
    } elseif ($type === 'group') {
        $this->messages = MessageModel::where('group_id', $id)
            ->orderBy('created_at')
            ->get();
    }

    \Log::info('loadMessages fetched', [
        'selectedConversation' => $this->selectedConversation,
        'messages_count' => $this->messages->count(),
        'messages' => $this->messages->map(function($msg) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'group_id' => $msg->group_id,
                'content' => $msg->content,
                'created_at' => $msg->created_at->toDateTimeString(),
            ];
        })->toArray(),
    ]);

    $this->loadingMessages = false;

    $this->dispatch('scroll-to-bottom');
}

    public function sendMessage()
    {
        \Log::info('sendMessage called');
        \Log::info('$this->selectedConversation:', (array) $this->selectedConversation);
        \Log::info('$this->newMessageText:', [$this->newMessageText]);
        \Log::info('$this->newMessageFile:', [$this->newMessageFile]);

        if (!empty($this->newMessageText) || $this->newMessageFile) {
            $user = Auth::user();
            $message = new MessageModel();
            $message->sender_id = $user->id;
            $message->content = $this->newMessageText;

            if ($this->selectedConversation['type'] === 'user' && $this->selectedConversation['id']) {
                $message->receiver_id = $this->selectedConversation['id'];
                $channel = "chat.{$this->selectedConversation['id']}";
                \Log::info('Sending to user:', ['receiver_id' => $message->receiver_id, 'channel' => $channel]);
            } elseif ($this->selectedConversation['type'] === 'group' && $this->selectedConversation['id']) {
                $message->group_id = $this->selectedConversation['id'];
                $channel = "group.{$this->selectedConversation['id']}";
                \Log::info('Sending to group:', ['group_id' => $message->group_id, 'channel' => $channel]);
            } else {
                \Log::error('Invalid selectedConversation type or ID:', (array) $this->selectedConversation);
                return;
            }

            if ($this->newMessageFile) {
                $path = $this->newMessageFile->store('chat-files', 'public');
                $message->media_path = $path;
                $message->type = $this->getFileType($this->newMessageFile->getMimeType());
            } else {
                $message->type = 'text';
            }

            try {
                $message->save();
                \Log::info('Message saved:', $message->toArray());
                $this->newMessageText = '';
                $this->newMessageFile = null;
                $this->messages[] = $message;
                broadcast(new MessageSent($message))->toOthers();
                \Log::info('Message broadcasted');
                $this->dispatch('scroll-to-bottom');
            } catch (\Exception $e) {
                \Log::error('Error saving message:', ['message' => $e->getMessage()]);
                $this->addError('sendMessage', 'There was an error sending your message.');
            }
        }
    }

    public function prependNewMessage($event)
    {
        if ($this->selectedConversation && $this->selectedConversation['type'] === 'user' && $event['message']['sender_id'] != $this->selfId && $event['message']['sender_id'] == $this->selectedConversation['id']) {
            $this->messages->prepend((object) $event['message']);
        } elseif ($this->selectedConversation && $this->selectedConversation['type'] === 'user' && $event['message']['sender_id'] != $this->selfId && $event['message']['receiver_id'] == $this->selfId && $event['message']['sender_id'] == $this->selectedConversation['id']) {
            $this->messages->prepend((object) $event['message']);
        }
    }

    public function prependNewGroupMessage($event)
    {
        if ($this->selectedConversation && $this->selectedConversation['type'] === 'group' && $event['message']['group_id'] == $this->selectedConversation['id'] && $event['message']['sender_id'] != $this->selfId) {
            $this->messages->prepend((object) $event['message']);
        }
    }

    public function joinGroupChat()
    {
        if ($this->groupChat) {
            $channelName = "group.{$this->groupChat->id}";
            $this->dispatch('$echo:presence.join', channel: $channelName);
        }
    }

    protected function getFileType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } else {
            return 'file';
        }
    }

    public function render()
    {
        $user = Auth::user();
        $conversations = collect();

        if ($user->isStudent()) {
            $studentChats = User::where('role', 'student')
                ->where('course_id', $user->course_id)
                ->where('id', '!=', $user->id)
                ->get()
                ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'type' => 'user']);
            $coordinator = User::where('role', 'coordinator')
                ->where('course_id', $user->course_id)
                ->first();
            if ($coordinator) {
                $conversations->push(['id' => $coordinator->id, 'name' => 'Coordinator', 'type' => 'user']);
            }
            $conversations = $conversations->concat($studentChats);
            if ($this->groupChat) {
                $conversations->prepend(['id' => $this->groupChat->id, 'name' => $this->groupChat->name, 'type' => 'group']);
            }
        } elseif ($user->isCoordinator()) {
            $studentChats = User::where('role', 'student')
                ->where('course_id', $user->course_id)
                ->get()
                ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'type' => 'user']);
            $conversations = $conversations->concat($studentChats);
            $coordinatorChats = User::where('role', 'coordinator')
                ->where('course_id', $user->course_id)
                ->where('id', '!=', $user->id)
                ->get()
                ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'type' => 'user']);
            $conversations = $conversations->concat($coordinatorChats);
            if ($this->groupChat) {
                $conversations->prepend(['id' => $this->groupChat->id, 'name' => $this->groupChat->name, 'type' => 'group']);
            }
        }

        return view('livewire.message', [
            'conversations' => $conversations,
        ])->layout('layouts.auth-layout');
    }
}