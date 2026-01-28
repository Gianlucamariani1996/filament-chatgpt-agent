<?php

namespace LikeABas\FilamentChatgptAgent;

use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\SystemMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

class ChatgptChat
{
    /**
     * @var array<int, array{role: string, content: string}>
     */
    protected array $messages = [];

    protected ?string $latestResponse = null;

    protected function plugin(): ChatgptAgentPlugin
    {
        return ChatgptAgentPlugin::get() ?? ChatgptAgentPlugin::make();
    }

    public function addMessage(string $content, string $role = 'system'): void
    {
        $this->messages[] = [
            'role' => $role,
            'content' => $content,
        ];
    }

    public function loadMessages(array $messages): static
    {
        $this->messages = collect($messages)->map(function ($message) {
            return [
                'role' => $message['role'],
                'content' => $message['content'],
            ];
        })->toArray();

        return $this;
    }

    public function send(): void
    {
        $messages = $this->messages;
        $systemMessage = $this->plugin()->getSystemMessage();

        if ($systemMessage !== '') {
            array_unshift($messages, [
                'role' => 'system',
                'content' => $systemMessage,
            ]);
        }

        $builder = Prism::text()
            ->using(Provider::OpenAI, $this->plugin()->getModel())
            ->withMessages($this->mapMessagesForPrism($messages));

        $temperature = $this->plugin()->getTemperature();
        if (method_exists($builder, 'temperature') && $temperature !== null) {
            $builder = $builder->temperature($temperature);
        }

        $maxTokens = $this->plugin()->getMaxTokens();
        if (method_exists($builder, 'maxTokens') && $maxTokens !== null) {
            $builder = $builder->maxTokens($maxTokens);
        }

        $response = $builder->generate();
        $this->latestResponse = $response->text ?? '';
    }

    public function latestMessage(): object
    {
        return (object) [
            'content' => $this->latestResponse ?? '',
        ];
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array<int, UserMessage|AssistantMessage|SystemMessage>
     */
    protected function mapMessagesForPrism(array $messages): array
    {
        return collect($messages)
            ->map(function (array $message): UserMessage|AssistantMessage|SystemMessage {
                return match ($message['role']) {
                    'user' => new UserMessage($message['content']),
                    'assistant' => new AssistantMessage($message['content']),
                    'system' => new SystemMessage($message['content']),
                    default => new SystemMessage($message['content']),
                };
            })
            ->values()
            ->all();
    }
}
