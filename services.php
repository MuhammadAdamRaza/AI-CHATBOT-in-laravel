<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ai' => [
        'provider' => env('AI_PROVIDER', 'auto'),
        'key' => env('GEMINI_API_KEY', env('AI_API_KEY')),
        'model' => env('AI_MODEL', 'auto'),
        'url' => env('AI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions'),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY', env('AI_API_KEY')),
        'model' => env('GEMINI_MODEL', 'gemini-3.1-flash-lite'),
        'url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions'),
    ],

    'deepseek' => [
        'key' => env('DEEPSEEK_API_KEY', env('OPENROUTER_API_KEY')),
        'model' => env('DEEPSEEK_MODEL', 'deepseek/deepseek-chat'),
        'url' => env('DEEPSEEK_API_URL', 'https://openrouter.ai/api/v1/chat/completions'),
    ],

    'openrouter' => [
        'key' => env('OPENROUTER_API_KEY'),
        'url' => env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions'),
    ],

];
