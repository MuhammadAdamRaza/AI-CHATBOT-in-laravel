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
            'prompt' => 'You are Nexus AI, an advanced, polite, and helpful assistant powered by Google Gemini. Provide high-quality, formatted Markdown responses. When analyzing images or documents, be thorough, detailed, and insightful. CRITICAL CONVERSATION RULES: 1. Always answer the user\'s latest message directly. Do NOT apologize for past typos or drag old unrelated topics (like past movie questions or previous conversation threads) into new messages. 2. You have full AI image generation capabilities: whenever the user asks to create, draw, generate, or visualize an image (e.g. "generate an image of...", "draw a...", "tasveer banao", "spider image"), formulate a vivid, detailed descriptive English prompt and ALWAYS embed it directly using Markdown: ![Image Description](https://image.pollinations.ai/prompt/{url_encoded_prompt}?width=1024&height=1024&nologo=true&seed={random_number}) followed by a brief friendly description. NEVER ask "Do you want me to generate an image?" - immediately generate it!',
            'icon' => 'sparkles',
        ],
        'artist' => [
            'name' => 'AI Image & Concept Artist',
            'description' => 'Generates photorealistic images, digital art & illustrations.',
            'prompt' => 'You are an elite AI digital artist and concept designer. When the user describes a visual or asks for an image, refine their idea into a breathtaking, highly detailed visual prompt and render it using: ![Art Description](https://image.pollinations.ai/prompt/{url_encoded_prompt}?width=1024&height=1024&nologo=true&seed={random_number}) and describe the artistic style, lighting, and composition.',
            'icon' => 'image',
        ],
        'coder' => [
            'name' => 'Senior Code Architect',
            'description' => 'Clean code, architecture patterns, and debugging.',
            'prompt' => 'You are an elite principal software engineer and architect powered by Google Gemini. Provide clean, well-commented, secure code in markdown format with language identifiers and performance insights.',
            'icon' => 'code',
        ],
        'writer' => [
            'name' => 'Copywriter & Editor',
            'description' => 'Creative writing, emails, and content polish.',
            'prompt' => 'You are a master creative writer and professional editor powered by Google Gemini. Craft compelling, articulate prose, catchy headlines, and crisp emails.',
            'icon' => 'pen-tool',
        ],
        'analyst' => [
            'name' => 'Business Analyst',
            'description' => 'Data breakdowns, SWOT, strategy, and logic.',
            'prompt' => 'You are a senior business intelligence and strategy analyst powered by Google Gemini. Structure your insights with concise bullet points, pros/cons, and actionable next steps.',
            'icon' => 'bar-chart-2',
        ],
        'concise' => [
            'name' => 'Direct & Concise',
            'description' => 'Bullet-point answers with zero filler.',
            'prompt' => 'You are a direct, concise AI assistant powered by Google Gemini. Answer immediately with facts and bullets without introductory greetings or conversational fluff.',
            'icon' => 'zap',
        ],
    ];

    /**
     * Google Gemini Models with capability metadata.
     *
     * @var array<string, array{name: string, badge: string, badge_color: string, desc: string, speed: string, intelligence: string}>
     */
    protected array $models = [
        'gemini-3.1-flash-lite' => [
            'name' => 'Gemini 3.1 Flash',
            'badge' => 'Default & Fast',
            'badge_color' => 'blue',
            'desc' => "Ultra-fast multimodal execution model for immediate answers, code & analysis.",
            'speed' => 'Fastest',
            'intelligence' => 'High',
        ],
        'gemini-3.5-flash-lite' => [
            'name' => 'Gemini 3.5 Flash',
            'badge' => 'High Throughput',
            'badge_color' => 'indigo',
            'desc' => 'High-capacity multimodal model with 1M token context for large documents & files.',
            'speed' => 'Fast',
            'intelligence' => 'High',
        ],
        'gemini-4-argon' => [
            'name' => 'Gemini 4 Argon',
            'badge' => 'Deep Reasoning',
            'badge_color' => 'purple',
            'desc' => 'Most powerful Google model for complex logic, math, research and architecture.',
            'speed' => 'Thoughtful',
            'intelligence' => 'Maximum',
        ],
        'gemini-3.8-flash' => [
            'name' => 'Gemini 3.8 Flash',
            'badge' => 'Multimodal Pro',
            'badge_color' => 'emerald',
            'desc' => 'Balanced multimodal intelligence for complex vision, files and workflows.',
            'speed' => 'Balanced',
            'intelligence' => 'High',
        ],
        // Backward compatibility mappings for previously created chats
        'gemini-2.0-flash' => [
            'name' => 'Gemini 3.1 Flash',
            'badge' => 'Default & Fast',
            'badge_color' => 'blue',
            'desc' => 'Mapped to Gemini 3.1 Flash.',
            'speed' => 'Fastest',
            'intelligence' => 'High',
        ],
        'gemini-2.5-flash' => [
            'name' => 'Gemini 3.5 Flash',
            'badge' => 'High Throughput',
            'badge_color' => 'indigo',
            'desc' => 'Mapped to Gemini 3.5 Flash.',
            'speed' => 'Fast',
            'intelligence' => 'High',
        ],
        'gemini-2.5-pro' => [
            'name' => 'Gemini 4 Argon',
            'badge' => 'Deep Reasoning',
            'badge_color' => 'purple',
            'desc' => 'Mapped to Gemini 4 Argon.',
            'speed' => 'Thoughtful',
            'intelligence' => 'Maximum',
        ],
        'gemini-2.0-flash-thinking-exp' => [
            'name' => 'Gemini 4 Argon',
            'badge' => 'Chain of Thought',
            'badge_color' => 'emerald',
            'desc' => 'Mapped to Gemini 4 Argon.',
            'speed' => 'Deliberate',
            'intelligence' => 'Maximum',
        ],
        'gemini-2.0-flash-lite' => [
            'name' => 'Gemini 3.1 Flash',
            'badge' => 'Instant & Light',
            'badge_color' => 'amber',
            'desc' => 'Mapped to Gemini 3.1 Flash.',
            'speed' => 'Instant',
            'intelligence' => 'Balanced',
        ],
        'gpt-4o' => [
            'name' => 'Gemini 4 Argon',
            'badge' => 'Deep Reasoning',
            'badge_color' => 'purple',
            'desc' => 'Flagship model mapped to Gemini 4 Argon.',
            'speed' => 'Thoughtful',
            'intelligence' => 'Maximum',
        ],
        'gpt-4o-mini' => [
            'name' => 'Gemini 3.1 Flash',
            'badge' => 'Fast & Light',
            'badge_color' => 'blue',
            'desc' => 'Workhorse model mapped to Gemini 3.1 Flash.',
            'speed' => 'Fastest',
            'intelligence' => 'High',
        ],
        'o3-mini' => [
            'name' => 'Gemini 4 Argon',
            'badge' => 'Reasoning',
            'badge_color' => 'emerald',
            'desc' => 'Reasoning model mapped to Gemini 4 Argon.',
            'speed' => 'Deliberate',
            'intelligence' => 'Maximum',
        ],
        'gpt-3.5-turbo' => [
            'name' => 'Gemini 3.1 Flash',
            'badge' => 'Instant & Light',
            'badge_color' => 'amber',
            'desc' => 'Lightweight model mapped to Gemini 3.1 Flash.',
            'speed' => 'Instant',
            'intelligence' => 'Balanced',
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
            'defaultModel' => config('services.ai.model', 'gemini-3.1-flash-lite'),
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
                'model' => $validated['model'] ?? config('services.ai.model', 'gemini-3.1-flash-lite'),
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

        $activeModel = $conversation->model;
        $personaKey = $conversation->persona;
        $systemPrompt = $this->personas[$personaKey]['prompt'] ?? $this->personas['default']['prompt'];

        // Save user message in MySQL with attachments JSON
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userPrompt ?: 'Analyzed uploaded attachment(s).',
            'model' => $activeModel,
            'attachments' => ! empty($attachments) ? $attachments : null,
        ]);

        // Build messages payload for AI API from MySQL history
        $recentMessages = $conversation->messages()
            ->orderBy('id', 'desc')
            ->take(16)
            ->get()
            ->reverse();

        $apiMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($recentMessages as $msg) {
            $msgContent = $msg->content;
            $msgAttachments = is_array($msg->attachments) ? $msg->attachments : [];

            $hasImages = false;
            $imageParts = [];
            $textAdditions = '';

            foreach ($msgAttachments as $att) {
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

            if ($hasImages) {
                $contentArray = [
                    ['type' => 'text', 'text' => $msgContent . $textAdditions],
                ];
                foreach ($imageParts as $img) {
                    $contentArray[] = $img;
                }
                $apiMessages[] = [
                    'role' => $msg->role,
                    'content' => $contentArray,
                ];
            } else {
                $apiMessages[] = [
                    'role' => $msg->role,
                    'content' => $msgContent . $textAdditions,
                ];
            }
        }

        return response()->stream(function () use ($chatbot, $apiMessages, $activeModel, $conversation): void {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            echo 'data: ' . json_encode([
                'conversation_id' => $conversation->id,
                'title' => $conversation->title,
                'model' => $conversation->model,
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
                'model' => $validated['model'] ?? config('services.ai.model', 'gemini-3.1-flash-lite'),
                'persona' => 'default',
                'temperature' => 0.70,
            ]);
        }

        $activeModel = $validated['model'] ?? $conversation->model;

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
