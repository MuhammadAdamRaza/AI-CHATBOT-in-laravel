<?php

namespace App\Services;

use Generator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatbotService
{
    /**
     * Map friendly or legacy model names to active Google Gemini & OpenRouter models.
     *
     * @var array<string, string>
     */
    protected array $modelMap = [
        // Google Gemini Models (Native Google API)
        'gemini-3.1-flash-lite' => 'gemini-3.1-flash-lite',
        'gemini-3.5-flash-lite' => 'gemini-3.5-flash-lite',
        'gemini-3.8-flash' => 'gemini-3.8-flash',
        'gemini-4-argon' => 'gemini-4-argon',

        // ChatGPT Models (OpenAI via OpenRouter)
        'gpt-4o-mini' => 'openai/gpt-4o-mini',
        'gpt-4o' => 'openai/gpt-4o-mini',
        'chatgpt' => 'openai/gpt-4o-mini',

        // DeepSeek AI Models (via OpenRouter)
        'deepseek-chat' => 'deepseek/deepseek-chat',
        'deepseek-reasoner' => 'deepseek/deepseek-r1-0528',
        'deepseek-v3' => 'deepseek/deepseek-chat',
        'deepseek-r1' => 'deepseek/deepseek-r1-0528',
        'deepseek-coder' => 'deepseek/deepseek-chat',

        // Anthropic Claude Models (via OpenRouter)
        'claude-sonnet-5' => 'anthropic/claude-sonnet-5',
        'claude-3.5-sonnet' => 'anthropic/claude-sonnet-5',
        'claude-haiku-5.5' => 'anthropic/claude-haiku-5.5',
        'claude-haiku' => 'anthropic/claude-haiku-5.5',
        'claude' => 'anthropic/claude-sonnet-5',

        // xAI Grok Models (via OpenRouter)
        'grok-4.5' => 'x-ai/grok-4.5',
        'grok-2' => 'x-ai/grok-4.5',
        'grok' => 'x-ai/grok-4.5',

        // Alibaba Qwen Coder (Specialized Code Intelligence on OpenRouter)
        'qwen-coder' => 'qwen/qwen-2.5-coder-32b-instruct',
        'qwen-2.5-coder' => 'qwen/qwen-2.5-coder-32b-instruct',
        'qwen' => 'qwen/qwen-2.5-coder-32b-instruct',

        // Meta Llama Models (via OpenRouter)
        'llama-3.3-70b' => 'meta-llama/llama-3.3-70b-instruct',
        'llama-3.3' => 'meta-llama/llama-3.3-70b-instruct',
        'llama' => 'meta-llama/llama-3.3-70b-instruct',

        // Real-Time Online Web Search (via OpenRouter Online suffix)
        'web-search' => 'openai/gpt-4o-mini:online',
        'online-search' => 'openai/gpt-4o-mini:online',

        // Dynamic Smart Router
        'auto' => 'auto',

        // Legacy aliases
        'gemini-2.0-flash' => 'gemini-3.1-flash-lite',
        'gemini-2.5-flash' => 'gemini-3.5-flash-lite',
        'gemini-2.5-pro' => 'gemini-4-argon',
        'gemini-2.0-flash-thinking-exp' => 'gemini-4-argon',
        'gemini-2.0-flash-lite' => 'gemini-3.1-flash-lite',
        'o3-mini' => 'deepseek/deepseek-r1-0528',
        'gpt-3.5-turbo' => 'openai/gpt-4o-mini',
    ];

    /**
     * Fallback model when an advanced model experiences temporary demand (503/429).
     */
    protected string $reliableFallbackModel = 'gemini-3.1-flash-lite';

    /**
     * Send messages to AI provider and retrieve reply as a complete string.
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

        $targetModelKey = (empty($model) || $model === 'auto') ? 'auto' : $model;
        if ($targetModelKey === 'auto') {
            $decision = $this->detectOptimalModel($lastUserMsg);
            $targetModel = $this->normalizeModel($decision['model']);
        } else {
            $targetModel = $this->normalizeModel($model);
        }

        $apiKey = $this->resolveApiKey($targetModel);

        if ($this->isPlaceholderKey($apiKey)) {
            Log::info('Request served via graceful fallback (no API key configured).');
            return $this->getGracefulFallbackReply($messages);
        }

        $url = $this->resolveUrl($targetModel);

        try {
            $response = $this->executePost($url, $apiKey, $targetModel, $messages, $temperature, false);

            // If primary model failed due to high demand or balance, try reliable fallback model (Google Gemini)
            if ($response->failed() && $targetModel !== $this->reliableFallbackModel) {
                Log::warning("AI {$targetModel} returned {$response->status()}, falling back to {$this->reliableFallbackModel}");
                $response = $this->executePost(
                    $this->resolveUrl($this->reliableFallbackModel),
                    $this->resolveApiKey($this->reliableFallbackModel),
                    $this->reliableFallbackModel,
                    $messages,
                    $temperature,
                    false
                );
                $targetModel = $this->reliableFallbackModel;
            }

            if ($response->failed()) {
                Log::error('AI API call failed', [
                    'status' => $response->status(),
                    'error' => $response->json('error.message') ?? $response->body(),
                    'model' => $targetModel,
                ]);

                return $this->getUserFriendlyErrorMessage();
            }

            // Support DeepSeek R1 reasoning content
            $reasoning = (string) ($response->json('choices.0.message.reasoning') ?? $response->json('choices.0.message.reasoning_content') ?? '');
            $content = (string) $response->json('choices.0.message.content', '');

            if (! empty(trim($reasoning))) {
                return "> 💭 **Deep Thinking Process:**\n> " . str_replace("\n", "\n> ", trim($reasoning)) . "\n\n---\n\n" . $content;
            }

            if (empty(trim($content))) {
                return $this->getUserFriendlyErrorMessage();
            }

            return $content;
        } catch (Throwable $e) {
            Log::error('AI API Exception', [
                'message' => $e->getMessage(),
                'model' => $targetModel,
            ]);

            return $this->getUserFriendlyErrorMessage();
        }
    }

    /**
     * Stream completion response chunks in real-time.
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

        $targetModelKey = (empty($model) || $model === 'auto') ? 'auto' : $model;
        if ($targetModelKey === 'auto') {
            $decision = $this->detectOptimalModel($lastUserMsg);
            $targetModel = $this->normalizeModel($decision['model']);
        } else {
            $targetModel = $this->normalizeModel($model);
        }

        $apiKey = $this->resolveApiKey($targetModel);

        if ($this->isPlaceholderKey($apiKey)) {
            Log::info('AI streaming request served via graceful fallback (no API key configured).');
            yield $this->getGracefulFallbackReply($messages);
            return;
        }

        $url = $this->resolveUrl($targetModel);

        try {
            $response = $this->executePost($url, $apiKey, $targetModel, $messages, $temperature, true);

            // Fallback to high-availability model if primary experiences capacity limits
            if ($response->failed() && $targetModel !== $this->reliableFallbackModel) {
                Log::warning("AI stream {$targetModel} returned {$response->status()}, retrying with {$this->reliableFallbackModel}");
                $response = $this->executePost(
                    $this->resolveUrl($this->reliableFallbackModel),
                    $this->resolveApiKey($this->reliableFallbackModel),
                    $this->reliableFallbackModel,
                    $messages,
                    $temperature,
                    true
                );
                $targetModel = $this->reliableFallbackModel;
            }

            if ($response->failed()) {
                Log::error('AI Streaming API failed', [
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
            $inReasoning = false;

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
                    if (! is_array($decoded)) {
                        continue;
                    }

                    // Check for reasoning tokens (DeepSeek-R1 or OpenRouter reasoning)
                    $deltaReasoning = $decoded['choices'][0]['delta']['reasoning'] ?? ($decoded['choices'][0]['delta']['reasoning_content'] ?? null);
                    if (! empty($deltaReasoning)) {
                        if (! $inReasoning) {
                            $inReasoning = true;
                            $yieldedAny = true;
                            yield "> 💭 **Thinking Process:**\n> ";
                        }
                        $yieldedAny = true;
                        yield str_replace("\n", "\n> ", $deltaReasoning);
                    }

                    // Check for standard content tokens
                    $deltaContent = $decoded['choices'][0]['delta']['content'] ?? null;
                    if ($deltaContent !== null && $deltaContent !== '') {
                        if ($inReasoning) {
                            $inReasoning = false;
                            yield "\n\n---\n\n";
                        }
                        $yieldedAny = true;
                        yield $deltaContent;
                    }
                }
            }

            if (! $yieldedAny) {
                yield $this->getUserFriendlyErrorMessage();
            }
        } catch (Throwable $e) {
            Log::error('AI Streaming Exception', [
                'message' => $e->getMessage(),
                'model' => $targetModel,
            ]);

            yield $this->getUserFriendlyErrorMessage();
        }
    }

    /**
     * Check if a model is served via OpenRouter.
     */
    public function isOpenRouter(string $model): bool
    {
        return ! str_starts_with($model, 'gemini-');
    }

    /**
     * Check if a model is DeepSeek.
     */
    public function isDeepSeek(string $model): bool
    {
        return str_starts_with($model, 'deepseek');
    }

    /**
     * Execute an HTTP POST request to OpenAI-compatible completions endpoint.
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
        $isOpenRouter = $this->isOpenRouter($model);

        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ];

        if ($isOpenRouter) {
            $headers['HTTP-Referer'] = (string) config('app.url', 'http://localhost:8000');
            $headers['X-Title'] = 'Nexus AI Studio';
        } else {
            $headers['x-goog-api-key'] = $apiKey;
        }

        $sanitizedMessages = $this->sanitizeMessagesForModel($messages, $model);

        $client = Http::withHeaders($headers)->timeout($stream ? 120 : 60);

        if ($stream) {
            $client = $client->withOptions(['stream' => true]);
        }

        $payload = [
            'model' => $model,
            'messages' => $sanitizedMessages,
            'max_tokens' => 4096,
            'stream' => $stream,
        ];

        if (! str_contains($model, 'r1')) {
            $payload['temperature'] = $temperature;
        }

        return $client->post($url, $payload);
    }

    /**
     * Sanitize messages according to provider capabilities.
     *
     * @param array<int, array{role: string, content: mixed}> $messages
     * @param string $model
     * @return array<int, array{role: string, content: mixed}>
     */
    protected function sanitizeMessagesForModel(array $messages, string $model): array
    {
        if ($this->isOpenRouter($model)) {
            $cleaned = [];
            foreach ($messages as $m) {
                $role = $m['role'] ?? 'user';
                $content = $m['content'] ?? '';

                if ($role === 'assistant') {
                    $strContent = is_array($content) ? json_encode($content) : (string) $content;
                    if (str_contains($strContent, 'placeholder characters') ||
                        str_contains($strContent, 'placeholder or test input') ||
                        str_contains($strContent, 'temporary connection issue') ||
                        str_contains($strContent, 'DeepSeek Notice (402')) {
                        continue;
                    }
                }

                if (is_array($content)) {
                    $textParts = [];
                    foreach ($content as $part) {
                        if (($part['type'] ?? '') === 'text') {
                            $textParts[] = $part['text'] ?? '';
                        } elseif (($part['type'] ?? '') === 'image_url') {
                            $textParts[] = '[Attached Image - Visual analysis is best supported using Google Gemini models]';
                        }
                    }
                    $cleaned[] = ['role' => $role, 'content' => implode("\n", $textParts)];
                } else {
                    $cleaned[] = ['role' => $role, 'content' => (string) $content];
                }
            }
            return $cleaned;
        }

        return $messages;
    }

    /**
     * Resolve the active API key for the requested model/provider.
     */
    public function resolveApiKey(string $model): string
    {
        if ($this->isOpenRouter($model)) {
            return trim((string) (
                config('services.openrouter.key')
                ?: env('OPENROUTER_API_KEY')
                ?: config('services.deepseek.key')
                ?: env('DEEPSEEK_API_KEY')
                ?: ''
            ));
        }

        return trim((string) (
            config('services.gemini.key')
            ?: config('services.ai.key')
            ?: env('GEMINI_API_KEY')
            ?: env('AI_API_KEY')
            ?: ''
        ));
    }

    /**
     * Resolve the API endpoint URL for the requested model/provider.
     */
    public function resolveUrl(string $model): string
    {
        if ($this->isOpenRouter($model)) {
            return (string) config('services.openrouter.url', 'https://openrouter.ai/api/v1/chat/completions');
        }

        return (string) config('services.gemini.url', config('services.ai.url', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions'));
    }

    /**
     * Normalize model string to ensure an active Gemini or OpenRouter model is targeted.
     */
    public function normalizeModel(?string $model): string
    {
        if (empty($model)) {
            $model = (string) config('services.ai.model', 'auto');
        }

        if ($model === 'auto') {
            return 'auto';
        }

        return $this->modelMap[$model] ?? 'gemini-3.1-flash-lite';
    }

    /**
     * Dynamically detect the optimal AI engine based on user requirements and input context.
     *
     * @param string $prompt
     * @param array<int, mixed> $attachments
     * @return array{model: string, provider: string, reason: string, badge: string}
     */
    public function detectOptimalModel(string $prompt, array $attachments = []): array
    {
        $text = strtolower(trim($prompt));

        // 1. Multimodal Vision or Document Uploads -> Google Gemini
        if (! empty($attachments)) {
            return [
                'model' => 'gemini-3.1-flash-lite',
                'provider' => 'Google Gemini',
                'reason' => 'Multimodal Vision & Document OCR',
                'badge' => 'Gemini Vision',
            ];
        }

        // 2. Image Generation Prompts -> FLUX / Pollinations
        if ($this->extractImagePrompt($prompt) !== null) {
            return [
                'model' => 'gemini-3.1-flash-lite',
                'provider' => 'FLUX AI',
                'reason' => 'Photorealistic Image Generation',
                'badge' => 'FLUX 1024px',
            ];
        }

        // 3. Web Frameworks, Full-Stack Architecture, Laravel, Blade, Vue/React, CSS/Tailwind, PHP -> Claude 3.5 Sonnet
        $architectureKeywords = [
            'laravel', 'vue', 'react', 'tailwind', 'blade', 'controller', 'middleware', 'eloquent',
            'migration', 'routing', 'full stack', 'fullstack', 'full-stack', 'architecture', 'refactor',
            'frontend', 'front end', 'front-end', 'backend', 'baceknd', 'back end', 'back-end',
            'bootstrap', 'component', 'html/css', 'html', 'css', 'rest api', 'jwt', 'livewire', 'inertia',
            'design pattern', 'clean code', 'solid principle', 'repository pattern', 'crud',
            'php', 'ecommerce', 'ecoomerce', 'eccommerce', 'ecom', 'landing page', 'landing', 'website',
            'web app', 'web application', 'ui design', 'dashboard', 'database design', 'mvc',
        ];
        foreach ($architectureKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return [
                    'model' => 'claude-sonnet-5',
                    'provider' => 'Anthropic Claude',
                    'reason' => 'Full-Stack Architecture & Web Frameworks (' . ucfirst($kw) . ')',
                    'badge' => 'Claude 3.5 Sonnet',
                ];
            }
        }

        // 4. Deep Mathematical Proofs, Algorithms, Complexity, Logic Puzzles -> DeepSeek R1 (Reasoning)
        $reasoningKeywords = [
            'algorithm', 'dijkstra', 'dynamic programming', 'graph theory', 'big o', 'complexity',
            'prove that', 'mathematical proof', 'math proof', 'derivation', 'integral', 'differential equation',
            'calculus', 'puzzle', 'step by step logic', 'think step by step', 'logic deduction', 'tree traversal',
            'recursion depth', 'binary search tree', 'knapsack', 'np-complete', 'combinatorics',
        ];
        foreach ($reasoningKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return [
                    'model' => 'deepseek-reasoner',
                    'provider' => 'DeepSeek AI',
                    'reason' => 'Deep Step-by-Step Reasoning & Algorithms (' . ucfirst($kw) . ')',
                    'badge' => 'DeepSeek R1',
                ];
            }
        }

        // 5. Backend Code, Bug Fixing, Python, SQL, C++, Java, Rust, Go, Debugging -> DeepSeek V3
        $backendCodeKeywords = [
            'code', 'coding', 'program', 'programming', 'script', 'scripting', 'software',
            'function', 'def ', 'class ', 'syntax error', 'stack trace', 'exception', 'bug', 'fix bug',
            'debug', 'sql query', 'mysql', 'postgres', 'sqlite', 'database', 'sql', 'query',
            'python', 'java', 'c++', 'cpp', 'c#', 'csharp', 'golang', 'rust', 'docker', 'bash script',
            'powershell', 'regex', 'unit test', 'pytest', 'phpunit', 'git commit', 'git rebase',
        ];
        foreach ($backendCodeKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return [
                    'model' => 'deepseek-chat',
                    'provider' => 'DeepSeek AI',
                    'reason' => 'Code Optimization & Debugging (' . ucfirst($kw) . ')',
                    'badge' => 'DeepSeek V3',
                ];
            }
        }

        // 6. Superheroes, Pop Culture, Characters, Cinema, Movies -> Claude 3.5 Sonnet
        $popCultureKeywords = [
            'hulk', 'spiderman', 'spider-man', 'batman', 'superman', 'iron man', 'ironman',
            'thor', 'captain america', 'avengers', 'marvel', 'dc comics', 'superhero', 'anime',
            'movie', 'movies', 'mobvies', 'film', 'cinema', 'hollywood', 'series', 'character', 'hero',
        ];
        foreach ($popCultureKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return [
                    'model' => 'claude-sonnet-5',
                    'provider' => 'Anthropic Claude',
                    'reason' => 'Character & Pop Culture Specialist (' . ucfirst($kw) . ')',
                    'badge' => 'Claude 3.5 Sonnet',
                ];
            }
        }

        // 7. Live Web Search & Real-Time Facts -> Web Search AI (:online)
        $webSearchKeywords = [
            'search the web', 'browse web', 'google search', 'live price', 'gold price', 'silver price',
            'stock market today', 'weather today', 'score today', 'match today', 'election results',
            'who won today', 'aaj ki khabar', 'live score', 'currency rate today', 'dollar rate today',
        ];
        foreach ($webSearchKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return [
                    'model' => 'web-search',
                    'provider' => 'Live Web AI',
                    'reason' => 'Real-Time Web Search & Live Grounding (' . ucfirst($kw) . ')',
                    'badge' => 'Live Web Search',
                ];
            }
        }

        // 8. Real-time Trends, News, Unfiltered Discussion, Latest World Events -> xAI Grok 2
        $grokKeywords = [
            'latest news', 'today', 'current trend', 'trending', 'trend', 'grok', 'elon musk', 'twitter/x',
            'twitter', 'x.com', 'crypto price', 'bitcoin update', 'bitcoin', 'crypto', 'current events',
            'breaking news', 'what happened in', 'unfiltered', 'humor', 'roast', 'sarcasm',
        ];
        foreach ($grokKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return [
                    'model' => 'grok-4.5',
                    'provider' => 'xAI Grok',
                    'reason' => 'Real-Time Insights & Current Events',
                    'badge' => 'Grok 2',
                ];
            }
        }

        // 8. Long-form Writing, Professional Emails, Creative Copywriting, Translation -> Claude 3.5 Haiku / Sonnet
        $creativeKeywords = [
            'write an email', 'write an essay', 'cover letter', 'resume', 'cv', 'copywriting',
            'blog post', 'poem', 'poetry', 'story', 'speech', 'press release', 'proofread',
            'paraphrase', 'rewrite professionally', 'formal letter', 'translate', 'pdf', 'document',
        ];
        foreach ($creativeKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return [
                    'model' => 'claude-sonnet-5',
                    'provider' => 'Anthropic Claude',
                    'reason' => 'Creative Writing & Document Drafting',
                    'badge' => 'Claude 3.5',
                ];
            }
        }

        // 9. General Q&A, Rapid Chit-Chat, Quick Facts -> ChatGPT 4o-mini
        return [
            'model' => 'gpt-4o-mini',
            'provider' => 'OpenAI ChatGPT',
            'reason' => 'Fast Everyday Intelligence & Direct Answers',
            'badge' => 'ChatGPT 4o-mini',
        ];
    }

    /**
     * Check if the API key is missing, empty, or set to a placeholder.
     */
    public function isPlaceholderKey(string $apiKey): bool
    {
        return empty($apiKey)
            || $apiKey === 'your-api-key-here'
            || $apiKey === 'your-gemini-api-key-here'
            || $apiKey === 'your-deepseek-api-key-here'
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
            return "Hello! I am **Nexus AI**, your multi-engine assistant powered by **Gemini**, **ChatGPT**, **DeepSeek**, **Claude**, and **Grok**. How can I help you today?";
        }

        if (preg_match('/\b(who are you|kon ho|tum kon ho|what is this|kya ho)\b/i', $trimmed)) {
            return "I am **Nexus AI**, an enterprise multi-engine AI platform integrating **Google Gemini**, **OpenAI ChatGPT**, **DeepSeek**, **Anthropic Claude**, and **xAI Grok**.";
        }

        if ($imageSubject = $this->extractImagePrompt($lastUserMsg)) {
            return $this->generateImageResponse($imageSubject);
        }

        return "I am currently processing requests through Nexus AI. I am temporarily experiencing a connection delay right now — please try again in just a moment!";
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
            '/^(.*?)\s+(?:ki|ka|ke)?\s*(?:image|tasveer|photo|pic|picture)\s*(?:bana|generate|dikhao|karo|banao|bana do|bana kr do|arkoo|karoo)?$/iu',
            '/^(?:gnerati9ng|generating|generate|create|make|draw|banao|dikhao)?\s*(.*?)\s*(?:image|picture|photo|pic|tasveer)\s*(?:generate|banao|karoo|karo|bana do|bana kr do|arkoo)?$/iu',
            '/^(?:please\s+)?(?:generate|create|draw|make|paint|show me|banao|tasveer banao)\s+(?:an?\s+)?(?:image|picture|photo|illustration|art|tasveer)?\s*(?:of|about|for)?\s*(.+)$/iu',
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
}
