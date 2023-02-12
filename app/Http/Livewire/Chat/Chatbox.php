<?php

namespace App\Http\Livewire\Chat;

use App\Models\User;
use App\Models\Message;
use Livewire\Component;
use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Models\Conversation;

class Chatbox extends Component
{
    // protected $listeners = ['loadConversation', 'pushMessage', 'loadmore', 'updateHeight'];
    public $selectedConversation;
    public $receiver_instance;
    public $paginateVar = 10;
    public $messages;
    public $height;

    public function getListeners() {
        $auth_id = auth()->user()->id;
        return [
            "echo-private:chat.{$auth_id},MessageSent" => 'broadcastedMessageReceived',
            "echo-private:chat.{$auth_id},MessageRead" => 'broadcastedMessageRead',
            'loadConversation', 'pushMessage', 'loadmore', 'updateHeight', 'broadcastMessageRead',
            'resetComponent',
        ];
    }
    public function resetComponent() {
        $this->selectedConversation = null;
        $this->receiver_instance = null;
    }
    public function broadcastedMessageRead($event) {
        if($this->selectedConversation) {
            if((int) $this->selectedConversation->id === (int) $event['conversation_id']){
                $this->dispatchBrowserEvent('markMessageAsRead');
            }
        }
    }
    public function broadcastedMessageReceived($event) {
        $this->emitTo('chat.chat-list', 'refresh');
        $broadcastedMessage = Message::find($event['message_id']);
        if($this->selectedConversation) {
            if((int) $this->selectedConversation->id === (int) $event['conversation_id']){
                $broadcastedMessage->read = 1;
                $broadcastedMessage->save();
                $this->pushMessage($broadcastedMessage->id);
                $this->emitSelf('broadcastMessageRead');
            }
        }
    }
    public function broadcastMessageRead() {
        broadcast(new MessageRead($this->selectedConversation->id, $this->receiver_instance->id));
    }
    public function pushMessage($MessageId) {
        $newMessage = Message::find($MessageId);
        $this->messages->push($newMessage);
        $this->dispatchBrowserEvent('rowChatToBottom');
    }
    public function loadmore() {
        $this->paginateVar = $this->paginateVar + 10;
        $this->messageCount = Message::where('conversation_id', $this->selectedConversation->id)->count();
        $this->messages = Message::where('conversation_id', $this->selectedConversation->id)
            ->skip($this->messageCount - $this->paginateVar)->take($this->paginateVar)->get();
        $height = $this->height;
        $this->dispatchBrowserEvent('updateHeight', ($height));
    }
    public function updateHeight($height) {
        $this->height = $height;

    }
    public function loadConversation(Conversation $conv, User $receiver) {
        $this->selectedConversation = $conv;
        $this->receiver_instance = $receiver;
        $this->messageCount = Message::where('conversation_id', $this->selectedConversation->id)->count();
        $this->messages = Message::where('conversation_id', $this->selectedConversation->id)
            ->skip($this->messageCount - $this->paginateVar)->take($this->paginateVar)->get();
        $this->dispatchBrowserEvent('chatSelected');
        Message::where('conversation_id', $this->selectedConversation->id)->where('receiver_id', auth()->user()->id)
            ->update(['read' => 1]);
        $this->emitSelf('broadcastMessageRead');
    }
    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}
