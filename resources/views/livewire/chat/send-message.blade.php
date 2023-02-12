<div>
    {{-- Success is as dangerous as failure. --}}
    @if ($selectedConversation)
        <form wire:submit.prevent="sendMessage" action="">
            <div class="chatbox_footer">

                <div class="custom_form_group">
                    <input type="text" wire:model="body" class="control" placeholder="write message..">
                    <button type="submit" class="submit">Send</button>
                </div>
            </div>
        </form>
    @endif
</div>
