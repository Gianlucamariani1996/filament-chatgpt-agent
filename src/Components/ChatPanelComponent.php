<?php

namespace FilamentAgenticChat\Components;

use FilamentAgenticChat\AgenticChatPlugin;
use FilamentAgenticChat\Services\ChatServices;
use Livewire\Attributes\Session;
use Livewire\Component;

class ChatPanelComponent extends Component
{

    public string $name;

    public string $buttonText;

    public string $buttonIcon;

    public string $sendingText;

    public array $messages;

    #[Session]
    public string $question;

    public string $questionContext;

    public bool $pageWatcherEnabled;

    public string $pageWatcherSelector;

    public string $winWidth;

    public string $winPosition;

    public bool $showPositionBtn;

    public bool $panelHidden;

    public string|bool $logoUrl;

    public function mount(): void
    {
        $this->panelHidden = session($this->sessionKey() . '-panelHidden', true);
        $this->winWidth = "width:" . $this->plugin()->getDefaultPanelWidth() . ";";
        $this->winPosition = session($this->sessionKey() . '-winPosition', '');
        $this->showPositionBtn = true;
        $this->messages = session(
            $this->sessionKey(),
            $this->getDefaultMessages()
        );
        $this->question = "";
        $this->name = $this->plugin()->getBotName();
        $this->buttonText = $this->plugin()->getButtonText();
        $this->buttonIcon = $this->plugin()->getButtonIcon();
        $this->sendingText = $this->plugin()->getSendingText();
        $this->questionContext = '';
        $this->pageWatcherEnabled = $this->plugin()->isPageWatcherEnabled();
        $this->pageWatcherSelector = $this->plugin()->getPageWatcherSelector();
        $this->logoUrl = $this->plugin()->getLogoUrl();
    }

    public function render()
    {
        return view('chatgpt-agent::livewire.chat-bot');
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->question))) {
            $this->question = "";
            return;
        }
        $this->messages[] = [
            "role" => 'user',
            "content" => $this->question,
        ];

        $this->chat();
        $this->question = "";
        $this->dispatch('sendmessage', ['message' => $this->question]);
    }

    public function changeWinWidth(): void
    {
        if ($this->winWidth == "width:" . $this->plugin()->getDefaultPanelWidth() . ";") {
            $this->winWidth = "width:100%;";
            $this->showPositionBtn = false;
        } else {
            $this->winWidth = "width:" . $this->plugin()->getDefaultPanelWidth() . ";";
            $this->showPositionBtn = true;
        }
    }

    public function changeWinPosition(): void
    {
        if ($this->winPosition != "left") {
            $this->winPosition = "left";
        } else {
            $this->winPosition = "";
        }
        session([$this->sessionKey() . '-winPosition' => $this->winPosition]);
    }

    public function resetSession(): void
    {
        request()->session()->forget($this->sessionKey());
        $this->messages = $this->getDefaultMessages();
    }

    public function togglePanel(): void
    {
        $this->panelHidden = !$this->panelHidden;
        session([$this->sessionKey() . '-panelHidden' => $this->panelHidden]);
    }

    protected function chat(): void
    {
        $chat = new ChatServices();
        $chat->loadMessages($this->messages);
        if ($this->pageWatcherEnabled) {
            $chat->addMessage($this->plugin()->getPageWatcherMessage() . $this->questionContext);
            \Log::info($this->questionContext);
        }

        $chat->send();

        $this->messages[] = ['role' => 'assistant', 'content' => $chat->latestMessage()->content];

        request()->session()->put($this->sessionKey(), $this->messages);

    }

    protected function getDefaultMessages(): array
    {
        return $this->plugin()->getStartMessage() ?
            [
                ['role' => 'assistant', 'content' => $this->plugin()->getStartMessage()],
            ] : [];
    }

    protected function plugin(): AgenticChatPlugin
    {
        return AgenticChatPlugin::get() ?? AgenticChatPlugin::make();
    }

    protected function sessionKey(): string
    {
        return auth()->id() . '-chatgpt-agent-messages';
    }
}
