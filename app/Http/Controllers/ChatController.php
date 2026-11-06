<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    /**
     * Preset system personas.
     *
     * @var array<string, array{name: string, description: string, prompt: string, icon: string}>
     */
    protected array $personas = [
        'default' => [
            'name' => 'General Assistant',
            'description' => 'Helpful, thoughtful, and well-rounded assistant with vision & image creation.',
            'prompt' => 'You are Nexus AI, an elite, highly intelligent AI assistant powered by Google Gemini, OpenAI ChatGPT, Anthropic Claude, DeepSeek, and xAI Grok. Deliver articulate, comprehensive, beautifully structured Markdown responses. Always answer the user\'s current prompt directly, accurately, and thoroughly. If the user writes in English, Urdu, or Roman Urdu, respond naturally in their language. If asked to draw or create an image, vividly describe it and embed: ![Art](https://image.pollinations.ai/prompt/{url_encoded_prompt}?width=1024&height=1024&nologo=true&seed={random_number}).',
            'icon' => 'sparkles',
        ],
        'artist' => [
            'name' => 'AI Image & Concept Artist',
            'description' => 'Generates photorealistic images, digital art & illustrations.',
            'prompt' => 'You are an elite AI digital artist and concept designer. When the user describes a visual or asks for an image, refine their idea into a breathtaking, highly detailed visual prompt and render it using: ![Art](https://image.pollinations.ai/prompt/{url_encoded_prompt}?width=1024&height=1024&nologo=true&seed={random_number}) and describe the artistic style, lighting, and composition.',
            'icon' => 'image',
        ],
        'coder' => [
            'name' => 'Senior Code Architect',
            'description' => 'Clean code, architecture patterns, and debugging.',
            'prompt' => 'You are an elite principal software engineer and architect powered by Claude, DeepSeek, and Gemini. Provide clean, well-commented, secure code in markdown format with language identifiers and performance insights.',
            'icon' => 'code',
        ],
        'writer' => [
            'name' => 'Copywriter & Editor',
            'description' => 'Creative writing, emails, and content polish.',
            'prompt' => 'You are a master creative writer and professional editor. Craft compelling, articulate prose, catchy headlines, and crisp emails in beautiful Markdown.',
            'icon' => 'pen-tool',
        ],
        'analyst' => [
            'name' => 'Business Analyst',
            'description' => 'Data breakdowns, SWOT, strategy, and logic.',
            'prompt' => 'You are a senior business intelligence and strategy analyst. Structure your insights with concise bullet points, pros/cons, and actionable next steps.',
            'icon' => 'bar-chart-2',
        ],
        'concise' => [
            'name' => 'Direct & Concise',
            'description' => 'Bullet-point answers with zero filler.',
            'prompt' => 'You are a direct, concise AI assistant. Answer immediately with facts and bullets without introductory greetings or conversational fluff.',
            'icon' => 'zap',
        ],
    ];

    /**
     * AI Models Catalog (Google Gemini & DeepSeek AI) with capability metadata.
     *
     * @var array<string, array{name: string, provider: string, provider_badge: string, badge: string, badge_color: string, desc: string, speed: string, intelligence: string}>
     */
    protected array $models = [
        // Smart Auto-Pilot Router
        'auto' => [
            'name' => 'Auto (Smart Engine)',
            'provider' => 'Smart Router',
            'provider_badge' => 'Auto-Pilot',
            'badge' => 'AI Selects Best',
            'badge_color' => 'purple',
            'desc' => 'Dynamically analyzes user requirement & routes to Claude, DeepSeek, Grok, Gemini, or ChatGPT.',
            'speed' => 'Adaptive',
            'intelligence' => 'Maximum',
        ],

        // Google Gemini Models
        'gemini-3.1-flash-lite' => [
            'name' => 'Gemini 3.1 Flash',
            'provider' => 'Google Gemini',
            'provider_badge' => 'Google',
            'badge' => 'Default & Fast',
            'badge_color' => 'blue',
            'desc' => "Ultra-fast multimodal execution model for immediate answers, code & analysis.",
            'speed' => 'Fastest',
            'intelligence' => 'High',
        ],
        'gemini-3.5-flash-lite' => [
            'name' => 'Gemini 3.5 Flash',
            'provider' => 'Google Gemini',
            'provider_badge' => 'Google',
            'badge' => 'High Throughput',
            'badge_color' => 'indigo',
            'desc' => 'High-capacity multimodal model with 1M token context for large documents & files.',
            'speed' => 'Fast',
            'intelligence' => 'High',
        ],
        'gemini-4-argon' => [
            'name' => 'Gemini 4 Argon',
            'provider' => 'Google Gemini',
            'provider_badge' => 'Google',
            'badge' => 'Deep Reasoning',
            'badge_color' => 'purple',
            'desc' => 'Most powerful Google model for complex logic, math, research and architecture.',
            'speed' => 'Thoughtful',
            'intelligence' => 'Maximum',
        ],
        'gemini-3.8-flash' => [
            'name' => 'Gemini 3.8 Flash',
            'provider' => 'Google Gemini',
            'provider_badge' => 'Google',
            'badge' => 'Multimodal Pro',
            'badge_color' => 'emerald',
            'desc' => 'Balanced multimodal intelligence for complex vision, files and workflows.',
            'speed' => 'Balanced',
            'intelligence' => 'High',
        ],

        // DeepSeek AI Models
        'deepseek-chat' => [
            'name' => 'DeepSeek V3',
            'provider' => 'DeepSeek AI',
            'provider_badge' => 'DeepSeek',
            'badge' => 'MoE 671B',
            'badge_color' => 'sky',
            'desc' => 'DeepSeek-V3 671B Mixture-of-Experts for coding, conversation, and multilingual tasks.',
            'speed' => 'Ultra Fast',
            'intelligence' => 'Very High',
        ],
        'deepseek-reasoner' => [
            'name' => 'DeepSeek R1',
            'provider' => 'DeepSeek AI',
            'provider_badge' => 'DeepSeek',
            'badge' => 'Deep Thinking (R1)',
            'badge_color' => 'teal',
            'desc' => 'State-of-the-art open reasoning model featuring transparent chain-of-thought logic & math.',
            'speed' => 'Deliberate',
            'intelligence' => 'Maximum',
        ],

        // OpenAI ChatGPT Models (via OpenRouter)
        'gpt-4o-mini' => [
            'name' => 'ChatGPT 4o-mini',
            'provider' => 'OpenAI ChatGPT',
            'provider_badge' => 'OpenAI',
            'badge' => 'GPT-4o Mini',
            'badge_color' => 'emerald',
            'desc' => 'Fast, lightweight intelligence from OpenAI for daily tasks, code and reasoning.',
            'speed' => 'Fastest',
            'intelligence' => 'High',
        ],

        // Anthropic Claude Models (via OpenRouter)
        'claude-sonnet-5' => [
            'name' => 'Claude 3.5 Sonnet',
            'provider' => 'Anthropic Claude',
            'provider_badge' => 'Claude',
            'badge' => 'Sonnet 3.5',
            'badge_color' => 'amber',
            'desc' => 'Leading industry model for advanced coding, nuanced creative writing, and system analysis.',
            'speed' => 'Fast',
            'intelligence' => 'Maximum',
        ],
        'claude-haiku-5.5' => [
            'name' => 'Claude 3.5 Haiku',
            'provider' => 'Anthropic Claude',
            'provider_badge' => 'Claude',
            'badge' => 'Haiku 3.5',
            'badge_color' => 'orange',
            'desc' => 'High-velocity compact model from Anthropic for instant, precise Q&A.',
            'speed' => 'Ultra Fast',
            'intelligence' => 'High',
        ],

        // xAI Grok Models (via OpenRouter)
        'grok-4.5' => [
            'name' => 'Grok 2 (xAI)',
            'provider' => 'xAI Grok',
            'provider_badge' => 'xAI',
            'badge' => 'Grok Intelligence',
            'badge_color' => 'rose',
            'desc' => 'State-of-the-art model from xAI with real-world knowledge and direct answers.',
            'speed' => 'Fast',
            'intelligence' => 'Very High',
        ],

        // Alibaba Qwen Coder (via OpenRouter)
        'qwen-coder' => [
            'name' => 'Qwen 2.5 Coder',
            'provider' => 'Alibaba Qwen',
            'provider_badge' => 'Qwen',
            'badge' => 'Coder 32B',
            'badge_color' => 'emerald',
            'desc' => 'Specialized code intelligence for multi-language development, algorithms & refactoring.',
            'speed' => 'Ultra Fast',
            'intelligence' => 'Very High',
        ],

        // Meta Llama Models (via OpenRouter)
        'llama-3.3-70b' => [
            'name' => 'Llama 3.3 70B',
            'provider' => 'Meta AI',
            'provider_badge' => 'Meta',
            'badge' => '70B Turbo',
            'badge_color' => 'indigo',
            'desc' => 'Meta flagship open-weights model delivering industry-grade reasoning and wide knowledge.',
            'speed' => 'Fast',
            'intelligence' => 'Very High',
        ],

        // Live Real-Time Web Search AI (via OpenRouter :online)
        'web-search' => [
            'name' => 'Live Web Search AI',
            'provider' => 'Real-Time Web',
            'provider_badge' => 'Web Search',
            'badge' => 'Live Search',
            'badge_color' => 'sky',
            'desc' => 'Real-time live internet grounding for today\'s news, current prices, sports scores and live facts.',
            'speed' => 'Fast',
            'intelligence' => 'Live Grounded',
        ],

        // Aliases & Backward compatibility mappings
        'deepseek-r1' => [
            'name' => 'DeepSeek R1',
            'provider' => 'DeepSeek AI',
            'provider_badge' => 'DeepSeek',
            'badge' => 'Deep Thinking (R1)',
            'badge_color' => 'teal',
            'desc' => 'Mapped to DeepSeek R1.',
            'speed' => 'Deliberate',
            'intelligence' => 'Maximum',
        ],
        'deepseek-v3' => [
            'name' => 'DeepSeek V3',
            'provider' => 'DeepSeek AI',
            'provider_badge' => 'DeepSeek',
            'badge' => 'MoE 671B',
            'badge_color' => 'sky',
            'desc' => 'Mapped to DeepSeek V3.',
            'speed' => 'Ultra Fast',
            'intelligence' => 'Very High',
        ],
        'gemini-2.0-flash' => [
            'name' => 'Gemini 3.1 Flash',
            'provider' => 'Google Gemini',
            'provider_badge' => 'Google',
            'badge' => 'Default & Fast',
            'badge_color' => 'blue',
            'desc' => 'Mapped to Gemini 3.1 Flash.',
            'speed' => 'Fastest',
            'intelligence' => 'High',
        ],
        'gemini-2.5-flash' => [
            'name' => 'Gemini 3.5 Flash',
            'provider' => 'Google Gemini',
            'provider_badge' => 'Google',
            'badge' => 'High Throughput',
            'badge_color' => 'indigo',
            'desc' => 'Mapped to Gemini 3.5 Flash.',
            'speed' => 'Fast',
            'intelligence' => 'High',
        ],
        'gemini-2.5-pro' => [
            'name' => 'Gemini 4 Argon',
            'provider' => 'Google Gemini',
            'provider_badge' => 'Google',
            'badge' => 'Deep Reasoning',
            'badge_color' => 'purple',
            'desc' => 'Mapped to Gemini 4 Argon.',
            'speed' => 'Thoughtful',
            'intelligence' => 'Maximum',
        ],
        'gemini-2.0-flash-thinking-exp' => [
            'name' => 'Gemini 4 Argon',
            'provider' => 'Google Gemini',
            'provider_badge' => 'Google',
            'badge' => 'Chain of Thought',
            'badge_color' => 'emerald',
            'desc' => 'Mapped to Gemini 4 Argon.',
            'speed' => 'Deliberate',
            'intelligence' => 'Maximum',
        ],
        'gemini-2.0-flash-lite' => [
            'name' => 'Gemini 3.1 Flash',
            'provider' => 'Google Gemini',
            'provider_badge' => 'Google',
            'badge' => 'Instant & Light',
            'badge_color' => 'amber',
            'desc' => 'Mapped to Gemini 3.1 Flash.',
            'speed' => 'Instant',
            'intelligence' => 'Balanced',
        ],
        'gpt-4o' => [
            'name' => 'ChatGPT 4o-mini',
            'provider' => 'OpenAI ChatGPT',
            'provider_badge' => 'OpenAI',
            'badge' => 'GPT-4o Mini',
            'badge_color' => 'emerald',
            'desc' => 'Mapped to ChatGPT 4o-mini.',
            'speed' => 'Fastest',
            'intelligence' => 'High',
        ],
        'o3-mini' => [
            'name' => 'DeepSeek R1',
            'provider' => 'DeepSeek AI',
            'provider_badge' => 'DeepSeek',
            'badge' => 'Reasoning',
            'badge_color' => 'teal',
            'desc' => 'Reasoning model mapped to DeepSeek R1.',
            'speed' => 'Deliberate',
            'intelligence' => 'Maximum',
        ],
        'gpt-3.5-turbo' => [
            'name' => 'ChatGPT 4o-mini',
            'provider' => 'OpenAI ChatGPT',
            'provider_badge' => 'OpenAI',
            'badge' => 'GPT-4o Mini',
            'badge_color' => 'emerald',
            'desc' => 'Legacy model mapped to ChatGPT 4o-mini.',
            'speed' => 'Fastest',
            'intelligence' => 'High',
        ],
    ];

    /**
     * Display the chat interface.
     */
    public function index(Request $request, ?Conversation $conversation = null): View
    {
        $sessionId = $this->resolveSessionId($request);
        $userId = $request->user()?->id;

        $conversations = Conversation::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId), fn ($q) => $q->where('session_id', $sessionId))
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->get();

        if ($conversation && $conversation->exists) {
            $activeConversation = $conversation;
        } else {
            $activeConversation = $conversations->first();
        }

        $messages = $activeConversation
            ? $activeConversation->messages()->get()
            : collect();

        return view('chat', [
            'models' => $this->models,
            'defaultModel' => config('services.ai.model', 'auto'),
            'personas' => $this->personas,
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
        ]);
    }

    /**
     * Handle file uploads (Images, Documents, Code).
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'], // max 20MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getClientMimeType() ?: 'application/octet-stream';
        $sizeBytes = $file->getSize();

        // Safe filename
        $fileName = Str::random(24) . '.' . $extension;
        $destinationPath = public_path('uploads');

        if (! file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);
        $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $fileName;

        $isImage = str_starts_with($mime, 'image/') || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
        $base64 = null;
        $textPreview = null;

        if ($isImage) {
            $fileData = file_get_contents($fullPath);
            $base64 = 'data:' . $mime . ';base64,' . base64_encode($fileData);
        } else {
            // Read first 40KB for text or code documents
            if (in_array($extension, ['txt', 'md', 'json', 'csv', 'xml', 'log', 'php', 'js', 'ts', 'py', 'html', 'css', 'sql', 'yaml', 'yml', 'env', 'sh', 'java', 'cpp', 'c', 'h'])) {
                $content = @file_get_contents($fullPath);
                $textPreview = Str::limit($content, 40000, "\n...[Content truncated]");
            }
        }

        // Format readable size
        $formattedSize = $sizeBytes >= 1048576
            ? number_format($sizeBytes / 1048576, 1) . ' MB'
            : number_format($sizeBytes / 1024, 0) . ' KB';

        return response()->json([
            'url' => '/uploads/' . $fileName,
            'name' => $originalName,
            'size' => $formattedSize,
            'type' => $isImage ? 'image' : 'document',
            'mime' => $mime,
            'base64' => $base64,
            'text_preview' => $textPreview,
        ]);
    }

    /**
     * Get list of conversations for sidebar (AJAX).
     */
    public function list(Request $request): JsonResponse
    {
        $sessionId = $this->resolveSessionId($request);
        $userId = $request->user()?->id;

        $conversations = Conversation::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId), fn ($q) => $q->where('session_id', $sessionId))
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($conversations);
    }

    /**
     * Get a specific conversation and its messages (AJAX).
     */
    public function show(Conversation $conversation): JsonResponse
    {
        return response()->json([
            'conversation' => $conversation,
            'messages' => $conversation->messages()->orderBy('id')->get(),
        ]);
    }

    /**
     * Create a new conversation in MySQL.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->models))],
            'persona' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->personas))],
            'title' => ['nullable', 'string', 'max:100'],
        ]);

        $conversation = Conversation::create([
            'user_id' => $request->user()?->id,
            'session_id' => $this->resolveSessionId($request),
            'title' => $validated['title'] ?? 'New Chat',
            'model' => $validated['model'] ?? config('services.ai.model', 'gpt-4o-mini'),
            'persona' => $validated['persona'] ?? 'default',
            'temperature' => 0.70,
        ]);

        return response()->json([
            'conversation' => $conversation,
        ], 201);
    }

    /**
     * Update conversation properties.
     */
    public function update(Request $request, Conversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->models))],
            'persona' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->personas))],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:2'],
        ]);

        $conversation->update(array_filter($validated, fn ($val) => ! is_null($val)));

        return response()->json([
            'conversation' => $conversation,
        ]);
    }

    /**
     * Delete a conversation and all its messages.
     */
    public function destroy(Conversation $conversation): JsonResponse
    {
        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted.',
        ]);
    }

    /**
     * Handle incoming chat message with Server-Sent Events (SSE) streaming and multimodal attachments.
     */
    public function stream(Request $request, ChatbotService $chatbot): StreamedResponse
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:10000'],
            'conversation_id' => ['nullable', 'integer', 'exists:conversations,id'],
            'model' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->models))],
            'persona' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->personas))],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:2'],
            'attachments' => ['nullable', 'array'],
            'web_search' => ['nullable', 'boolean'],
        ]);

        $userPrompt = trim((string) ($validated['message'] ?? ''));
        $attachments = $validated['attachments'] ?? [];

        if (empty($userPrompt) && empty($attachments)) {
            abort(422, 'Message or attachment is required.');
        }

        $sessionId = $this->resolveSessionId($request);
        $userId = $request->user()?->id;

        // Find or create conversation in MySQL
        if (! empty($validated['conversation_id'])) {
            $conversation = Conversation::findOrFail($validated['conversation_id']);
        } else {
            $initialTitle = ! empty($userPrompt)
                ? Str::limit($userPrompt, 32, '...')
                : (! empty($attachments) ? 'File: ' . Str::limit($attachments[0]['name'] ?? 'Upload', 24) : 'New Chat');

            $conversation = Conversation::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'title' => $initialTitle,
                'model' => $validated['model'] ?? config('services.ai.model', 'auto'),
                'persona' => $validated['persona'] ?? 'default',
                'temperature' => isset($validated['temperature']) ? (float) $validated['temperature'] : 0.70,
            ]);
        }

        // If title is default, update it
        if ($conversation->title === 'New Chat') {
            $newTitle = ! empty($userPrompt)
                ? Str::limit($userPrompt, 32, '...')
                : (! empty($attachments) ? 'File: ' . Str::limit($attachments[0]['name'] ?? 'Upload', 24) : 'New Chat');
            $conversation->update(['title' => $newTitle]);
        }

        if (! empty($validated['model']) && $conversation->model !== $validated['model']) {
            $conversation->update(['model' => $validated['model']]);
        }

        $requestedModel = $validated['model'] ?? $conversation->model ?? 'auto';
        $isWebSearch = (bool) ($request->boolean('web_search'));
        $autoDecision = null;

        if ($isWebSearch) {
            $activeModel = 'web-search';
            $autoDecision = [
                'model' => 'web-search',
                'provider' => 'Real-Time Web AI',
                'reason' => 'Live Internet Search & Grounding',
                'badge' => 'Live Search',
            ];
        } elseif ($requestedModel === 'auto' || empty($requestedModel)) {
            $autoDecision = $chatbot->detectOptimalModel($userPrompt, $attachments);
            $activeModel = $autoDecision['model'];
        } else {
            $activeModel = $requestedModel;
        }

        $personaKey = $conversation->persona;
        $systemPrompt = $this->personas[$personaKey]['prompt'] ?? $this->personas['default']['prompt'];

        if ($isWebSearch || $activeModel === 'web-search') {
            $systemPrompt .= "\n\nYou have real-time live internet web browsing enabled. Search the live internet for up-to-date, factual information, current prices, today's news and cite sources wherever relevant.";
        }

        // Save user message in MySQL with attachments JSON
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userPrompt ?: 'Analyzed uploaded attachment(s).',
            'model' => $activeModel,
            'attachments' => ! empty($attachments) ? $attachments : null,
        ]);

        // Build zero-pollution API messages payload: focuses 100% on the user's latest prompt
        $textAdditions = '';
        $hasImages = false;
        $imageParts = [];

        foreach ($attachments as $att) {
            if (($att['type'] ?? '') === 'image' && ! empty($att['base64'])) {
                $hasImages = true;
                $imageParts[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => $att['base64'],
                    ],
                ];
            } elseif (! empty($att['text_preview'])) {
                $textAdditions .= "\n\n[Attached Document: {$att['name']}]\n```\n{$att['text_preview']}\n```";
            }
        }

        $userContent = $userPrompt . $textAdditions;

        if ($hasImages) {
            $userMessageArray = [
                ['type' => 'text', 'text' => $userContent],
            ];
            foreach ($imageParts as $img) {
                $userMessageArray[] = $img;
            }
            $apiMessages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessageArray],
            ];
        } else {
            $apiMessages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userContent],
            ];
        }

        return response()->stream(function () use ($chatbot, $apiMessages, $activeModel, $conversation, $autoDecision): void {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            echo 'data: ' . json_encode([
                'conversation_id' => $conversation->id,
                'title' => $conversation->title,
                'model' => $activeModel,
                'auto_routed' => $autoDecision !== null,
                'routed_reason' => $autoDecision['reason'] ?? null,
                'routed_provider' => $autoDecision['provider'] ?? null,
                'routed_badge' => $autoDecision['badge'] ?? null,
            ]) . "\n\n";

            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();

            $fullReply = '';

            foreach ($chatbot->stream($apiMessages, $activeModel, (float) $conversation->temperature) as $chunk) {
                $fullReply .= $chunk;
                echo 'data: ' . json_encode(['text' => $chunk]) . "\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }

            // Save assistant reply into MySQL
            Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $fullReply,
                'model' => $activeModel,
            ]);

            $conversation->touch();

            echo "data: [DONE]\n\n";

            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Fallback standard JSON send endpoint.
     */
    public function send(Request $request, ChatbotService $chatbot): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:10000'],
            'conversation_id' => ['nullable', 'integer', 'exists:conversations,id'],
            'model' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->models))],
            'attachments' => ['nullable', 'array'],
            'web_search' => ['nullable', 'boolean'],
        ]);

        $userPrompt = trim((string) ($validated['message'] ?? ''));
        $attachments = $validated['attachments'] ?? [];

        if (empty($userPrompt) && empty($attachments)) {
            abort(422, 'Message or attachment is required.');
        }

        $sessionId = $this->resolveSessionId($request);
        $userId = $request->user()?->id;

        if (! empty($validated['conversation_id'])) {
            $conversation = Conversation::findOrFail($validated['conversation_id']);
        } else {
            $conversation = Conversation::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'title' => Str::limit($userPrompt ?: 'Attachment Chat', 32, '...'),
                'model' => $validated['model'] ?? config('services.ai.model', 'auto'),
                'persona' => 'default',
                'temperature' => 0.70,
            ]);
        }

        $requestedModel = $validated['model'] ?? $conversation->model ?? 'auto';
        $isWebSearch = (bool) ($request->boolean('web_search'));
        $autoDecision = null;

        if ($isWebSearch) {
            $activeModel = 'web-search';
            $autoDecision = [
                'model' => 'web-search',
                'provider' => 'Real-Time Web AI',
                'reason' => 'Live Internet Search & Grounding',
                'badge' => 'Live Search',
            ];
        } elseif ($requestedModel === 'auto' || empty($requestedModel)) {
            $autoDecision = $chatbot->detectOptimalModel($userPrompt, $attachments);
            $activeModel = $autoDecision['model'];
        } else {
            $activeModel = $requestedModel;
        }

        $personaKey = $conversation->persona;
        $systemPrompt = $this->personas[$personaKey]['prompt'] ?? $this->personas['default']['prompt'];

        if ($isWebSearch || $activeModel === 'web-search') {
            $systemPrompt .= "\n\nYou have real-time live internet web browsing enabled. Search the live internet for up-to-date, factual information, current prices, today's news and cite sources wherever relevant.";
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userPrompt ?: 'Uploaded attachment.',
            'model' => $activeModel,
            'attachments' => ! empty($attachments) ? $attachments : null,
        ]);

        $reply = $chatbot->ask([
            ['role' => 'system', 'content' => $this->personas[$conversation->persona]['prompt']],
            ['role' => 'user', 'content' => $userPrompt],
        ], $activeModel, (float) $conversation->temperature);

        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $reply,
            'model' => $activeModel,
        ]);

        $conversation->touch();

        return response()->json([
            'conversation_id' => $conversation->id,
            'title' => $conversation->title,
            'reply' => $reply,
            'model' => $activeModel,
            'auto_routed' => $autoDecision !== null,
            'routed_reason' => $autoDecision['reason'] ?? null,
            'routed_provider' => $autoDecision['provider'] ?? null,
            'routed_badge' => $autoDecision['badge'] ?? null,
        ]);
    }

    /**
     * Resolve or generate a persistent session ID for guest visitors.
     */
    protected function resolveSessionId(Request $request): string
    {
        if (! $request->session()->has('chat_session_id')) {
            $request->session()->put('chat_session_id', (string) Str::uuid());
            $request->session()->save();
        }

        return (string) $request->session()->get('chat_session_id');
    }
}
