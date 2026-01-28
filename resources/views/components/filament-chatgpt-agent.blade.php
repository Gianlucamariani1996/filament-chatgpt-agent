<div>
    @php($plugin = \LikeABas\FilamentChatgptAgent\ChatgptAgentPlugin::get())
    @if($plugin?->isEnabled())
        @livewire('fi-chatgpt-agent')
    @endif
</div>
