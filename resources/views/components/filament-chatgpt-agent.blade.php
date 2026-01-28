<div>
    @php($plugin = \FilamentAgenticChat\AgenticChatPlugin::get())
    @if($plugin?->isEnabled())
        @livewire('fi-chatgpt-agent')
    @endif
</div>
