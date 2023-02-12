<?php

namespace App\Http\Livewire\Chat;

use App\Models\User;
use Livewire\Component;
use App\Models\Conversation;

class ChatList extends Component
{
    public $uth_id;
    public $conversations;
    public $receiver_instance;
    public $name;
    public $selectedConversation;
    protected $listeners = ['chatUserSelected', 'refresh'=>'$refresh', 'resetComponent'];

    public function resetComponent() {
        $this->selectedConversation = null;
        $this->receiver_instance = null;
    }
    public function chatUserSelected(Conversation $conv, $receiverId) {
        $this->selectedConversation = $conv;
        $receiverInstance = User::find($receiverId);
        $this-> emitTo('chat.chatbox', 'loadConversation', $this->selectedConversation, $receiverInstance);
        $this-> emitTo('chat.send-message', 'updateSendMessage', $this->selectedConversation, $receiverInstance);
    }
    public function getCahtUserInstance(Conversation $conversation, $request) {
        $this->uth_id = auth()->id();
        if($conversation->sender_id == $this->uth_id) {
            $this->receiver_instance = User::firstWhere('id', $conversation->receiver_id);
        }
        else {
            $this->receiver_instance = User::firstWhere('id', $conversation->sender_id);
        }
        if(isset($request)) {
            return $this->receiver_instance->$request;
        }
    }
    public function mount() {
        $this->uth_id = auth()->id();
        $this->conversations = Conversation::where('sender_id', $this->uth_id)->orWhere('receiver_id', $this->uth_id)
            ->orderBy('last_time_message', 'DESC')->get();
    }
    public function render()
    {
        return view('livewire.chat.chat-list');
    }
}
