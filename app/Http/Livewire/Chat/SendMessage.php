<?php

namespace App\Http\Livewire\Chat;

use App\Models\User;
use App\Models\Message;
use Livewire\Component;
use App\Events\MessageSent;
use App\Models\Conversation;

class SendMessage extends Component
{
    protected $listeners = ['updateSendMessage', 'dispatchMessageSent', 'resetComponent'];
    public $selectedConversation;
    public $receiver_instance;
    public $body;
    public $createdMessage;

    public function resetComponent() {
        $this->selectedConversation = null;
        $this->receiver_instance = null;
    }
    public function updateSendMessage(Conversation $conv, User $receiver) {
        $this->selectedConversation = $conv;
        $this->receiver_instance = $receiver;

    }
    public function sendMessage() {
        if($this->body == null) {
            return null;
        }
        $this->createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => auth()->user()->id,
            'receiver_id' => $this->receiver_instance->id,
            'body' => $this->body,
        ]);
        $this->selectedConversation->last_time_message = $this->createdMessage->created_at;
        $this->selectedConversation->save();
        $this->emitTo('chat.chatbox', 'pushMessage', $this->createdMessage->id);
        $this->emitTo('chat.chat-list', 'refresh');
        $this->reset('body');
        $this->emitSelf('dispatchMessageSent');
    }
    public function dispatchMessageSent() {
        broadcast(new MessageSent(auth()->user(), $this->createdMessage, $this->selectedConversation, $this->receiver_instance));
    }
    public function render()
    {
        return view('livewire.chat.send-message');
    }
}
