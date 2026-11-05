<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nexus AI &mdash; Advanced Intelligence</title>

    <!-- Tailwind CSS with typography & dark mode -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#090d16',
                        },
                        brand: {
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Markdown & Syntax Highlighting -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark-dimmed.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.3); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.5); }

        .prose-chat pre {
            margin: 0.75rem 0;
            padding: 0;
            border-radius: 0.85rem;
            overflow: hidden;
            background: #141721 !important;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .prose-chat pre code {
            padding: 1rem;
            font-size: 0.85rem;
            display: block;
            overflow-x: auto;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .prose-chat code:not(pre code) {
            background: rgba(125, 125, 125, 0.15);
            padding: 0.15rem 0.35rem;
            border-radius: 0.35rem;
            font-size: 0.85em;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .prose-chat p { margin-bottom: 0.75rem; line-height: 1.65; }
        .prose-chat p:last-child { margin-bottom: 0; }
        .prose-chat ul { list-style-type: disc; margin-left: 1.25rem; margin-bottom: 0.65rem; }
        .prose-chat ol { list-style-type: decimal; margin-left: 1.25rem; margin-bottom: 0.65rem; }
        .prose-chat blockquote {
            border-left: 3px solid #3b82f6;
            padding-left: 0.85rem;
            margin: 0.75rem 0;
            opacity: 0.85;
            font-style: italic;
        }
        .prose-chat table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.75rem 0;
            font-size: 0.85rem;
        }
        .prose-chat th, .prose-chat td {
            border: 1px solid rgba(148, 163, 184, 0.25);
            padding: 0.5rem 0.85rem;
            text-align: left;
        }
        .prose-chat th { background: rgba(148, 163, 184, 0.12); }
        .prose-chat img {
            border-radius: 1rem;
            max-width: 100%;
            height: auto;
            margin: 0.85rem 0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(148, 163, 184, 0.2);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: rgba(148, 163, 184, 0.08);
            display: block;
        }
        .prose-chat img:hover {
            transform: scale(1.015);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.25);
        }

        .cursor-blink {
            display: inline-block;
            width: 6px;
            height: 15px;
            background-color: currentColor;
            vertical-align: -1px;
            margin-left: 2px;
            animation: blink 0.8s infinite;
        }
        @keyframes blink { 0%, 50% { opacity: 1; } 51%, 100% { opacity: 0; } }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.3; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .voice-active { animation: pulse-ring 1.5s infinite ease-in-out; }

        /* Dropzone overlay */
        .dropzone-overlay {
            display: none;
        }
        .dragging .dropzone-overlay {
            display: flex;
        }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-900 dark:bg-dark-950 dark:text-slate-100 flex flex-col font-sans transition-colors duration-200 antialiased overflow-hidden select-none sm:select-auto">

    <div class="flex h-full w-full relative" id="app-container">

        {{-- DRAG & DROP OVERLAY --}}
        <div class="dropzone-overlay fixed inset-0 z-50 bg-blue-600/20 backdrop-blur-sm border-4 border-dashed border-blue-500 items-center justify-center p-6 text-center pointer-events-none transition-all">
            <div class="p-8 rounded-3xl bg-white dark:bg-dark-900 shadow-2xl flex flex-col items-center gap-3">
                <div class="h-16 w-16 rounded-2xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center animate-bounce">
                    <i data-lucide="upload-cloud" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold">Drop files here to analyze</h3>
                <p class="text-xs text-slate-500">Supports Images, PDFs, Code, TXT, CSV and Markdown</p>
            </div>
        </div>

        {{-- MOBILE OVERLAY BACKDROP --}}
        <div id="mobile-backdrop" class="fixed inset-0 bg-black/60 z-30 hidden lg:hidden backdrop-blur-xs transition-opacity"></div>

        {{-- LEFT SIDEBAR --}}
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-72 bg-white dark:bg-dark-900 border-r border-slate-200 dark:border-slate-800/80 flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shrink-0">
            
            {{-- Top Branding & New Chat --}}
            <div class="p-3.5 border-b border-slate-200 dark:border-slate-800/80 flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <a href="/" class="flex items-center gap-2.5 group">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-violet-600 text-white flex items-center justify-center shadow-md shadow-blue-500/25 group-hover:scale-105 transition-transform">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="font-bold text-sm tracking-tight flex items-center gap-1.5">
                                <span>Nexus AI</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">STUDIO</span>
                            </span>
                            <p class="text-[10px] text-slate-400">Powered by Google Gemini</p>
                        </div>
                    </a>
                    <button id="close-sidebar-btn" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                {{-- New Chat Button --}}
                <button id="new-chat-btn" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm shadow-blue-600/30 transition active:scale-[0.98]">
                    <div class="flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>New conversation</span>
                    </div>
                    <kbd class="hidden sm:inline-block text-[10px] bg-blue-700/60 text-blue-100 px-1.5 py-0.5 rounded font-mono">Ctrl+K</kbd>
                </button>
            </div>

            {{-- Recent Conversations List --}}
            <div class="flex-1 overflow-y-auto px-2 py-3 space-y-1" id="conversations-list">
                <div class="px-2.5 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                    <span>Recent Chats</span>
                    <i data-lucide="history" class="w-3.5 h-3.5 text-slate-400"></i>
                </div>

                @forelse ($conversations as $conv)
                    <div class="conversation-item group relative flex items-center justify-between px-3 py-2.5 rounded-xl text-xs cursor-pointer transition {{ ($activeConversation && $activeConversation->id === $conv->id) ? 'bg-slate-100 dark:bg-dark-800 text-slate-900 dark:text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-dark-800/60' }}" data-id="{{ $conv->id }}">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1" onclick="loadConversation({{ $conv->id }})">
                            <i data-lucide="message-square" class="w-3.5 h-3.5 shrink-0 text-slate-400"></i>
                            <span class="truncate conversation-title">{{ $conv->title }}</span>
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 flex items-center gap-1 transition-opacity">
                            <button type="button" onclick="renameConversation(event, {{ $conv->id }})" title="Rename" class="p-1 hover:text-blue-500 text-slate-400">
                                <i data-lucide="edit-3" class="w-3 h-3"></i>
                            </button>
                            <button type="button" onclick="deleteConversation(event, {{ $conv->id }})" title="Delete" class="p-1 hover:text-red-500 text-slate-400">
                                <i data-lucide="trash" class="w-3 h-3"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div id="no-chats-placeholder" class="px-3 py-8 text-center text-xs text-slate-400">
                        No previous chats yet.<br>Start a conversation!
                    </div>
                @endforelse
            </div>

            {{-- Sidebar Footer: User Status & Theme Toggle --}}
            <div class="p-3 border-t border-slate-200 dark:border-slate-800/80 space-y-1.5 text-xs">
                <button id="theme-toggle" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800 transition">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="moon" id="theme-icon" class="w-4 h-4"></i>
                        <span id="theme-text">Dark mode</span>
                    </div>
                    <span class="text-[10px] text-slate-400">Switch</span>
                </button>
                <div class="flex items-center justify-between px-2 pt-1 text-[11px] text-slate-400">
                    <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Ready
                    </span>
                    <a href="/" class="hover:text-slate-600 dark:hover:text-slate-200">Home &rarr;</a>
                </div>
            </div>
        </aside>

        {{-- MAIN CHAT WINDOW --}}
        <main class="flex-1 flex flex-col h-full bg-white dark:bg-dark-950 overflow-hidden relative">

            {{-- TOP HEADER: Real Model Switcher & Tools --}}
            <header class="h-14 border-b border-slate-200 dark:border-slate-800/80 px-3 sm:px-5 flex items-center justify-between z-20 shrink-0 bg-white/95 dark:bg-dark-950/95 backdrop-blur">
                <div class="flex items-center gap-2 sm:gap-3">
                    <button id="open-sidebar-btn" class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    {{-- GOOGLE GEMINI MODEL SWITCHER POPOVER --}}
                    <div class="relative inline-block text-left" id="model-dropdown-container">
                        @php
                            $currentModelKey = $activeConversation?->model ?? $defaultModel;
                            if (str_starts_with($currentModelKey, 'gpt-') || str_starts_with($currentModelKey, 'o3-') || str_starts_with($currentModelKey, 'gemini-2.') || str_starts_with($currentModelKey, 'gemini-1.')) {
                                $currentModelKey = 'gemini-3.1-flash-lite';
                            }

                            $currentModelMeta = $models[$currentModelKey] ?? $models['gemini-3.1-flash-lite'] ?? reset($models);
                        @endphp
                        <button
                            id="model-switcher-btn"
                            type="button"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-dark-800 transition border border-transparent hover:border-slate-200 dark:hover:border-slate-700 active:scale-95"
                        >
                            <span id="model-display-name">{{ $currentModelMeta['name'] }}</span>
                            <span id="model-display-badge" class="text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                {{ $currentModelMeta['badge'] }}
                            </span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>

                        <div
                            id="model-menu"
                            class="hidden absolute left-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white dark:bg-dark-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-2 z-50 transition-all focus:outline-none"
                        >
                            <div class="px-3 py-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span>Google Gemini Intelligence</span>
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 dark:bg-dark-800 dark:text-blue-400 font-mono">Official API</span>
                            </div>
                            <div class="space-y-1 pt-1.5">
                                @foreach ($models as $key => $meta)
                                    @continue(str_starts_with($key, 'gpt-') || str_starts_with($key, 'o3-') || str_starts_with($key, 'gemini-2.') || str_starts_with($key, 'gemini-1.'))
                                    <button
                                        type="button"
                                        class="model-option w-full flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-dark-800 text-left transition group {{ $key === $currentModelKey ? 'bg-blue-50/60 dark:bg-dark-800/90 ring-1 ring-blue-500/30' : '' }}"
                                        data-model="{{ $key }}"
                                        data-name="{{ $meta['name'] }}"
                                        data-badge="{{ $meta['badge'] }}"
                                    >
                                        <div class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-dark-800 flex items-center justify-center shrink-0 text-slate-600 dark:text-slate-300 mt-0.5 group-hover:scale-105 transition-transform">
                                            @if (str_contains($key, 'argon'))
                                                <i data-lucide="sparkles" class="w-4 h-4 text-purple-500"></i>
                                            @elseif (str_contains($key, '3.8'))
                                                <i data-lucide="brain" class="w-4 h-4 text-emerald-500"></i>
                                            @elseif (str_contains($key, '3.5'))
                                                <i data-lucide="layers" class="w-4 h-4 text-indigo-500"></i>
                                            @else
                                                <i data-lucide="zap" class="w-4 h-4 text-blue-500"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ $meta['name'] }}</span>
                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 dark:bg-dark-800 dark:text-slate-300">
                                                    {{ $meta['badge'] }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">
                                                {{ $meta['desc'] }}
                                            </p>
                                        </div>
                                        <i data-lucide="check" class="check-icon w-4 h-4 text-blue-500 shrink-0 self-center {{ $key === $currentModelKey ? '' : 'hidden' }}"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Top Action Buttons --}}
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <button id="export-chat-btn" title="Export to Markdown" class="p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-dark-800 rounded-xl transition">
                        <i data-lucide="download" class="w-4 h-4"></i>
                    </button>
                    <button id="speech-toggle-btn" title="Toggle Auto Read-Aloud" class="p-2 text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-dark-800 rounded-xl transition">
                        <i data-lucide="volume-2" id="speech-icon" class="w-4 h-4"></i>
                    </button>
                </div>
            </header>

            {{-- MESSAGES SCROLL CONTAINER --}}
            <div id="chat-container" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6">

                {{-- Empty State --}}
                <div id="empty-state" class="max-w-2xl mx-auto py-12 sm:py-20 text-center {{ $messages->isNotEmpty() ? 'hidden' : '' }}">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-xl shadow-blue-500/25 mb-4">
                        <i data-lucide="sparkles" class="w-7 h-7"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-2">
                        How can Nexus AI assist you today?
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-8">
                        Generate AI images, analyze uploaded documents, speak via voice, or ask complex coding questions.
                    </p>

                    {{-- Suggestion Chips --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left max-w-xl mx-auto">
                        <button class="prompt-chip group p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-dark-900 hover:border-blue-500 dark:hover:border-blue-500 transition-all text-left shadow-xs hover:shadow-md">
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                <i data-lucide="palette" class="w-3.5 h-3.5 text-purple-500"></i>
                                <span>Generate AI Image</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Generate an image of a cybernetic tiger in a futuristic neon jungle at night.</p>
                        </button>

                        <button class="prompt-chip group p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-dark-900 hover:border-blue-500 dark:hover:border-blue-500 transition-all text-left shadow-xs hover:shadow-md">
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                <i data-lucide="code" class="w-3.5 h-3.5 text-blue-500"></i>
                                <span>Laravel REST Controller</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Create an API controller with FormRequest validation & Resource.</p>
                        </button>

                        <button class="prompt-chip group p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-dark-900 hover:border-blue-500 dark:hover:border-blue-500 transition-all text-left shadow-xs hover:shadow-md">
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-emerald-500"></i>
                                <span>Document & Code Review</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Drop a code file or text doc to find bugs and optimizations.</p>
                        </button>

                        <button class="prompt-chip group p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-dark-900 hover:border-blue-500 dark:hover:border-blue-500 transition-all text-left shadow-xs hover:shadow-md">
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-amber-500"></i>
                                <span>Security Vulnerability Audit</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Review web app security checklist against SQL injection and CSRF.</p>
                        </button>
                    </div>
                </div>

                {{-- Messages List --}}
                <div id="messages-list" class="max-w-3xl lg:max-w-4xl mx-auto space-y-6">
                    @foreach ($messages as $msg)
                        <div class="message-wrapper flex gap-3 sm:gap-4 {{ $msg->role === 'user' ? 'justify-end' : 'justify-start' }}">
                            @if ($msg->role === 'assistant')
                                <div class="h-8 w-8 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                    <i data-lucide="bot" class="w-4 h-4"></i>
                                </div>
                            @endif

                            <div class="max-w-[88%] sm:max-w-[80%] space-y-2">
                                
                                {{-- Render User Attachments if any --}}
                                @if ($msg->role === 'user' && !empty($msg->attachments))
                                    <div class="flex flex-wrap gap-2 justify-end">
                                        @foreach ($msg->attachments as $att)
                                            @if (($att['type'] ?? '') === 'image')
                                                <a href="{{ $att['url'] }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 max-w-[200px] shadow-xs hover:opacity-90 transition">
                                                    <img src="{{ $att['url'] }}" alt="{{ $att['name'] }}" class="h-28 w-auto object-cover">
                                                </a>
                                            @else
                                                <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-dark-800 border border-slate-200 dark:border-slate-700 text-xs">
                                                    <i data-lucide="file" class="w-4 h-4 text-blue-500"></i>
                                                    <span class="truncate max-w-[150px] font-medium">{{ $att['name'] }}</span>
                                                    <span class="text-[10px] text-slate-400">({{ $att['size'] ?? '' }})</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <div class="{{ $msg->role === 'user' ? 'bg-blue-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 shadow-xs text-sm whitespace-pre-wrap break-words' : 'bg-transparent text-slate-800 dark:text-slate-200 prose-chat text-sm assistant-content' }}">
                                    @if ($msg->role === 'assistant')
                                        {!! Str::markdown($msg->content) !!}
                                    @else
                                        {{ $msg->content }}
                                    @endif
                                </div>

                                @if ($msg->role === 'assistant')
                                    <div class="assistant-actions flex items-center gap-2 px-1 text-xs text-slate-400">
                                        <button type="button" onclick="navigator.clipboard.writeText({{ json_encode($msg->content) }}); showToast('Copied to clipboard');" class="flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                                            <i data-lucide="copy" class="w-3.5 h-3.5"></i> Copy
                                        </button>
                                        <button type="button" onclick="readAloud({{ json_encode($msg->content) }})" class="flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                                            <i data-lucide="volume-2" class="w-3.5 h-3.5"></i> Listen
                                        </button>
                                    </div>
                                @endif
                            </div>

                            @if ($msg->role === 'user')
                                <div class="h-8 w-8 rounded-xl bg-slate-800 text-white flex items-center justify-center shrink-0 shadow-sm text-xs font-semibold">
                                    U
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- FLOATING INPUT AREA WITH ATTACHMENT PREVIEW --}}
            <div class="p-3 sm:p-5 bg-gradient-to-t from-white via-white to-transparent dark:from-dark-950 dark:via-dark-950 dark:to-transparent shrink-0">
                <div class="max-w-3xl lg:max-w-4xl mx-auto">

                    {{-- Error banner --}}
                    <div id="error-banner" class="hidden mb-3 p-3 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 text-xs text-red-600 dark:text-red-300 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-red-500"></i>
                            <span id="error-message">Something went wrong.</span>
                        </div>
                        <button id="dismiss-error" class="text-red-400 hover:text-red-600">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    {{-- Chat Input Form --}}
                    <form id="chat-form" class="relative bg-slate-100/80 dark:bg-dark-900 border border-slate-300/80 dark:border-slate-800 rounded-3xl shadow-sm focus-within:border-blue-500 dark:focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 transition-all p-2 sm:p-2.5">
                        
                        {{-- ATTACHMENTS PREVIEW CHIPS BAR (Above Textarea) --}}
                        <div id="attachments-bar" class="hidden flex-wrap gap-2 px-2 pb-2 border-b border-slate-200/60 dark:border-slate-800">
                            <!-- Dynamic attachment chips render here -->
                        </div>

                        {{-- Hidden File Input --}}
                        <input type="file" id="file-input" class="hidden" multiple accept="image/*,.pdf,.txt,.csv,.json,.md,.sql,.php,.js,.ts,.py,.html,.css,.xml,.log">

                        <textarea
                            id="message-input"
                            rows="1"
                            maxlength="10000"
                            placeholder="Ask Nexus AI, attach an image, or drop files... (Shift+Enter for newline)"
                            class="w-full bg-transparent px-3 py-1.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none resize-none max-h-48 overflow-y-auto"
                        ></textarea>

                        <div class="flex items-center justify-between pt-1 px-1">
                            
                            {{-- Attachment & Voice Buttons --}}
                            <div class="flex items-center gap-1">
                                <button
                                    id="attach-btn"
                                    type="button"
                                    title="Upload image, document or code file"
                                    class="p-2 rounded-xl text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 hover:bg-slate-200 dark:hover:bg-dark-800 transition active:scale-95"
                                >
                                    <i data-lucide="paperclip" class="w-4 h-4"></i>
                                </button>

                                <button
                                    id="voice-input-btn"
                                    type="button"
                                    title="Voice Input (Speech-to-Text)"
                                    class="p-2 rounded-xl text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 hover:bg-slate-200 dark:hover:bg-dark-800 transition active:scale-95"
                                >
                                    <i data-lucide="mic" id="mic-icon" class="w-4 h-4"></i>
                                </button>
                                <span id="char-counter" class="text-[10px] text-slate-400 font-mono hidden sm:inline ml-1">0/10000</span>
                            </div>

                            {{-- Send / Stop Action Button --}}
                            <div class="flex items-center gap-2">
                                <button
                                    id="stop-btn"
                                    type="button"
                                    class="hidden px-3 py-1.5 rounded-full bg-slate-300 dark:bg-dark-800 hover:bg-slate-400 dark:hover:bg-dark-700 text-slate-800 dark:text-slate-100 text-xs font-semibold flex items-center gap-1.5 transition"
                                >
                                    <i data-lucide="square" class="w-3 h-3 fill-current"></i>
                                    <span>Stop</span>
                                </button>

                                <button
                                    id="send-btn"
                                    type="submit"
                                    aria-label="Send message"
                                    class="h-8 w-8 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-all shadow-sm shadow-blue-500/25 active:scale-95 disabled:opacity-40"
                                >
                                    <i data-lucide="arrow-up" class="w-4 h-4 stroke-[2.5]"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <p class="text-center text-[11px] text-slate-400 mt-2">
                        Nexus AI can make mistakes. Verify important info.
                    </p>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 transform translate-y-12 opacity-0 transition-all duration-300 pointer-events-none bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-xs font-medium px-4 py-2.5 rounded-2xl shadow-xl flex items-center gap-2">
        <i data-lucide="check" class="w-4 h-4 text-emerald-400 dark:text-emerald-600"></i>
        <span id="toast-text">Success</span>
    </div>

    <script>
        lucide.createIcons();

        // Active State
        let currentConversationId = {{ $activeConversation?->id ? $activeConversation->id : 'null' }};
        let currentModel = '{{ $currentModelKey }}';
        let activeAbortController = null;
        let isTTSAutoPlay = false;
        let speechRecognition = null;
        let isRecording = false;
        let pendingAttachments = [];

        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        // DOM elements
        const chatContainer = document.getElementById('chat-container');
        const messagesList = document.getElementById('messages-list');
        const emptyState = document.getElementById('empty-state');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const sendBtn = document.getElementById('send-btn');
        const stopBtn = document.getElementById('stop-btn');
        const charCounter = document.getElementById('char-counter');
        const errorBanner = document.getElementById('error-banner');
        const errorMessage = document.getElementById('error-message');
        const dismissError = document.getElementById('dismiss-error');
        const newChatBtn = document.getElementById('new-chat-btn');
        const conversationsList = document.getElementById('conversations-list');
        const exportChatBtn = document.getElementById('export-chat-btn');
        const voiceInputBtn = document.getElementById('voice-input-btn');
        const speechToggleBtn = document.getElementById('speech-toggle-btn');
        const speechIcon = document.getElementById('speech-icon');
        const toast = document.getElementById('toast');
        const toastText = document.getElementById('toast-text');

        // Attachment elements
        const attachBtn = document.getElementById('attach-btn');
        const fileInput = document.getElementById('file-input');
        const attachmentsBar = document.getElementById('attachments-bar');
        const appContainer = document.getElementById('app-container');

        // Model Switcher Elements
        const modelSwitcherBtn = document.getElementById('model-switcher-btn');
        const modelMenu = document.getElementById('model-menu');
        const modelDisplayName = document.getElementById('model-display-name');
        const modelDisplayBadge = document.getElementById('model-display-badge');

        // Sidebar Elements
        const sidebar = document.getElementById('sidebar');
        const openSidebarBtn = document.getElementById('open-sidebar-btn');
        const closeSidebarBtn = document.getElementById('close-sidebar-btn');
        const mobileBackdrop = document.getElementById('mobile-backdrop');

        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const themeText = document.getElementById('theme-text');

        // Configure Marked
        marked.setOptions({
            highlight: function(code, lang) {
                if (lang && hljs.getLanguage(lang)) {
                    return hljs.highlight(code, { language: lang }).value;
                }
                return hljs.highlightAuto(code).value;
            },
            breaks: true,
        });

        // Theme init
        function initTheme() {
            const isDark = localStorage.getItem('theme') === 'dark' || 
                (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (isDark) {
                document.documentElement.classList.add('dark');
                themeText.textContent = 'Light mode';
                themeIcon.setAttribute('data-lucide', 'sun');
            } else {
                document.documentElement.classList.remove('dark');
                themeText.textContent = 'Dark mode';
                themeIcon.setAttribute('data-lucide', 'moon');
            }
            lucide.createIcons();
        }

        themeToggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            themeText.textContent = isDark ? 'Light mode' : 'Dark mode';
            themeIcon.setAttribute('data-lucide', isDark ? 'sun' : 'moon');
            lucide.createIcons();
        });

        initTheme();

        // Sidebar Toggles
        function toggleSidebar(show) {
            if (show) {
                sidebar.classList.remove('-translate-x-full');
                mobileBackdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                mobileBackdrop.classList.add('hidden');
            }
        }
        openSidebarBtn.addEventListener('click', () => toggleSidebar(true));
        closeSidebarBtn.addEventListener('click', () => toggleSidebar(false));
        mobileBackdrop.addEventListener('click', () => toggleSidebar(false));

        // MODEL SWITCHER DROPDOWN
        modelSwitcherBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            modelMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!modelMenu.contains(e.target) && !modelSwitcherBtn.contains(e.target)) {
                modelMenu.classList.add('hidden');
            }
        });

        document.querySelectorAll('.model-option').forEach(btn => {
            btn.addEventListener('click', async () => {
                const selectedKey = btn.getAttribute('data-model');
                const selectedName = btn.getAttribute('data-name');
                const selectedBadge = btn.getAttribute('data-badge');

                currentModel = selectedKey;
                modelDisplayName.textContent = selectedName;
                modelDisplayBadge.textContent = selectedBadge;

                document.querySelectorAll('.model-option').forEach(opt => {
                    opt.classList.remove('bg-blue-50/60', 'dark:bg-dark-800/90', 'ring-1', 'ring-blue-500/30');
                    opt.querySelector('.check-icon')?.classList.add('hidden');
                });
                btn.classList.add('bg-blue-50/60', 'dark:bg-dark-800/90', 'ring-1', 'ring-blue-500/30');
                btn.querySelector('.check-icon')?.classList.remove('hidden');

                modelMenu.classList.add('hidden');
                showToast(`Switched to ${selectedName}`);

                if (currentConversationId) {
                    try {
                        await fetch(`/chat/api/conversations/${currentConversationId}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({ model: currentModel }),
                        });
                    } catch (e) {}
                }
            });
        });

        // ============================================
        // ATTACHMENT & FILE UPLOAD SYSTEM (Real AI)
        // ============================================
        attachBtn.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                Array.from(fileInput.files).forEach(uploadFile);
                fileInput.value = '';
            }
        });

        // Drag & Drop handling
        let dragCounter = 0;
        window.addEventListener('dragenter', (e) => {
            e.preventDefault();
            dragCounter++;
            appContainer.classList.add('dragging');
        });

        window.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dragCounter--;
            if (dragCounter <= 0) {
                dragCounter = 0;
                appContainer.classList.remove('dragging');
            }
        });

        window.addEventListener('dragover', (e) => e.preventDefault());

        window.addEventListener('drop', (e) => {
            e.preventDefault();
            dragCounter = 0;
            appContainer.classList.remove('dragging');
            if (e.dataTransfer && e.dataTransfer.files.length > 0) {
                Array.from(e.dataTransfer.files).forEach(uploadFile);
            }
        });

        // Clipboard Paste support (e.g. Snipping tool screenshot Ctrl+V)
        window.addEventListener('paste', (e) => {
            if (e.clipboardData && e.clipboardData.files.length > 0) {
                Array.from(e.clipboardData.files).forEach(uploadFile);
            }
        });

        async function uploadFile(file) {
            const tempId = 'up-' + Date.now() + '-' + Math.random().toString(36).substr(2, 5);
            renderAttachmentChip({ name: file.name, size: (file.size / 1024).toFixed(0) + ' KB', type: 'loading', tempId });

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('/chat/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!response.ok) throw new Error('Upload failed');
                const data = await response.json();

                // Replace loading chip with active chip
                document.getElementById(tempId)?.remove();
                data.tempId = tempId;
                pendingAttachments.push(data);
                renderAttachmentChip(data);
                showToast(`Attached ${data.name}`);
            } catch (err) {
                document.getElementById(tempId)?.remove();
                showError('Could not upload ' + file.name + ' (max 20MB)');
            }
        }

        function renderAttachmentChip(att) {
            attachmentsBar.classList.remove('hidden');
            const chip = document.createElement('div');
            chip.id = att.tempId;
            chip.className = 'flex items-center gap-2 px-2.5 py-1.5 rounded-xl bg-white dark:bg-dark-800 border border-slate-200 dark:border-slate-700 text-xs shadow-xs';

            if (att.type === 'loading') {
                chip.innerHTML = `
                    <div class="w-3.5 h-3.5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="truncate max-w-[120px] font-medium text-slate-500">${att.name}</span>
                `;
            } else if (att.type === 'image') {
                chip.innerHTML = `
                    <img src="${att.url}" class="w-6 h-6 rounded-md object-cover">
                    <span class="truncate max-w-[120px] font-medium">${att.name}</span>
                    <button type="button" class="remove-att-btn text-slate-400 hover:text-red-500 p-0.5"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
                `;
            } else {
                chip.innerHTML = `
                    <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
                    <span class="truncate max-w-[120px] font-medium">${att.name}</span>
                    <button type="button" class="remove-att-btn text-slate-400 hover:text-red-500 p-0.5"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
                `;
            }

            chip.querySelector('.remove-att-btn')?.addEventListener('click', () => {
                chip.remove();
                pendingAttachments = pendingAttachments.filter(item => item.tempId !== att.tempId);
                if (pendingAttachments.length === 0) {
                    attachmentsBar.classList.add('hidden');
                }
            });

            attachmentsBar.appendChild(chip);
            lucide.createIcons();
        }

        function clearAttachments() {
            pendingAttachments = [];
            attachmentsBar.innerHTML = '';
            attachmentsBar.classList.add('hidden');
        }

        // Toast Helper
        function showToast(msg) {
            toastText.textContent = msg;
            toast.classList.remove('translate-y-12', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-12', 'opacity-0');
            }, 2500);
        }

        function showError(msg) {
            errorMessage.textContent = msg;
            errorBanner.classList.remove('hidden');
        }
        dismissError.addEventListener('click', () => errorBanner.classList.add('hidden'));

        function scrollToBottom() {
            chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
        }

        // Auto resize textarea
        messageInput.addEventListener('input', () => {
            messageInput.style.height = 'auto';
            messageInput.style.height = Math.min(messageInput.scrollHeight, 180) + 'px';
            charCounter.textContent = `${messageInput.value.length}/10000`;
        });

        messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                startNewChat();
            }
        });

        window.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                startNewChat();
            }
        });

        // Copy Code
        window.copyCode = function(button) {
            const pre = button.closest('pre');
            const code = pre.querySelector('code').innerText;
            navigator.clipboard.writeText(code).then(() => {
                const orig = button.innerHTML;
                button.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400"></i> Copied!';
                lucide.createIcons();
                setTimeout(() => {
                    button.innerHTML = orig;
                    lucide.createIcons();
                }, 2000);
            });
        };

        // Text to Speech
        window.readAloud = function(text) {
            if (!('speechSynthesis' in window)) return;
            if (window.speechSynthesis.speaking) {
                window.speechSynthesis.cancel();
                return;
            }
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.rate = 1.0;
            window.speechSynthesis.speak(utterance);
        };

        speechToggleBtn.addEventListener('click', () => {
            isTTSAutoPlay = !isTTSAutoPlay;
            speechIcon.classList.toggle('text-blue-600', isTTSAutoPlay);
            showToast(isTTSAutoPlay ? 'Auto Read-Aloud ON' : 'Auto Read-Aloud OFF');
        });

        // Speech-to-Text
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
            speechRecognition = new SpeechRec();
            speechRecognition.continuous = false;
            speechRecognition.interimResults = false;
            speechRecognition.lang = 'en-US';

            speechRecognition.onstart = () => {
                isRecording = true;
                voiceInputBtn.classList.add('text-red-500', 'voice-active');
                showToast('Listening... Speak now');
            };

            speechRecognition.onresult = (e) => {
                const transcript = e.results[0][0].transcript;
                messageInput.value = (messageInput.value + ' ' + transcript).trim();
                messageInput.dispatchEvent(new Event('input'));
            };

            speechRecognition.onend = speechRecognition.onerror = () => {
                isRecording = false;
                voiceInputBtn.classList.remove('text-red-500', 'voice-active');
            };

            voiceInputBtn.addEventListener('click', () => {
                if (isRecording) speechRecognition.stop();
                else speechRecognition.start();
            });
        }

        // Suggestion prompt chips
        document.querySelectorAll('.prompt-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                const text = chip.querySelector('p').textContent;
                messageInput.value = text;
                messageInput.dispatchEvent(new Event('input'));
                chatForm.dispatchEvent(new Event('submit'));
            });
        });

        // Code block decoration
        function decorateCodeBlocks(container) {
            container.querySelectorAll('pre').forEach(pre => {
                if (pre.querySelector('.code-header')) return;
                const code = pre.querySelector('code');
                const langMatch = code ? code.className.match(/language-(\w+)/) : null;
                const lang = langMatch ? langMatch[1] : 'code';

                const header = document.createElement('div');
                header.className = 'code-header flex items-center justify-between px-3.5 py-1.5 bg-slate-900 text-slate-400 text-xs border-b border-white/10 select-none';
                header.innerHTML = `
                    <span class="font-mono text-[11px] uppercase tracking-wider text-slate-400">${lang}</span>
                    <button type="button" onclick="copyCode(this)" class="flex items-center gap-1 text-[11px] hover:text-white transition">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        <span>Copy</span>
                    </button>
                `;
                pre.prepend(header);
            });
            lucide.createIcons();
        }

        // Append User Message to UI
        function appendUserMessage(text, attachments) {
            emptyState.classList.add('hidden');
            const wrap = document.createElement('div');
            wrap.className = 'message-wrapper flex gap-3 sm:gap-4 justify-end';

            let attachmentsHtml = '';
            if (attachments && attachments.length > 0) {
                attachmentsHtml = '<div class="flex flex-wrap gap-2 justify-end mb-2">';
                attachments.forEach(att => {
                    if (att.type === 'image') {
                        attachmentsHtml += `
                            <a href="${att.url}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 max-w-[200px] shadow-xs hover:opacity-90 transition">
                                <img src="${att.url}" alt="${att.name}" class="h-28 w-auto object-cover">
                            </a>`;
                    } else {
                        attachmentsHtml += `
                            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-dark-800 border border-slate-200 dark:border-slate-700 text-xs">
                                <i data-lucide="file" class="w-4 h-4 text-blue-500"></i>
                                <span class="truncate max-w-[150px] font-medium">${att.name}</span>
                                <span class="text-[10px] text-slate-400">(${att.size})</span>
                            </div>`;
                    }
                });
                attachmentsHtml += '</div>';
            }

            wrap.innerHTML = `
                <div class="max-w-[88%] sm:max-w-[80%] space-y-2">
                    ${attachmentsHtml}
                    ${text ? `<div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 shadow-xs text-sm whitespace-pre-wrap break-words">${escapeHtml(text)}</div>` : ''}
                </div>
                <div class="h-8 w-8 rounded-xl bg-slate-800 text-white flex items-center justify-center shrink-0 shadow-sm text-xs font-semibold">
                    U
                </div>
            `;
            messagesList.appendChild(wrap);
            lucide.createIcons();
            scrollToBottom();
        }

        function escapeHtml(string) {
            const pre = document.createElement('pre');
            pre.textContent = string;
            return pre.innerHTML;
        }

        // Create Assistant Bubble
        function createAssistantBubble() {
            const wrap = document.createElement('div');
            wrap.className = 'message-wrapper flex gap-3 sm:gap-4 justify-start';
            wrap.innerHTML = `
                <div class="h-8 w-8 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                    <i data-lucide="bot" class="w-4 h-4"></i>
                </div>
                <div class="max-w-[88%] sm:max-w-[80%]">
                    <div class="assistant-content bg-transparent text-slate-800 dark:text-slate-200 prose-chat text-sm">
                        <span class="cursor-blink"></span>
                    </div>
                    <div class="assistant-actions hidden items-center gap-2 mt-2 px-1 text-xs text-slate-400">
                        <button type="button" class="copy-msg-btn flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i> Copy
                        </button>
                        <button type="button" class="listen-msg-btn flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                            <i data-lucide="volume-2" class="w-3.5 h-3.5"></i> Listen
                        </button>
                    </div>
                </div>
            `;
            messagesList.appendChild(wrap);
            lucide.createIcons();
            scrollToBottom();
            return {
                wrap,
                content: wrap.querySelector('.assistant-content'),
                actions: wrap.querySelector('.assistant-actions'),
                copyBtn: wrap.querySelector('.copy-msg-btn'),
                listenBtn: wrap.querySelector('.listen-msg-btn'),
            };
        }

        // Submission with Streaming & Multimodal Attachments
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = messageInput.value.trim();
            const attachmentsToSend = [...pendingAttachments];

            if (!message && attachmentsToSend.length === 0) return;

            errorBanner.classList.add('hidden');
            appendUserMessage(message, attachmentsToSend);

            messageInput.value = '';
            messageInput.style.height = 'auto';
            charCounter.textContent = '0/10000';
            clearAttachments();

            sendBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
            messageInput.disabled = true;

            const bubble = createAssistantBubble();
            let accumulatedText = '';
            activeAbortController = new AbortController();

            const payload = {
                message: message,
                conversation_id: currentConversationId,
                model: currentModel,
                attachments: attachmentsToSend,
            };

            try {
                const response = await fetch('/chat/stream', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'text/event-stream',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(payload),
                    signal: activeAbortController.signal,
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const reader = response.body.getReader();
                const decoder = new TextDecoder('utf-8');
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split('\n');
                    buffer = lines.pop();

                    for (const line of lines) {
                        const trimmed = line.trim();
                        if (!trimmed || !trimmed.startsWith('data:')) continue;
                        const dataStr = trimmed.slice(5).trim();

                        if (dataStr === '[DONE]') break;

                        try {
                            const parsed = JSON.parse(dataStr);
                            
                            if (parsed.conversation_id && !currentConversationId) {
                                currentConversationId = parsed.conversation_id;
                                history.pushState({}, '', `/chat/c/${currentConversationId}`);
                                updateSidebarConversations(parsed.conversation_id, parsed.title);
                            }

                            if (parsed.text) {
                                accumulatedText += parsed.text;
                                bubble.content.innerHTML = DOMPurify.sanitize(marked.parse(accumulatedText)) + '<span class="cursor-blink"></span>';
                                decorateCodeBlocks(bubble.content);
                                scrollToBottom();
                            }
                        } catch (err) {}
                    }
                }

                bubble.content.innerHTML = DOMPurify.sanitize(marked.parse(accumulatedText || 'No response.'));
                decorateCodeBlocks(bubble.content);
                bubble.actions.classList.remove('hidden');
                bubble.actions.classList.add('flex');

                bubble.copyBtn.addEventListener('click', () => {
                    navigator.clipboard.writeText(accumulatedText);
                    showToast('Message copied to clipboard');
                });
                bubble.listenBtn.addEventListener('click', () => readAloud(accumulatedText));

                if (isTTSAutoPlay) readAloud(accumulatedText);

            } catch (err) {
                if (err.name === 'AbortError') {
                    bubble.content.innerHTML = DOMPurify.sanitize(marked.parse(accumulatedText + '\n\n*(Generation stopped)*'));
                } else {
                    console.error('Chat error:', err);
                    bubble.content.innerHTML = '<p class="text-slate-600 dark:text-slate-300 text-sm">I apologize, but I am unable to process your request at the moment. Please try again in a few moments.</p>';
                }
            } finally {
                sendBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
                messageInput.disabled = false;
                activeAbortController = null;
                messageInput.focus();
                lucide.createIcons();
            }
        });

        // Stop Button
        stopBtn.addEventListener('click', () => {
            if (activeAbortController) activeAbortController.abort();
        });

        // NEW CHAT BUTTON
        async function startNewChat() {
            currentConversationId = null;
            messagesList.innerHTML = '';
            emptyState.classList.remove('hidden');
            clearAttachments();
            history.pushState({}, '', '/chat');

            document.querySelectorAll('.conversation-item').forEach(el => {
                el.classList.remove('bg-slate-100', 'dark:bg-dark-800', 'text-slate-900', 'dark:text-white', 'font-medium', 'shadow-xs');
                el.classList.add('text-slate-600', 'dark:text-slate-300');
            });

            messageInput.value = '';
            messageInput.focus();
            toggleSidebar(false);
            showToast('Started new chat');
        }

        newChatBtn.addEventListener('click', startNewChat);

        // LOAD CONVERSATION
        window.loadConversation = async function(id) {
            try {
                const res = await fetch(`/chat/api/conversations/${id}`);
                const data = await res.json();
                currentConversationId = data.conversation.id;

                if (data.conversation.model) {
                    currentModel = data.conversation.model;
                    const opt = document.querySelector(`.model-option[data-model="${currentModel}"]`);
                    if (opt) {
                        modelDisplayName.textContent = opt.getAttribute('data-name');
                        modelDisplayBadge.textContent = opt.getAttribute('data-badge');
                    }
                }

                messagesList.innerHTML = '';
                if (data.messages.length > 0) {
                    emptyState.classList.add('hidden');
                    data.messages.forEach(msg => {
                        const isUser = msg.role === 'user';
                        const wrap = document.createElement('div');
                        wrap.className = 'message-wrapper flex gap-3 sm:gap-4 ' + (isUser ? 'justify-end' : 'justify-start');

                        if (!isUser) {
                            wrap.innerHTML = `
                                <div class="h-8 w-8 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                    <i data-lucide="bot" class="w-4 h-4"></i>
                                </div>
                                <div class="max-w-[88%] sm:max-w-[80%]">
                                    <div class="assistant-content bg-transparent text-slate-800 dark:text-slate-200 prose-chat text-sm">
                                        ${DOMPurify.sanitize(marked.parse(msg.content))}
                                    </div>
                                    <div class="assistant-actions flex items-center gap-2 mt-2 px-1 text-xs text-slate-400">
                                        <button type="button" class="copy-msg-btn flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                                            <i data-lucide="copy" class="w-3.5 h-3.5"></i> Copy
                                        </button>
                                        <button type="button" class="listen-msg-btn flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                                            <i data-lucide="volume-2" class="w-3.5 h-3.5"></i> Listen
                                        </button>
                                    </div>
                                </div>
                            `;
                            wrap.querySelector('.copy-msg-btn').onclick = () => {
                                navigator.clipboard.writeText(msg.content);
                                showToast('Copied to clipboard');
                            };
                            wrap.querySelector('.listen-msg-btn').onclick = () => readAloud(msg.content);
                        } else {
                            let attHtml = '';
                            if (msg.attachments && msg.attachments.length > 0) {
                                attHtml = '<div class="flex flex-wrap gap-2 justify-end mb-2">';
                                msg.attachments.forEach(att => {
                                    if (att.type === 'image') {
                                        attHtml += `
                                            <a href="${att.url}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 max-w-[200px] shadow-xs hover:opacity-90 transition">
                                                <img src="${att.url}" alt="${att.name}" class="h-28 w-auto object-cover">
                                            </a>`;
                                    } else {
                                        attHtml += `
                                            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-dark-800 border border-slate-200 dark:border-slate-700 text-xs">
                                                <i data-lucide="file" class="w-4 h-4 text-blue-500"></i>
                                                <span class="truncate max-w-[150px] font-medium">${att.name}</span>
                                                <span class="text-[10px] text-slate-400">(${att.size})</span>
                                            </div>`;
                                    }
                                });
                                attHtml += '</div>';
                            }

                            wrap.innerHTML = `
                                <div class="max-w-[88%] sm:max-w-[80%] space-y-2">
                                    ${attHtml}
                                    <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 shadow-xs text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.content)}</div>
                                </div>
                                <div class="h-8 w-8 rounded-xl bg-slate-800 text-white flex items-center justify-center shrink-0 shadow-sm text-xs font-semibold">
                                    U
                                </div>
                            `;
                        }
                        messagesList.appendChild(wrap);
                    });
                    decorateCodeBlocks(messagesList);
                    scrollToBottom();
                } else {
                    emptyState.classList.remove('hidden');
                }

                document.querySelectorAll('.conversation-item').forEach(el => {
                    const match = el.getAttribute('data-id') == id;
                    el.classList.toggle('bg-slate-100', match);
                    el.classList.toggle('dark:bg-dark-800', match);
                    el.classList.toggle('text-slate-900', match);
                    el.classList.toggle('dark:text-white', match);
                    el.classList.toggle('font-medium', match);
                    el.classList.toggle('shadow-xs', match);
                });

                history.pushState({}, '', `/chat/c/${id}`);
                toggleSidebar(false);
                lucide.createIcons();
            } catch (err) {
                showToast('Failed to load chat');
            }
        };

        // RENAME CONVERSATION
        window.renameConversation = async function(e, id) {
            e.stopPropagation();
            const currentTitle = document.querySelector(`.conversation-item[data-id="${id}"] .conversation-title`)?.textContent || 'Chat';
            const newTitle = prompt('Enter new conversation title:', currentTitle);
            if (!newTitle || newTitle === currentTitle) return;

            try {
                const res = await fetch(`/chat/api/conversations/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ title: newTitle.trim() }),
                });
                if (res.ok) {
                    const titleEl = document.querySelector(`.conversation-item[data-id="${id}"] .conversation-title`);
                    if (titleEl) titleEl.textContent = newTitle.trim();
                    showToast('Title updated');
                }
            } catch (e) {
                showToast('Failed to rename conversation');
            }
        };

        // DELETE CONVERSATION
        window.deleteConversation = async function(e, id) {
            e.stopPropagation();
            if (!confirm('Are you sure you want to delete this chat permanently?')) return;

            try {
                const res = await fetch(`/chat/api/conversations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                });
                if (res.ok) {
                    document.querySelector(`.conversation-item[data-id="${id}"]`)?.remove();
                    if (currentConversationId == id) {
                        startNewChat();
                    }
                    showToast('Chat deleted');
                }
            } catch (e) {
                showToast('Failed to delete chat');
            }
        };

        // UPDATE SIDEBAR
        function updateSidebarConversations(id, title) {
            document.getElementById('no-chats-placeholder')?.remove();
            let existing = document.querySelector(`.conversation-item[data-id="${id}"]`);
            if (existing) {
                existing.querySelector('.conversation-title').textContent = title;
                return;
            }

            const item = document.createElement('div');
            item.className = 'conversation-item group relative flex items-center justify-between px-3 py-2.5 rounded-xl text-xs cursor-pointer transition bg-slate-100 dark:bg-dark-800 text-slate-900 dark:text-white font-medium shadow-xs';
            item.setAttribute('data-id', id);
            item.innerHTML = `
                <div class="flex items-center gap-2.5 min-w-0 flex-1" onclick="loadConversation(${id})">
                    <i data-lucide="message-square" class="w-3.5 h-3.5 shrink-0 text-slate-400"></i>
                    <span class="truncate conversation-title">${title}</span>
                </div>
                <div class="opacity-0 group-hover:opacity-100 flex items-center gap-1 transition-opacity">
                    <button type="button" onclick="renameConversation(event, ${id})" title="Rename" class="p-1 hover:text-blue-500 text-slate-400">
                        <i data-lucide="edit-3" class="w-3 h-3"></i>
                    </button>
                    <button type="button" onclick="deleteConversation(event, ${id})" title="Delete" class="p-1 hover:text-red-500 text-slate-400">
                        <i data-lucide="trash" class="w-3 h-3"></i>
                    </button>
                </div>
            `;
            conversationsList.prepend(item);
            lucide.createIcons();
        }

        // Export Conversation as Markdown
        exportChatBtn.addEventListener('click', () => {
            const wrappers = messagesList.querySelectorAll('.message-wrapper');
            if (wrappers.length === 0) {
                showToast('No messages to export');
                return;
            }

            let markdown = `# Nexus AI Conversation Export\n*Exported on: ${new Date().toLocaleString()}*\n\n---\n\n`;
            wrappers.forEach(w => {
                const isUser = w.classList.contains('justify-end');
                const role = isUser ? 'User' : 'Nexus AI';
                const text = isUser ? w.querySelector('.whitespace-pre-wrap')?.textContent : w.querySelector('.assistant-content')?.innerText;
                markdown += `### ${role}:\n${text}\n\n`;
            });

            const blob = new Blob([markdown], { type: 'text/markdown;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `nexus-chat-${Date.now()}.md`;
            a.click();
            URL.revokeObjectURL(url);
            showToast('Conversation exported (.md)');
        });

        // Initialize code decoration
        decorateCodeBlocks(messagesList);
        messageInput.focus();
    </script>
</body>
</html>
