<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatbotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('chat page can be rendered with gemini, chatgpt, deepseek, claude, and grok models', function () {
    $response = $this->get('/chat');

    $response->assertStatus(200);
    $response->assertSee('Nexus AI');
    $response->assertSee('Auto (Smart Engine)');
    $response->assertSee('Gemini 3.1 Flash');
    $response->assertSee('DeepSeek V3');
    $response->assertSee('DeepSeek R1');
    $response->assertSee('ChatGPT 4o-mini');
    $response->assertSee('Claude 3.5 Sonnet');
    $response->assertSee('Grok 2 (xAI)');
});

test('chat message validation fails on empty input', function () {
    $response = $this->postJson('/chat/stream', [
        'message' => '',
    ]);

    $response->assertStatus(422);
});

test('chatbot service recognizes both gemini and deepseek providers', function () {
    $service = app(ChatbotService::class);

    expect($service->isDeepSeek('deepseek-chat'))->toBeTrue();
    expect($service->isDeepSeek('deepseek-reasoner'))->toBeTrue();
    expect($service->isDeepSeek('gemini-3.1-flash-lite'))->toBeFalse();
    expect($service->isDeepSeek('gemini-4-argon'))->toBeFalse();
});

test('chatbot service resolves urls correctly for each provider', function () {
    $service = app(ChatbotService::class);

    expect($service->resolveUrl('deepseek-chat'))->toContain('openrouter.ai');
    expect($service->resolveUrl('gemini-3.1-flash-lite'))->toContain('generativelanguage.googleapis.com');
});

test('chatbot service extracts image prompts for instant generation', function () {
    $service = app(ChatbotService::class);

    $prompt1 = $service->extractImagePrompt('spider ki image bana kar do');
    expect($prompt1)->not->toBeNull();

    $prompt2 = $service->extractImagePrompt('hulk image generate karoo');
    expect($prompt2)->not->toBeNull();

    $question = $service->extractImagePrompt('what is mysql');
    expect($question)->toBeNull();
});

test('chat stream endpoint returns streamed response for gemini model', function () {
    $response = $this->post('/chat/stream', [
        'message' => 'Hello stream test',
        'model' => 'gemini-3.1-flash-lite',
    ]);

    $response->assertStatus(200);
    $this->assertStringContainsString('text/event-stream', (string) $response->headers->get('Content-Type'));
});

test('chat stream endpoint returns instructional notice when deepseek key is unconfigured', function () {
    config()->set('services.deepseek.key', '');

    $response = $this->post('/chat/stream', [
        'message' => 'Explain quantum mechanics',
        'model' => 'deepseek-chat',
    ]);

    $response->assertStatus(200);
    $this->assertStringContainsString('text/event-stream', (string) $response->headers->get('Content-Type'));
});

test('chatbot service dynamically routes prompt requirements to optimal engines', function () {
    $service = app(ChatbotService::class);

    // 1. Laravel Architecture -> Claude 3.5 Sonnet
    $res1 = $service->detectOptimalModel('How to create a multi-auth system in Laravel with Blade?');
    expect($res1['model'])->toBe('claude-sonnet-5');
    expect($res1['provider'])->toContain('Claude');

    // 2. Math & Algorithm Proof -> DeepSeek R1
    $res2 = $service->detectOptimalModel('Solve Dijkstra algorithm step by step with proof');
    expect($res2['model'])->toBe('deepseek-reasoner');
    expect($res2['provider'])->toContain('DeepSeek');

    // 3. Backend Python / Bug Fix -> DeepSeek V3
    $res3 = $service->detectOptimalModel('Fix syntax error in this python function with regex');
    expect($res3['model'])->toBe('deepseek-chat');

    // 4. Trending News -> Grok 2
    $res4 = $service->detectOptimalModel('What is the latest news and trending topics today?');
    expect($res4['model'])->toBe('grok-4.5');

    // 5. Image attachment -> Gemini Vision
    $res5 = $service->detectOptimalModel('Read text in this bill', [['type' => 'image', 'name' => 'bill.jpg']]);
    expect($res5['model'])->toBe('gemini-3.1-flash-lite');

    // 6. Fast General Chat -> ChatGPT
    $res6 = $service->detectOptimalModel('What is the capital of France?');
    expect($res6['model'])->toBe('gpt-4o-mini');
});
