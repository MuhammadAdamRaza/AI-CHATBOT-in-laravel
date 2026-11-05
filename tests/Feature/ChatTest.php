<?php

use App\Services\ChatbotService;
use Illuminate\Support\Facades\Http;

test('chat page can be rendered with models and personas', function () {
    $response = $this->get('/chat');

    $response->assertStatus(200);
    $response->assertSee('AI Chatbot');
    $response->assertSee('GPT-4o');
    $response->assertSee('Senior Code Architect');
});

test('chat message validation fails on empty message', function () {
    $response = $this->postJson('/chat', [
        'message' => '',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['message']);
});

test('chat send returns json reply with model', function () {
    config()->set('services.ai.key', 'test-openai-key');
    config()->set('services.ai.url', 'https://api.openai.com/v1/chat/completions');

    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'role' => 'assistant',
                        'content' => 'Hello! How can I assist you today?',
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->postJson('/chat', [
        'message' => 'Hello!',
        'model' => 'gpt-4o-mini',
        'persona' => 'coder',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'reply' => 'Hello! How can I assist you today?',
        'model' => 'gpt-4o-mini',
    ]);

    $this->assertTrue(session()->has('chat_history'));
});

test('chat stream endpoint returns streamed response', function () {
    config()->set('services.ai.key', 'your-chatgpt-api-key-here');

    $response = $this->post('/chat/stream', [
        'message' => 'Hello stream!',
        'model' => 'gpt-4o-mini',
    ]);

    $response->assertStatus(200);
    $this->assertEquals('text/event-stream', $response->headers->get('Content-Type'));
});

test('chat clear endpoint clears history', function () {
    session(['chat_history' => [['role' => 'user', 'content' => 'test']]]);

    $response = $this->postJson('/chat/clear');

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    $this->assertNull(session('chat_history'));
});
