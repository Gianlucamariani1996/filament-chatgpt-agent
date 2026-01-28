# ChatGPT Agent for Laravel Filament

Filament ChatGPT Agent is a Filament plugin that allows you to easily integrate ChatGPT into your Filament project, enabling ChatGPT to access context information from your project by creating GPT functions.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/likeabas/filament-chatgpt-agent.svg?style=flat-square)](https://packagist.org/packages/likeabas/filament-chatgpt-agent)
[![Total Downloads](https://img.shields.io/packagist/dt/likeabas/filament-chatgpt-agent.svg?style=flat-square)](https://packagist.org/packages/likeabas/filament-chatgpt-agent)

## Preview:
Dark Mode:
![](https://raw.githubusercontent.com/likeabas/filament-chatgpt-agent/main/screenshots/darkmode.png)
Select a text to quickly insert it:
![](https://raw.githubusercontent.com/likeabas/filament-chatgpt-agent/main/screenshots/select-to-insert.png)
Light Mode:
![](https://raw.githubusercontent.com/likeabas/filament-chatgpt-agent/main/screenshots/lightmode.png)
ChatGPT can read the page content for extra context:
![](https://raw.githubusercontent.com/likeabas/filament-chatgpt-agent/main/screenshots/page-watcher.png)

## Features

I asked ChatGPT to generate a full list of the plugin features:

- **Seamless ChatGPT Integration**: Easily integrates OpenAI’s ChatGPT into your Filament project.
- **Customizable Chat Interface**: Modify bot name, button text, panel width, and more.
- **Select To Insert**: Select some text on the page and insert that with one click.
- **Supports Laravel GPT Functions**: Define and register custom GPT functions to enhance AI capabilities.
- **Page Watcher**: Sends the page content and URL to ChatGPT for better contextual responses.
- **Configurable OpenAI Model**: Choose different models like `gpt-4o` or `gpt-4o-mini` and control temperature and token usage.
- **Custom System Message**: Define how the AI should behave using a system instruction.
- **Full Screen Mode**: The more space the better.
- **Dark Mode Support**: Specially tailored to night owls.

## Screenshots

## Installation

You need to have [Laravel GPT from Malkuhr](https://github.com/maltekuhr/laravel-gpt) installed to use this package. If you haven't done so, follow the [installation instructions](https://github.com/maltekuhr/laravel-gpt?tab=readme-ov-file#installation):

You can install the package via composer:

```bash
composer require maltekuhr/laravel-gpt:^0.1.5
```

Next you need to configure your OpenAI API Key and Organization ID. You can find both in the [OpenAI Dashboard](https://platform.openai.com/account/org-settings).

```dotenv
OPENAI_ORGANIZATION=YOUR_ORGANIZATION_ID
OPENAI_API_KEY=YOUR_API_KEY
```

Now install this package:

```bash
composer require likeabas/filament-chatgpt-agent
```

## Views

Optionally, you can publish the views:

```bash
php artisan vendor:publish --tag="chatgpt-agent-views"
```

## Translations

Optionally, you can publish translations:

```bash
php artisan vendor:publish --tag="chatgpt-agent-translations"
```

## Usage

### 1. Adding the Plugin to Filament Panel

Modify your Filament [Panel Configuration](https://laravel-filament.cn/docs/en/3.x/panels/configuration) to include the plugin:


```php
use FilamentAgenticChat\AgenticChatPlugin;

    public function panel(Panel $panel): Panel
    {
        return $panel
            ...
            ->plugin(
                AgenticChatPlugin::make()
            )
            ...
    }
```

### 2. You can customize the plugin using the available options:

Also see [all available options](#available-options) below.

```php
use App\GPT\Functions\YourCustomGPTFunction;
use FilamentAgenticChat\AgenticChatPlugin;

...

    public function panel(Panel $panel): Panel
    {
        return $panel
            ...
            ->plugin(
                AgenticChatPlugin::make()
                    ->defaultPanelWidth('400px') // default 350px
                    ->botName('GPT Assistant')
                    ->model('gpt-4o')
                    ->buttonText('Ask ChatGPT')
                    ->buttonIcon('heroicon-m-sparkles')
                    // System instructions for the GPT
                    ->systemMessage('Act nice and help') 
                    // Array of GPTFunctions the GPT can use
                    ->functions([ 
                        new YourCustomGPTFunction(),
                    ])
                    // Default start message, set to false to not show a message
                    ->startMessage('Hello sir! How can I help you today?') 
                    ->pageWatcherEnabled(true)

            )
            ...
    }
```
> Other language strings can be altered in the translations file. Just [publish the translations](#translations).


See the [full list of available options](#available-options)

### 3. Blade Component Usage

You can embed the ChatGPT agent in any Blade file:

```blade
<body>  
    @livewire('filament-chatgpt-agent')  
</body>
```

> This works for all Livewire pages in any Laravel project, not just Filament. Ensure Tailwind CSS, Filament, and Livewire are properly imported.



```blade
<body>

    ...

    @livewire('filament-chatgpt-agent')
</body>
```

## Available Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled()` | `bool,Closure` | `auth()->check()` | Enables or disables the ChatGPT agent. |
| `botName()` | `string,Closure` | `'ChatGPT Agent'` | Sets the displayed name of the bot. |
| `buttonText()` | `string,Closure` | `'Ask ChatGPT'` | Customizes the button text. |
| `buttonIcon()` | `string,Closure` | `'heroicon-m-sparkles'` | Defines the button icon. |
| `sendingText()` | `string,Closure` | `'Sending...'` | Text displayed while sending a message. |
| `model()` | `string,Closure` | `'gpt-4o-mini'` | Defines the ChatGPT model used. |
| `temperature()` | `float,Closure` | `0.7` | Controls response randomness. Lower is more deterministic. 0-2. |
| `maxTokens()` | `int,Closure` | `null` | Limits the token count per response. `null` is no limit. |
| `systemMessage()` | `string,Closure` | `''` | Provides system instructions for the bot. |
| `functions()` | `array,Closure` | `[]` | Defines callable GPT functions. See [Using Laravel GPT Functions](#using-laravel-gpt-functions) |
| `defaultPanelWidth()` | `string,Closure` | `'350px'` | Sets the chat panel width. |
| `pageWatcherEnabled()` | `bool,Closure` | `false` | See the [Page wachter](#page-watcher) option. |
| `pageWatcherSelector()` | `string,Closure` | `'.fi-page'` | Sets the CSS selector for the page watcher. |
| `pageWatcherMessage()` | `string,Closure,null` | `null` | Message displayed when the page changes. |
| `startMessage()` | `string,bool,Closure` | `false` | Default message on panel open. Set to `false` to disable. |
| `logoUrl()` | `string,bool,Closure` | `false` | Overwrite the chat avatar / logo. Set to `false` to show a default GPT icon. |

## Using Laravel GPT Functions

Laravel GPT allows you to define custom **GPTFunctions** that ChatGPT can call to execute tasks within your application. This is useful for integrating dynamic data retrieval, calculations, or external API calls into the ChatGPT responses.

Refer to the [Laravel GPT documentation](https://github.com/maltekuhr/laravel-gpt) for more details.

## Page Watcher

![](https://raw.githubusercontent.com/likeabas/filament-chatgpt-agent/main/screenshots/page-watcher.png)

The **Page Watcher** feature allows the ChatGPT agent to receive additional context about the current page by including the `.innerText` of a specified page element (default: `.fi-page`, the Filament page container) along with the page URL in each message sent to ChatGPT. This helps provide better contextual responses based on the page content.

### Privacy Considerations

**Use this feature with caution.** Since the entire page content (or the selected element's content) is sent to ChatGPT, users should be informed of this behavior. The `pageWatcherEnabled` option supports a closure, allowing you to provide an opt-in mechanism for users.

### Enabling Page Watcher

To enable the Page Watcher feature, set the `pageWatcherEnabled` option to `true` and define a selector for the element to monitor:

```php
public function panel(Panel $panel): Panel  
{
    return $panel
        ->plugin(
            ChatgptAgentPlugin::make()
                ->pageWatcherEnabled(true) // Enable page watcher
                ->pageWatcherSelector('.custom-content') // Specify the selector
                ->pageWatcherMessage(
                    "This is the plain text the user can see on the page, use it as additional context for the previous message:\n\n"
                ) // Optional custom message for ChatGPT
        );
}
```

Alternatively, you can use a closure to enable the feature conditionally, such as requiring users to opt-in:

```php
public function panel(Panel $panel): Panel  
{
    return $panel
        ->plugin(
            ChatgptAgentPlugin::make()
                ->pageWatcherEnabled(fn () => auth()->user()->settings['enable_page_watcher'] ?? false) // User opt-in
                ->pageWatcherSelector('.fi-page')
        );
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- Package created by [Bas Schleijpen](https://github.com/likeabas).
- The view and livewire component structure was inspired by [Martin Hwang](https://github.com/icetalker).
- [All Contributors](../../contributors)
