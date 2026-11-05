<?php

namespace App\Services;

use Generator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatbotService
{
    /**
     * Map friendly or legacy model names to active Google Gemini models.
     *
     * @var array<string, string>
     */
    protected array $modelMap = [
        'gemini-3.1-flash-lite' => 'gemini-3.1-flash-lite',
        'gemini-3.5-flash-lite' => 'gemini-3.5-flash-lite',
        'gemini-3.8-flash' => 'gemini-3.8-flash',
        'gemini-4-argon' => 'gemini-4-argon',
        // Legacy / previous version aliases
        'gemini-2.0-flash' => 'gemini-3.1-flash-lite',
        'gemini-2.5-flash' => 'gemini-3.5-flash-lite',
        'gemini-2.5-pro' => 'gemini-4-argon',
        'gemini-2.0-flash-thinking-exp' => 'gemini-4-argon',
        'gemini-2.0-flash-lite' => 'gemini-3.1-flash-lite',
        'gemini-1.5-flash' => 'gemini-3.1-flash-lite',
        'gemini-1.5-pro' => 'gemini-4-argon',
        // Legacy ChatGPT mappings
        'gpt-4o' => 'gemini-4-argon',
        'gpt-4o-mini' => 'gemini-3.1-flash-lite',
        'o3-mini' => 'gemini-4-argon',
        'gpt-3.5-turbo' => 'gemini-3.1-flash-lite',
    ];

    /**
     * Fallback model when an advanced model experiences temporary demand (503/429).
     */
    protected string $reliableFallbackModel = 'gemini-3.1-flash-lite';

    /**
     * Send messages to Google Gemini API and retrieve the reply as a complete string.
     *
     * @param array<int, array{role: string, content: string|array<int, mixed>}> $messages
     * @param string|null $model
     * @param float $temperature
     * @return string
     */
    public function ask(array $messages, ?string $model = null, float $temperature = 0.7): string
    {
        // 1. Detect if the latest user prompt is requesting an AI image
        $lastUserMsg = $this->extractLastUserText($messages);
        if ($imageSubject = $this->extractImagePrompt($lastUserMsg)) {
            return $this->generateImageResponse($imageSubject);
        }

        $apiKey = $this->resolveApiKey();
        $targetModel = $this->normalizeModel($model);

        if ($this->isPlaceholderKey($apiKey)) {
            Log::info('Gemini request served via graceful fallback (no API key configured).');
            return $this->getGracefulFallbackReply($messages);
        }

        $url = (string) config('services.ai.url', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions');

        try {
            $response = $this->executePost($url, $apiKey, $targetModel, $messages, $temperature, false);

            // If primary model failed due to high demand (503/429), try reliable fallback model
            if ($response->failed() && in_array($response->status(), [429, 503, 404]) && $targetModel !== $this->reliableFallbackModel) {
                Log::warning("Gemini {$targetModel} returned {$response->status()}, falling back to {$this->reliableFallbackModel}");
                $response = $this->executePost($url, $apiKey, $this->reliableFallbackModel, $messages, $temperature, false);
            }

            if ($response->failed()) {
                Log::error('Gemini API call failed', [
                    'status' => $response->status(),
                    'error' => $response->json('error.message') ?? $response->body(),
                    'model' => $targetModel,
                ]);

                return $this->getUserFriendlyErrorMessage();
            }

            $content = (string) $response->json('choices.0.message.content', '');
            if (empty(trim($content))) {
                return $this->getUserFriendlyErrorMessage();
            }

            return $content;
        } catch (Throwable $e) {
            Log::error('Gemini API Exception', [
                'message' => $e->getMessage(),
                'model' => $targetModel,
            ]);

            return $this->getUserFriendlyErrorMessage();
        }
    }

    /**
     * Stream completion response chunks in real-time from Google Gemini API.
     *
     * @param array<int, array{role: string, content: string|array<int, mixed>}> $messages
     * @param string|null $model
     * @param float $temperature
     * @return Generator<int, string, mixed, void>
     */
    public function stream(array $messages, ?string $model = null, float $temperature = 0.7): Generator
    {
        // 1. Detect if the latest user prompt is requesting an AI image
        $lastUserMsg = $this->extractLastUserText($messages);
        if ($imageSubject = $this->extractImagePrompt($lastUserMsg)) {
            yield $this->generateImageResponse($imageSubject);
            return;
        }

        $apiKey = $this->resolveApiKey();
        $targetModel = $this->normalizeModel($model);

        if ($this->isPlaceholderKey($apiKey)) {
            Log::info('Gemini streaming request served via graceful fallback (no API key configured).');
            yield $this->getGracefulFallbackReply($messages);
            return;
        }

        $url = (string) config('services.ai.url', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions');

        try {
            $response = $this->executePost($url, $apiKey, $targetModel, $messages, $temperature, true);

            // Fallback to high-availability model if primary is experiencing capacity limits
            if ($response->failed() && in_array($response->status(), [429, 503, 404]) && $targetModel !== $this->reliableFallbackModel) {
                Log::warning("Gemini stream {$targetModel} returned {$response->status()}, retrying with {$this->reliableFallbackModel}");
                $response = $this->executePost($url, $apiKey, $this->reliableFallbackModel, $messages, $temperature, true);
            }

            if ($response->failed()) {
                Log::error('Gemini Streaming API failed', [
                    'status' => $response->status(),
                    'error' => $response->json('error.message') ?? $response->body(),
                    'model' => $targetModel,
                ]);

                yield $this->getUserFriendlyErrorMessage();
                return;
            }

            $body = $response->toPsrResponse()->getBody();
            $buffer = '';
            $yieldedAny = false;

            while (! $body->eof()) {
                $chunk = $body->read(1024);
                $buffer .= $chunk;

                while (($newlinePos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $newlinePos));
                    $buffer = substr($buffer, $newlinePos + 1);

                    if (empty($line) || ! str_starts_with($line, 'data:')) {
                        continue;
                    }

                    $jsonStr = trim(substr($line, 5));
                    if ($jsonStr === '[DONE]') {
                        break 2;
                    }

                    $decoded = json_decode($jsonStr, true);
                    if (isset($decoded['choices'][0]['delta']['content'])) {
                        $delta = (string) $decoded['choices'][0]['delta']['content'];
                        if ($delta !== '') {
                            $yieldedAny = true;
                            yield $delta;
                        }
                    }
                }
            }

            if (! $yieldedAny) {
                yield $this->getUserFriendlyErrorMessage();
            }
        } catch (Throwable $e) {
            Log::error('Gemini Streaming Exception', [
                'message' => $e->getMessage(),
                'model' => $targetModel,
            ]);

            yield $this->getUserFriendlyErrorMessage();
        }
    }

    /**
     * Detect if a user message is requesting image generation, and return the extracted subject.
     */
    public function extractImagePrompt(string $text): ?string
    {
        $clean = trim($text);

        if (empty($clean)) {
            return null;
        }

        // Avoid false positives for pure informational questions
        if (preg_match('/^(what|why|who|when|where|how|explain|describe)\b/i', $clean) && ! preg_match('/\b(generate|create|draw|make|banao)\b/i', $clean)) {
            return null;
        }

        $patterns = [
            // "spider ki image bana kr do", "sher ki photo banao"
            '/^(.*?)\s+(?:ki|ka|ke)?\s*(?:image|tasveer|photo|pic|picture)\s*(?:bana|generate|dikhao|karo|banao|bana do|bana kr do|arkoo|karoo)?$/iu',

            // "spider image generate arkoo", "gnerati9ng spider image", "hulk image genereate karoo"
            '/^(?:gnerati9ng|generating|generate|create|make|draw|banao|dikhao)?\s*(.*?)\s*(?:image|picture|photo|pic|tasveer)\s*(?:generate|banao|karoo|karo|bana do|bana kr do|arkoo)?$/iu',

            // "generate an image of spider", "draw a futuristic car"
            '/^(?:please\s+)?(?:generate|create|draw|make|paint|show me|banao|tasveer banao)\s+(?:an?\s+)?(?:image|picture|photo|illustration|art|tasveer)?\s*(?:of|about|for)?\s*(.+)$/iu',

            // "image of a spider", "pic of hulk"
            '/^(?:image|picture|photo|pic|tasveer)\s+(?:of|about|for|ki|ka)?\s*(.+)$/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $clean, $matches)) {
                $extracted = trim($matches[1] ?? ($matches[2] ?? ''));
                $extracted = preg_replace('/\b(generate|genereate|generating|gnerati9ng|banao|karo|karoo|arkoo|ki|ka|ko|bana|kr|do|please|an?|the|image|tasveer|photo|pic|picture)\b/iu', '', $extracted);
                $extracted = trim(preg_replace('/\s+/', ' ', $extracted));
                if (! empty($extracted) && strlen($extracted) >= 2) {
                    return $extracted;
                }
            }
        }

        if (preg_match('/\b(image|tasveer|photo|picture)\b/iu', $clean)) {
            $extracted = trim(preg_replace('/\b(generate|genereate|generating|gnerati9ng|banao|karo|karoo|arkoo|ki|ka|ko|bana|kr|do|please|an?|the|image|tasveer|photo|pic|picture)\b/iu', '', $clean));
            $extracted = trim(preg_replace('/\s+/', ' ', $extracted));
            if (! empty($extracted) && strlen($extracted) >= 2) {
                return $extracted;
            }
        }

        return null;
    }

    /**
     * Build an instantaneous, high-resolution AI generated image response.
     */
    public function generateImageResponse(string $subject): string
    {
        $cleanSubject = trim($subject);
        $title = ucwords($cleanSubject);
        $seed = rand(1000, 9999);

        $enhancedPrompt = "a high quality detailed photorealistic {$cleanSubject}, cinematic lighting, dramatic composition, vivid colors, 8k resolution, masterpiece";
        $encoded = urlencode($enhancedPrompt);

        return "Here is your generated image of **{$title}**:\n\n"
            . "![{$title}](https://image.pollinations.ai/prompt/{$encoded}?width=1024&height=1024&nologo=true&seed={$seed})\n\n"
            . "*Prompt:* \"{$enhancedPrompt}\"\n\n"
            . "Agar aap is tasveer mein koi specific style (jaise 3D animation, anime, ya neon cyberpunk) chahte hain, to batayein!";
    }

    /**
     * Extract the text content of the last user turn from an array of messages.
     *
     * @param array<int, array{role: string, content: mixed}> $messages
     */
    protected function extractLastUserText(array $messages): string
    {
        foreach (array_reverse($messages) as $m) {
            if (($m['role'] ?? '') === 'user') {
                if (is_string($m['content'] ?? null)) {
                    return (string) $m['content'];
                } elseif (is_array($m['content'] ?? null)) {
                    $accum = '';
                    foreach ($m['content'] as $part) {
                        if (($part['type'] ?? '') === 'text') {
                            $accum .= ' ' . ($part['text'] ?? '');
                        }
                    }
                    return trim($accum);
                }
            }
        }

        return '';
    }

    /**
     * Execute an HTTP POST request to the Gemini OpenAI-compatible completions endpoint.
     *
     * @param string $url
     * @param string $apiKey
     * @param string $model
     * @param array<int, array{role: string, content: mixed}> $messages
     * @param float $temperature
     * @param bool $stream
     * @return \Illuminate\Http\Client\Response
     */
    protected function executePost(string $url, string $apiKey, string $model, array $messages, float $temperature, bool $stream): \Illuminate\Http\Client\Response
    {
        $client = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'x-goog-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout($stream ? 120 : 60);

        if ($stream) {
            $client = $client->withOptions(['stream' => true]);
        }

        return $client->post($url, [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
            'stream' => $stream,
        ]);
    }

    /**
     * Resolve the active API key from Gemini or AI service config.
     */
    protected function resolveApiKey(): string
    {
        return trim((string) (
            config('services.gemini.key')
            ?: config('services.ai.key')
            ?: env('GEMINI_API_KEY')
            ?: env('AI_API_KEY')
            ?: ''
        ));
    }

    /**
     * Normalize model string to ensure an active Gemini model is targeted.
     */
    public function normalizeModel(?string $model): string
    {
        if (empty($model)) {
            $model = (string) config('services.ai.model', 'gemini-3.1-flash-lite');
        }

        return $this->modelMap[$model] ?? 'gemini-3.1-flash-lite';
    }

    /**
     * Check if the API key is missing, empty, or set to a placeholder.
     */
    protected function isPlaceholderKey(string $apiKey): bool
    {
        return empty($apiKey)
            || $apiKey === 'your-api-key-here'
            || $apiKey === 'your-gemini-api-key-here'
            || str_starts_with($apiKey, 'your-')
            || str_starts_with($apiKey, 'sk-proj-');
    }

    /**
     * Return a clean, professional, courteous message for user-facing errors.
     */
    protected function getUserFriendlyErrorMessage(): string
    {
        return "I apologize, but I am currently experiencing a temporary connection issue. Please try again in a few moments, or feel free to rephrase your request.";
    }

    /**
     * Provide a smart, courteous local fallback reply when the remote API is unavailable.
     *
     * @param array<int, array{role: string, content: mixed}> $messages
     */
    protected function getGracefulFallbackReply(array $messages): string
    {
        $lastUserMsg = $this->extractLastUserText($messages);
        $trimmed = strtolower(trim($lastUserMsg));

        if (preg_match('/\b(hi|hello|hey|salam|assalam|aao|kese ho|kaise ho)\b/i', $trimmed)) {
            return "Hello! I am **Nexus AI**, your intelligent assistant powered by **Google Gemini**. How can I help you today?";
        }

        if (preg_match('/\b(who are you|kon ho|tum kon ho|what is this|kya ho)\b/i', $trimmed)) {
            return "I am **Nexus AI**, an advanced multimodal AI assistant powered by **Google Gemini**. I can assist with writing, coding, analyzing documents, discussing ideas, generating AI images, and answering questions!";
        }

        if ($imageSubject = $this->extractImagePrompt($lastUserMsg)) {
            return $this->generateImageResponse($imageSubject);
        }

        return "I am currently processing requests through Nexus AI (Google Gemini). I am temporarily experiencing a connection delay right now — please try again in just a moment!";
    }
}
