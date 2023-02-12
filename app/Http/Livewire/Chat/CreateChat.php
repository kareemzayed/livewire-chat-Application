<?php

namespace App\Http\Livewire\Chat;

use App\Models\User;
use App\Models\Message;
use Livewire\Component;
use App\Models\Conversation;

class CreateChat extends Component
{
    public $users;
    public $message = 'Hello, How are you !';

    public function checkconversation($receiverId){
        $checkConversation = Conversation::where('receiver_id', auth()->user()->id)->where('sender_id', $receiverId)->
            orWhere('receiver_id', $receiverId)->where('sender_id', auth()->user()->id)->get();

        if(count($checkConversation) == 0) {
            

            $createdConversation = Conversation::create([
                'receiver_id' => $receiverId,
                'sender_id' => auth()->user()->id,
                'last_time_message' => null,
            ]);

            $createdMessage = Message::create([
                'conversation_id' => $createdConversation->id,
                'sender_id' => auth()->user()->id,
                'receiver_id' => $receiverId,
                'body' => $this->message,
                'type' => 'String',
            ]);

            $createdConversation->last_time_message = $createdMessage->created_at;
            $createdConversation->save();

            dd($createdMessage);

        }
        else if(count($checkConversation) >= 1) {
            dd("Conversation Exist");
        }
    }
    public function render()
    {
        $this->users = User::where('id', '!=', auth()->user()->id)->get();
        return view('livewire.chat.create-chat');
    }
}
