<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nexus AI Studio &mdash; Dual Engine Intelligence (Gemini & DeepSeek)</title>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS with custom configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'monospace'],
                    },
                    colors: {
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            800: '#151922',
                            850: '#11141c',
                            900: '#0c0e14',
                            950: '#07080c',
                        },
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                        deepseek: {
                            500: '#0ea5e9',
                            600: '#0284c7',
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 25px -5px rgba(59, 130, 246, 0.25)',
                        'glow-cyan': '0 0 25px -5px rgba(14, 165, 233, 0.25)',
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
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.25); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.45); }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .prose-chat pre {
            margin: 0.85rem 0;
            padding: 0;
            border-radius: 0.95rem;
            overflow: hidden;
            background: #0f121a !important;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.3);
        }
        .prose-chat pre code {
            padding: 1.15rem;
            font-size: 0.84rem;
            line-height: 1.6;
            display: block;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
        }
        .prose-chat code:not(pre code) {
            background: rgba(125, 125, 125, 0.12);
            color: #3b82f6;
            padding: 0.18rem 0.4rem;
            border-radius: 0.4rem;
            font-size: 0.85em;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 500;
        }
        .dark .prose-chat code:not(pre code) {
            color: #60a5fa;
            background: rgba(255, 255, 255, 0.08);
        }
        .prose-chat p { margin-bottom: 0.85rem; line-height: 1.7; }
        .prose-chat p:last-child { margin-bottom: 0; }
        .prose-chat ul { list-style-type: disc; margin-left: 1.35rem; margin-bottom: 0.75rem; line-height: 1.65; }
        .prose-chat ol { list-style-type: decimal; margin-left: 1.35rem; margin-bottom: 0.75rem; line-height: 1.65; }
        .prose-chat li { margin-bottom: 0.25rem; }
        .prose-chat blockquote {
            border-left: 3px solid #3b82f6;
            background: rgba(59, 130, 246, 0.05);
            padding: 0.75rem 1.15rem;
            border-radius: 0 0.65rem 0.65rem 0;
            margin: 0.95rem 0;
            font-style: italic;
        }
        .dark .prose-chat blockquote {
            background: rgba(59, 130, 246, 0.08);
        }
        .prose-chat h1, .prose-chat h2, .prose-chat h3, .prose-chat h4 {
            font-weight: 700;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.015em;
        }
        .prose-chat h1 { font-size: 1.45rem; }
        .prose-chat h2 { font-size: 1.25rem; }
        .prose-chat h3 { font-size: 1.1rem; }
        .prose-chat table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.95rem 0;
            font-size: 0.85rem;
            border-radius: 0.6rem;
            overflow: hidden;
        }
        .prose-chat th, .prose-chat td {
            border: 1px solid rgba(148, 163, 184, 0.2);
            padding: 0.6rem 0.95rem;
            text-align: left;
        }
        .prose-chat th { background: rgba(148, 163, 184, 0.1); font-weight: 600; }
        .prose-chat img {
            border-radius: 1.15rem;
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
            box-shadow: 0 12px 30px -8px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(148, 163, 184, 0.2);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            background: rgba(148, 163, 184, 0.08);
            display: block;
        }
        .prose-chat img:hover {
            transform: scale(1.015);
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.35);
        }

        .cursor-blink {
            display: inline-block;
            width: 7px;
            height: 16px;
            background-color: #3b82f6;
            vertical-align: -2px;
            margin-left: 2px;
            border-radius: 1px;
            animation: blink 0.8s infinite;
        }
        @keyframes blink { 0%, 50% { opacity: 1; } 51%, 100% { opacity: 0; } }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.3; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .voice-active { animation: pulse-ring 1.5s infinite ease-in-out; }

        .dropzone-overlay { display: none; }
        .dragging .dropzone-overlay { display: flex; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-900 dark:bg-dark-950 dark:text-slate-100 flex flex-col font-sans transition-colors duration-200 antialiased overflow-hidden select-none sm:select-auto">

    <div class="flex h-full w-full relative" id="app-container">

        {{-- DRAG & DROP OVERLAY --}}
        <div class="dropzone-overlay fixed inset-0 z-50 bg-blue-600/20 backdrop-blur-md border-4 border-dashed border-blue-500 items-center justify-center p-6 text-center pointer-events-none transition-all">
            <div class="p-8 rounded-3xl bg-white dark:bg-dark-900 shadow-2xl flex flex-col items-center gap-3 border border-slate-200 dark:border-slate-800">
                <div class="h-16 w-16 rounded-2xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center animate-bounce">
                    <i data-lucide="upload-cloud" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold">Drop files here to analyze</h3>
                <p class="text-xs text-slate-500">Multimodal vision for Images, PDFs, Code, TXT, CSV, Markdown</p>
            </div>
        </div>

        {{-- TOAST NOTIFICATION --}}
        <div id="toast" class="fixed bottom-24 left-1/2 transform -translate-x-1/2 z-50 hidden transition-all duration-300">
            <div class="px-4 py-2.5 rounded-full bg-slate-900/90 dark:bg-white/95 text-white dark:text-slate-900 text-xs font-semibold shadow-2xl flex items-center gap-2 backdrop-blur-md border border-white/10 dark:border-black/10">
                <i data-lucide="info" class="w-4 h-4 text-blue-400 dark:text-blue-600"></i>
                <span id="toast-message">Notification</span>
            </div>
        </div>

        {{-- MOBILE OVERLAY BACKDROP --}}
        <div id="mobile-backdrop" class="fixed inset-0 bg-black/60 z-30 hidden lg:hidden backdrop-blur-xs transition-opacity"></div>

        {{-- MODERN COLLAPSIBLE SIDEBAR --}}
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-72 bg-white dark:bg-dark-900 border-r border-slate-200/80 dark:border-slate-800/80 flex flex-col transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out shrink-0">
            
            {{-- Top Branding & New Chat --}}
            <div class="p-3.5 border-b border-slate-200/80 dark:border-slate-800/80 flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <a href="/" class="flex items-center gap-2.5 group">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-violet-600 text-white flex items-center justify-center shadow-md shadow-blue-500/25 group-hover:scale-105 transition-transform ring-2 ring-blue-500/15">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="font-bold text-sm tracking-tight flex items-center gap-1.5">
                                <span>Nexus AI</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">STUDIO</span>
                            </span>
                            <p class="text-[10px] text-slate-400">Gemini • ChatGPT • DeepSeek • Claude • Grok</p>
                        </div>
                    </a>
                    
                    {{-- Close Button for Mobile / Collapse for Desktop --}}
                    <div class="flex items-center gap-1">
                        <button id="close-sidebar-btn" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                {{-- New Chat Button --}}
                <button id="new-chat-btn" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm shadow-blue-600/30 transition-all active:scale-[0.98]">
                    <div class="flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
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
                    <div class="conversation-item group relative flex items-center justify-between px-3 py-2.5 rounded-xl text-xs cursor-pointer transition {{ ($activeConversation && $activeConversation->id === $conv->id) ? 'bg-slate-100 dark:bg-dark-800 text-slate-900 dark:text-white font-medium shadow-xs ring-1 ring-slate-200 dark:ring-slate-700/60' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-dark-800/60' }}" data-id="{{ $conv->id }}">
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

            {{-- Sidebar Footer: Engine Status & Theme Toggle --}}
            <div class="p-3 border-t border-slate-200/80 dark:border-slate-800/80 space-y-1.5 text-xs">
                <button id="theme-toggle" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800 transition">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="moon" id="theme-icon" class="w-4 h-4"></i>
                        <span id="theme-text">Dark mode</span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">Toggle</span>
                </button>
                <div class="flex items-center justify-between px-2 pt-1 text-[11px] text-slate-400">
                    <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> 5 AI Engines Ready
                    </span>
                    <a href="/" class="hover:text-slate-600 dark:hover:text-slate-200 font-medium">Home &rarr;</a>
                </div>
            </div>
        </aside>

        {{-- MAIN CHAT WINDOW --}}
        <main class="flex-1 flex flex-col h-full bg-white dark:bg-dark-950 overflow-hidden relative">

            {{-- TOP HEADER: Sidebar Toggle, Model Switcher & Tools --}}
            <header class="h-14 border-b border-slate-200/80 dark:border-slate-800/80 px-3 sm:px-5 flex items-center justify-between z-20 shrink-0 bg-white/95 dark:bg-dark-950/95 backdrop-blur-md">
                <div class="flex items-center gap-2 sm:gap-3">
                    
                    {{-- Sidebar Toggle for Mobile & Desktop --}}
                    <button id="toggle-sidebar-btn" title="Toggle Sidebar" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800 transition active:scale-95">
                        <i data-lucide="panel-left" class="w-4 h-4"></i>
                    </button>

                    {{-- MULTI-PROVIDER MODEL SWITCHER POPOVER (GEMINI, CHATGPT, DEEPSEEK, CLAUDE, GROK) --}}
                    <div class="relative inline-block text-left" id="model-dropdown-container">
                        @php
                            $currentModelKey = $activeConversation?->model ?? $defaultModel;
                            if (str_starts_with($currentModelKey, 'gemini-2.') || str_starts_with($currentModelKey, 'gemini-1.')) {
                                $currentModelKey = 'gemini-3.1-flash-lite';
                            }
                            if (!isset($models[$currentModelKey])) {
                                $currentModelKey = 'auto';
                            }

                            $currentModelMeta = $models[$currentModelKey] ?? $models['auto'] ?? reset($models);
                            $pBadge = $currentModelMeta['provider_badge'] ?? 'Auto-Pilot';
                            
                            $pStyleMap = [
                                'Auto-Pilot' => ['badge' => 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300', 'icon' => 'sparkles', 'iconColor' => 'text-purple-500'],
                                'Google' => ['badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300', 'icon' => 'sparkles', 'iconColor' => 'text-blue-500'],
                                'DeepSeek' => ['badge' => 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300', 'icon' => 'cpu', 'iconColor' => 'text-sky-500'],
                                'OpenAI' => ['badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300', 'icon' => 'bot', 'iconColor' => 'text-emerald-500'],
                                'Claude' => ['badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300', 'icon' => 'flame', 'iconColor' => 'text-amber-500'],
                                'xAI' => ['badge' => 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300', 'icon' => 'zap', 'iconColor' => 'text-rose-500'],
                                'Qwen' => ['badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300', 'icon' => 'code-2', 'iconColor' => 'text-emerald-500'],
                                'Meta' => ['badge' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300', 'icon' => 'layers', 'iconColor' => 'text-indigo-500'],
                                'Web Search' => ['badge' => 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300', 'icon' => 'globe', 'iconColor' => 'text-sky-500'],
                            ];
                            $activePStyle = $pStyleMap[$pBadge] ?? $pStyleMap['Auto-Pilot'];
                        @endphp
                        <button
                            id="model-switcher-btn"
                            type="button"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-dark-900 hover:bg-slate-100 dark:hover:bg-dark-800 transition border border-slate-200/80 dark:border-slate-800 shadow-xs active:scale-95"
                        >
                        
                            <span class="flex items-center gap-1.5">
                                <span id="model-display-icon">
                                    <i data-lucide="{{ $activePStyle['icon'] }}" class="w-3.5 h-3.5 {{ $activePStyle['iconColor'] }}"></i>
                                </span>
                                <span id="model-display-name">{{ $currentModelMeta['name'] }}</span>
                            </span>
                            <span id="model-display-badge" class="text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded {{ $activePStyle['badge'] }}">
                                {{ $currentModelMeta['badge'] }}
                            </span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>

                        <div
                            id="model-menu"
                            class="hidden absolute left-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white dark:bg-dark-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-2 z-50 transition-all focus:outline-none"
                        >
                            <div class="px-3 py-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span>Multi-Engine Intelligence</span>
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-50 text-purple-600 dark:bg-dark-800 dark:text-purple-400 font-mono">Dynamic AI Router Active</span>
                            </div>

                            <div class="max-h-[460px] overflow-y-auto space-y-3 pt-2 px-1">
                                {{-- 0. DYNAMIC SMART ROUTER (AUTO-PILOT) --}}
                                <div class="pb-2 border-b border-slate-100 dark:border-slate-800/80">
                                    <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="sparkles" class="w-3 h-3 text-purple-500"></i>
                                            <span>Smart Auto-Pilot (Default)</span>
                                        </span>
                                        <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">Auto Task Router</span>
                                    </div>
                                    <div class="pt-1">
                                        <button
                                            type="button"
                                            class="model-option w-full flex items-start gap-3 p-2.5 rounded-xl hover:bg-purple-50/50 dark:hover:bg-dark-800 text-left transition group {{ $currentModelKey === 'auto' ? 'bg-purple-50/70 dark:bg-dark-800/90 ring-1 ring-purple-500/30' : '' }}"
                                            data-model="auto"
                                            data-name="Auto (Smart Engine)"
                                            data-badge="AI Selects Best"
                                            data-provider="auto"
                                        >
                                            <div class="h-8 w-8 rounded-lg bg-gradient-to-tr from-purple-500 via-indigo-500 to-blue-500 flex items-center justify-center shrink-0 text-white mt-0.5 shadow-sm group-hover:scale-105 transition-transform">
                                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Auto (Smart Engine)</span>
                                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                                                        Recommended
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">
                                                    Dynamically evaluates requirement & routes to Claude (Web & Laravel), DeepSeek (Algorithms & Logic), Gemini (Vision), or Grok.
                                                </p>
                                            </div>
                                            <i data-lucide="check" class="check-icon w-4 h-4 text-purple-500 shrink-0 self-center {{ $currentModelKey === 'auto' ? '' : 'hidden' }}"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- 1. GOOGLE GEMINI MODELS --}}
                                <div>
                                    <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="sparkles" class="w-3 h-3"></i>
                                            <span>Google Gemini</span>
                                        </span>
                                        <span class="text-[9px] font-normal text-slate-400">Official API</span>
                                    </div>
                                    <div class="space-y-1 pt-1">
                                        @foreach (['gemini-3.1-flash-lite', 'gemini-3.5-flash-lite', 'gemini-4-argon', 'gemini-3.8-flash'] as $key)
                                            @php $meta = $models[$key] ?? null; @endphp
                                            @continue(!$meta)
                                            <button
                                                type="button"
                                                class="model-option w-full flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-dark-800 text-left transition group {{ $key === $currentModelKey ? 'bg-blue-50/60 dark:bg-dark-800/90 ring-1 ring-blue-500/30' : '' }}"
                                                data-model="{{ $key }}"
                                                data-name="{{ $meta['name'] }}"
                                                data-badge="{{ $meta['badge'] }}"
                                                data-provider="gemini"
                                            >
                                                <div class="h-8 w-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center shrink-0 text-blue-600 dark:text-blue-300 mt-0.5 group-hover:scale-105 transition-transform">
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
                                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-blue-100/70 text-blue-700 dark:bg-dark-800 dark:text-blue-300">
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

                                {{-- 2. OPENAI CHATGPT --}}
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                    <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="bot" class="w-3 h-3"></i>
                                            <span>OpenAI ChatGPT</span>
                                        </span>
                                        <span class="text-[9px] font-normal text-slate-400">via OpenRouter</span>
                                    </div>
                                    <div class="space-y-1 pt-1">
                                        @foreach (['gpt-4o-mini'] as $key)
                                            @php $meta = $models[$key] ?? null; @endphp
                                            @continue(!$meta)
                                            <button
                                                type="button"
                                                class="model-option w-full flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-dark-800 text-left transition group {{ $key === $currentModelKey ? 'bg-emerald-50/60 dark:bg-dark-800/90 ring-1 ring-emerald-500/30' : '' }}"
                                                data-model="{{ $key }}"
                                                data-name="{{ $meta['name'] }}"
                                                data-badge="{{ $meta['badge'] }}"
                                                data-provider="openai"
                                            >
                                                <div class="h-8 w-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center shrink-0 text-emerald-600 dark:text-emerald-300 mt-0.5 group-hover:scale-105 transition-transform">
                                                    <i data-lucide="bot" class="w-4 h-4 text-emerald-500"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ $meta['name'] }}</span>
                                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                                            {{ $meta['badge'] }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">
                                                        {{ $meta['desc'] }}
                                                    </p>
                                                </div>
                                                <i data-lucide="check" class="check-icon w-4 h-4 text-emerald-500 shrink-0 self-center {{ $key === $currentModelKey ? '' : 'hidden' }}"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- 3. ANTHROPIC CLAUDE --}}
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                    <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="flame" class="w-3 h-3"></i>
                                            <span>Anthropic Claude</span>
                                        </span>
                                        <span class="text-[9px] font-normal text-slate-400">via OpenRouter</span>
                                    </div>
                                    <div class="space-y-1 pt-1">
                                        @foreach (['claude-sonnet-5', 'claude-haiku-5.5'] as $key)
                                            @php $meta = $models[$key] ?? null; @endphp
                                            @continue(!$meta)
                                            <button
                                                type="button"
                                                class="model-option w-full flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-dark-800 text-left transition group {{ $key === $currentModelKey ? 'bg-amber-50/60 dark:bg-dark-800/90 ring-1 ring-amber-500/30' : '' }}"
                                                data-model="{{ $key }}"
                                                data-name="{{ $meta['name'] }}"
                                                data-badge="{{ $meta['badge'] }}"
                                                data-provider="claude"
                                            >
                                                <div class="h-8 w-8 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center shrink-0 text-amber-600 dark:text-amber-300 mt-0.5 group-hover:scale-105 transition-transform">
                                                    @if (str_contains($key, 'sonnet'))
                                                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                                                    @else
                                                        <i data-lucide="zap" class="w-4 h-4 text-orange-500"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ $meta['name'] }}</span>
                                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                                                            {{ $meta['badge'] }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">
                                                        {{ $meta['desc'] }}
                                                    </p>
                                                </div>
                                                <i data-lucide="check" class="check-icon w-4 h-4 text-amber-500 shrink-0 self-center {{ $key === $currentModelKey ? '' : 'hidden' }}"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- 4. DEEPSEEK AI --}}
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                    <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="cpu" class="w-3 h-3"></i>
                                            <span>DeepSeek AI</span>
                                        </span>
                                        <span class="text-[9px] font-normal text-slate-400">V3 & R1</span>
                                    </div>
                                    <div class="space-y-1 pt-1">
                                        @foreach (['deepseek-chat', 'deepseek-reasoner'] as $key)
                                            @php $meta = $models[$key] ?? null; @endphp
                                            @continue(!$meta)
                                            <button
                                                type="button"
                                                class="model-option w-full flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-dark-800 text-left transition group {{ $key === $currentModelKey ? 'bg-sky-50/60 dark:bg-dark-800/90 ring-1 ring-sky-500/30' : '' }}"
                                                data-model="{{ $key }}"
                                                data-name="{{ $meta['name'] }}"
                                                data-badge="{{ $meta['badge'] }}"
                                                data-provider="deepseek"
                                            >
                                                <div class="h-8 w-8 rounded-lg bg-sky-50 dark:bg-sky-950/40 flex items-center justify-center shrink-0 text-sky-600 dark:text-sky-300 mt-0.5 group-hover:scale-105 transition-transform">
                                                    @if ($key === 'deepseek-reasoner')
                                                        <i data-lucide="brain" class="w-4 h-4 text-teal-500"></i>
                                                    @else
                                                        <i data-lucide="cpu" class="w-4 h-4 text-sky-500"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ $meta['name'] }}</span>
                                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300">
                                                            {{ $meta['badge'] }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">
                                                        {{ $meta['desc'] }}
                                                    </p>
                                                </div>
                                                <i data-lucide="check" class="check-icon w-4 h-4 text-sky-500 shrink-0 self-center {{ $key === $currentModelKey ? '' : 'hidden' }}"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- 5. xAI GROK --}}
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                    <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="zap" class="w-3 h-3"></i>
                                            <span>xAI Grok</span>
                                        </span>
                                        <span class="text-[9px] font-normal text-slate-400">via OpenRouter</span>
                                    </div>
                                    <div class="space-y-1 pt-1">
                                        @foreach (['grok-4.5'] as $key)
                                            @php $meta = $models[$key] ?? null; @endphp
                                            @continue(!$meta)
                                            <button
                                                type="button"
                                                class="model-option w-full flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-dark-800 text-left transition group {{ $key === $currentModelKey ? 'bg-rose-50/60 dark:bg-dark-800/90 ring-1 ring-rose-500/30' : '' }}"
                                                data-model="{{ $key }}"
                                                data-name="{{ $meta['name'] }}"
                                                data-badge="{{ $meta['badge'] }}"
                                                data-provider="grok"
                                            >
                                                <div class="h-8 w-8 rounded-lg bg-rose-50 dark:bg-rose-950/40 flex items-center justify-center shrink-0 text-rose-600 dark:text-rose-300 mt-0.5 group-hover:scale-105 transition-transform">
                                                    <i data-lucide="zap" class="w-4 h-4 text-rose-500"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ $meta['name'] }}</span>
                                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                                            {{ $meta['badge'] }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">
                                                        {{ $meta['desc'] }}
                                                    </p>
                                                </div>
                                                <i data-lucide="check" class="check-icon w-4 h-4 text-rose-500 shrink-0 self-center {{ $key === $currentModelKey ? '' : 'hidden' }}"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- 6. ALIBABA QWEN CODER --}}
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                    <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="code-2" class="w-3 h-3"></i>
                                            <span>Alibaba Qwen</span>
                                        </span>
                                        <span class="text-[9px] font-normal text-slate-400">Coding Specialist</span>
                                    </div>
                                    <div class="space-y-1 pt-1">
                                        @foreach (['qwen-coder'] as $key)
                                            @php $meta = $models[$key] ?? null; @endphp
                                            @continue(!$meta)
                                            <button
                                                type="button"
                                                class="model-option w-full flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-dark-800 text-left transition group {{ $key === $currentModelKey ? 'bg-emerald-50/60 dark:bg-dark-800/90 ring-1 ring-emerald-500/30' : '' }}"
                                                data-model="{{ $key }}"
                                                data-name="{{ $meta['name'] }}"
                                                data-badge="{{ $meta['badge'] }}"
                                                data-provider="qwen"
                                            >
                                                <div class="h-8 w-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center shrink-0 text-emerald-600 dark:text-emerald-300 mt-0.5 group-hover:scale-105 transition-transform">
                                                    <i data-lucide="code-2" class="w-4 h-4 text-emerald-500"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ $meta['name'] }}</span>
                                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                                            {{ $meta['badge'] }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">
                                                        {{ $meta['desc'] }}
                                                    </p>
                                                </div>
                                                <i data-lucide="check" class="check-icon w-4 h-4 text-emerald-500 shrink-0 self-center {{ $key === $currentModelKey ? '' : 'hidden' }}"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- 7. META LLAMA 3.3 --}}
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                    <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="layers" class="w-3 h-3"></i>
                                            <span>Meta AI</span>
                                        </span>
                                        <span class="text-[9px] font-normal text-slate-400">Llama 3.3 70B</span>
                                    </div>
                                    <div class="space-y-1 pt-1">
                                        @foreach (['llama-3.3-70b'] as $key)
                                            @php $meta = $models[$key] ?? null; @endphp
                                            @continue(!$meta)
                                            <button
                                                type="button"
                                                class="model-option w-full flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-dark-800 text-left transition group {{ $key === $currentModelKey ? 'bg-indigo-50/60 dark:bg-dark-800/90 ring-1 ring-indigo-500/30' : '' }}"
                                                data-model="{{ $key }}"
                                                data-name="{{ $meta['name'] }}"
                                                data-badge="{{ $meta['badge'] }}"
                                                data-provider="meta"
                                            >
                                                <div class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center shrink-0 text-indigo-600 dark:text-indigo-300 mt-0.5 group-hover:scale-105 transition-transform">
                                                    <i data-lucide="layers" class="w-4 h-4 text-indigo-500"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ $meta['name'] }}</span>
                                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                                            {{ $meta['badge'] }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">
                                                        {{ $meta['desc'] }}
                                                    </p>
                                                </div>
                                                <i data-lucide="check" class="check-icon w-4 h-4 text-indigo-500 shrink-0 self-center {{ $key === $currentModelKey ? '' : 'hidden' }}"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- 8. LIVE REAL-TIME WEB SEARCH AI --}}
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                    <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="globe" class="w-3 h-3"></i>
                                            <span>Real-Time Web Search</span>
                                        </span>
                                        <span class="text-[9px] font-normal text-slate-400">Live Grounding</span>
                                    </div>
                                    <div class="space-y-1 pt-1">
                                        @foreach (['web-search'] as $key)
                                            @php $meta = $models[$key] ?? null; @endphp
                                            @continue(!$meta)
                                            <button
                                                type="button"
                                                class="model-option w-full flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-dark-800 text-left transition group {{ $key === $currentModelKey ? 'bg-sky-50/60 dark:bg-dark-800/90 ring-1 ring-sky-500/30' : '' }}"
                                                data-model="{{ $key }}"
                                                data-name="{{ $meta['name'] }}"
                                                data-badge="{{ $meta['badge'] }}"
                                                data-provider="web"
                                            >
                                                <div class="h-8 w-8 rounded-lg bg-sky-50 dark:bg-sky-950/40 flex items-center justify-center shrink-0 text-sky-600 dark:text-sky-300 mt-0.5 group-hover:scale-105 transition-transform">
                                                    <i data-lucide="globe" class="w-4 h-4 text-sky-500"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ $meta['name'] }}</span>
                                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300">
                                                            {{ $meta['badge'] }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">
                                                        {{ $meta['desc'] }}
                                                    </p>
                                                </div>
                                                <i data-lucide="check" class="check-icon w-4 h-4 text-sky-500 shrink-0 self-center {{ $key === $currentModelKey ? '' : 'hidden' }}"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Top Action Buttons --}}
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <button id="export-chat-btn" title="Export to Markdown" class="p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-dark-800 rounded-xl transition active:scale-95">
                        <i data-lucide="download" class="w-4 h-4"></i>
                    </button>
                    <button id="speech-toggle-btn" title="Toggle Auto Read-Aloud" class="p-2 text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-dark-800 rounded-xl transition active:scale-95">
                        <i data-lucide="volume-2" id="speech-icon" class="w-4 h-4"></i>
                    </button>
                </div>
            </header>

            {{-- MESSAGES SCROLL CONTAINER --}}
            <div id="chat-container" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6">

                {{-- Empty State (Modern Hero) --}}
                <div id="empty-state" class="max-w-2xl mx-auto py-12 sm:py-20 text-center {{ $messages->isNotEmpty() ? 'hidden' : '' }}">
                    <div class="relative inline-flex mb-5">
                        <div class="absolute -inset-1.5 rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 opacity-30 blur-lg animate-pulse"></div>
                        <div class="relative inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-xl shadow-blue-500/25 ring-2 ring-white/20">
                            <i data-lucide="sparkles" class="w-8 h-8"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-2.5">
                        What will we create today?
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-10 leading-relaxed">
                        Nexus AI Studio unites <strong class="text-blue-600 dark:text-blue-400 font-semibold">Google Gemini</strong>, <strong class="text-emerald-600 dark:text-emerald-400 font-semibold">ChatGPT</strong>, <strong class="text-sky-600 dark:text-sky-400 font-semibold">DeepSeek</strong>, <strong class="text-amber-600 dark:text-amber-400 font-semibold">Claude</strong>, and <strong class="text-rose-600 dark:text-rose-400 font-semibold">Grok</strong> with multimodal vision, code intelligence, and image creation.
                    </p>

                    {{-- 4 Interactive Suggestion Cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-left max-w-xl mx-auto">
                        <button class="prompt-chip group p-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-dark-900/90 hover:border-purple-500/60 dark:hover:border-purple-500/60 hover:-translate-y-0.5 transition-all text-left shadow-xs hover:shadow-lg">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white mb-1.5 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                <div class="p-1.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400">
                                    <i data-lucide="palette" class="w-4 h-4"></i>
                                </div>
                                <span>Generate AI Image</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Generate an image of a cybernetic tiger in a futuristic neon jungle at night.</p>
                        </button>

                        <button class="prompt-chip group p-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-dark-900/90 hover:border-sky-500/60 dark:hover:border-sky-500/60 hover:-translate-y-0.5 transition-all text-left shadow-xs hover:shadow-lg">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white mb-1.5 group-hover:text-sky-600 dark:group-hover:text-sky-400">
                                <div class="p-1.5 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400">
                                    <i data-lucide="cpu" class="w-4 h-4"></i>
                                </div>
                                <span>DeepSeek R1 Reasoning</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Explain Dijkstra algorithm step-by-step with code and time complexity proof.</p>
                        </button>

                        <button class="prompt-chip group p-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-dark-900/90 hover:border-blue-500/60 dark:hover:border-blue-500/60 hover:-translate-y-0.5 transition-all text-left shadow-xs hover:shadow-lg">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white mb-1.5 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                <div class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400">
                                    <i data-lucide="code" class="w-4 h-4"></i>
                                </div>
                                <span>Laravel Architecture</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Create a production API controller with FormRequest validation & Eloquent Resource.</p>
                        </button>

                        <button class="prompt-chip group p-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-dark-900/90 hover:border-emerald-500/60 dark:hover:border-emerald-500/60 hover:-translate-y-0.5 transition-all text-left shadow-xs hover:shadow-lg">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white mb-1.5 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                                <div class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </div>
                                <span>Multimodal Document Review</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Drop a code file or PDF doc to discover bugs, security flaws, and performance optimizations.</p>
                        </button>
                    </div>
                </div>

                {{-- Messages List --}}
                <div id="messages-list" class="max-w-3xl lg:max-w-4xl mx-auto space-y-6">
                    @foreach ($messages as $msg)
                        <div class="message-wrapper flex gap-3 sm:gap-4 {{ $msg->role === 'user' ? 'justify-end' : 'justify-start' }}">
                            @if ($msg->role === 'assistant')
                                <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-blue-600 via-indigo-600 to-violet-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20 ring-2 ring-blue-500/15 mt-0.5">
                                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                                </div>
                            @endif

                            <div class="max-w-[88%] sm:max-w-[80%] space-y-2">
                                
                                {{-- Render User Attachments if any --}}
                                @if (! empty($msg->attachments) && is_array($msg->attachments))
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        @foreach ($msg->attachments as $att)
                                            @if (($att['type'] ?? '') === 'image')
                                                <a href="{{ $att['url'] }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 max-w-[220px] shadow-xs hover:opacity-95 transition">
                                                    <img src="{{ $att['url'] }}" alt="{{ $att['name'] }}" class="h-32 w-auto object-cover">
                                                </a>
                                            @else
                                                <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-dark-800 border border-slate-200 dark:border-slate-700 text-xs shadow-xs">
                                                    <i data-lucide="file" class="w-4 h-4 text-blue-500"></i>
                                                    <span class="truncate max-w-[150px] font-medium">{{ $att['name'] }}</span>
                                                    <span class="text-[10px] text-slate-400">({{ $att['size'] }})</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <div class="{{ $msg->role === 'user' ? 'bg-blue-600 text-white rounded-2xl rounded-tr-md px-4 py-3 shadow-xs text-sm whitespace-pre-wrap break-words leading-relaxed font-normal' : 'assistant-content bg-transparent text-slate-800 dark:text-slate-200 prose-chat text-[14.5px]' }}">
                                    @if ($msg->role === 'assistant')
                                        {!! Str::markdown($msg->content) !!}
                                    @else
                                        {{ $msg->content }}
                                    @endif
                                </div>

                                @if ($msg->role === 'assistant')
                                    <div class="assistant-actions flex items-center gap-3 px-1 pt-1 text-xs text-slate-400">
                                        <button type="button" onclick="navigator.clipboard.writeText({{ json_encode($msg->content) }}); showToast('Copied to clipboard');" class="flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                                            <i data-lucide="copy" class="w-3.5 h-3.5"></i> <span>Copy</span>
                                        </button>
                                        <button type="button" onclick="readAloud({{ json_encode($msg->content) }})" class="flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                                            <i data-lucide="volume-2" class="w-3.5 h-3.5"></i> <span>Listen</span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            @if ($msg->role === 'user')
                                <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-slate-700 to-slate-900 text-white flex items-center justify-center shrink-0 shadow-sm ring-2 ring-slate-200 dark:ring-zinc-800">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- FLOATING INPUT AREA WITH ATTACHMENT PREVIEW --}}
            <div class="p-3 sm:p-5 bg-gradient-to-t from-white via-white/95 to-transparent dark:from-dark-950 dark:via-dark-950/95 dark:to-transparent shrink-0">
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
                    <form id="chat-form" class="relative bg-white dark:bg-dark-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-black/50 focus-within:border-blue-500 dark:focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 transition-all p-2.5 sm:p-3">
                        
                        {{-- ATTACHMENTS PREVIEW CHIPS BAR (Above Textarea) --}}
                        <div id="attachments-bar" class="hidden flex-wrap gap-2 px-2 pb-2.5 border-b border-slate-100 dark:border-slate-800">
                            <!-- Dynamic attachment chips render here -->
                        </div>

                        {{-- Hidden File Input --}}
                        <input type="file" id="file-input" class="hidden" multiple accept="image/*,.pdf,.txt,.csv,.json,.md,.sql,.php,.js,.ts,.py,.html,.css,.xml,.log">

                        {{-- QUICK INLINE CAPABILITY CHIPS --}}
                        <div class="flex items-center gap-1.5 px-1.5 pb-1.5 overflow-x-auto text-[11px] text-slate-500 dark:text-slate-400 no-scrollbar">
                            <button type="button" onclick="quickFillPrompt('generate an image of ')" class="shrink-0 flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100/80 dark:bg-dark-800/80 hover:bg-purple-50 dark:hover:bg-purple-950/40 hover:text-purple-600 dark:hover:text-purple-400 transition font-medium">
                                <i data-lucide="palette" class="w-3 h-3 text-purple-500"></i>
                                <span>AI Image</span>
                            </button>
                            <button type="button" onclick="toggleWebSearch(true)" class="shrink-0 flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100/80 dark:bg-dark-800/80 hover:bg-sky-50 dark:hover:bg-sky-950/40 hover:text-sky-600 dark:hover:text-sky-400 transition font-medium">
                                <i data-lucide="globe" class="w-3 h-3 text-sky-500"></i>
                                <span>Web Search</span>
                            </button>
                            <button type="button" onclick="quickFillPrompt('Write a clean PHP/Laravel ')" class="shrink-0 flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100/80 dark:bg-dark-800/80 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-600 dark:hover:text-emerald-400 transition font-medium">
                                <i data-lucide="code-2" class="w-3 h-3 text-emerald-500"></i>
                                <span>Code</span>
                            </button>
                            <button type="button" onclick="quickFillPrompt('Solve and explain step by step: ')" class="shrink-0 flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100/80 dark:bg-dark-800/80 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 dark:hover:text-amber-400 transition font-medium">
                                <i data-lucide="brain" class="w-3 h-3 text-amber-500"></i>
                                <span>Reasoning</span>
                            </button>
                        </div>

                        <textarea
                            id="message-input"
                            rows="1"
                            maxlength="10000"
                            placeholder="Ask Nexus AI, describe an image to generate, or drop files... (Shift+Enter for newline)"
                            class="w-full bg-transparent px-3 py-1.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none resize-none max-h-48 overflow-y-auto"
                        ></textarea>

                        <div class="flex items-center justify-between pt-2 px-1">
                            
                            {{-- Attachment & Voice Buttons --}}
                            <div class="flex items-center gap-1.5">
                                <button
                                    id="attach-btn"
                                    type="button"
                                    title="Upload image, document or code file"
                                    class="p-2 rounded-xl text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-dark-800 transition active:scale-95"
                                >
                                    <i data-lucide="paperclip" class="w-4 h-4"></i>
                                </button>

                                <button
                                    id="voice-input-btn"
                                    type="button"
                                    title="Voice Input (Speech-to-Text)"
                                    class="p-2 rounded-xl text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-dark-800 transition active:scale-95"
                                >
                                    <i data-lucide="mic" id="mic-icon" class="w-4 h-4"></i>
                                </button>

                                <button
                                    id="web-search-btn"
                                    type="button"
                                    title="Toggle Real-Time Web Search Grounding"
                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-sky-600 dark:text-slate-400 dark:hover:text-sky-400 hover:bg-slate-100 dark:hover:bg-dark-800 transition border border-slate-200/60 dark:border-slate-800 active:scale-95"
                                >
                                    <i data-lucide="globe" class="w-3.5 h-3.5" id="web-search-icon"></i>
                                    <span class="hidden sm:inline">Search Web</span>
                                    <span id="web-search-dot" class="hidden w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                                </button>

                                <span id="char-counter" class="text-[10px] text-slate-400 font-mono hidden sm:inline ml-1">0/10000</span>
                            </div>

                            {{-- Send / Stop Action Button --}}
                            <div class="flex items-center gap-2">
                                <button
                                    id="stop-btn"
                                    type="button"
                                    class="hidden px-3.5 py-1.5 rounded-full bg-slate-200 dark:bg-dark-800 hover:bg-slate-300 dark:hover:bg-dark-700 text-slate-800 dark:text-slate-100 text-xs font-semibold flex items-center gap-1.5 transition active:scale-95"
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

                    <p class="text-center text-[11px] text-slate-400 mt-2 font-medium">
                        Nexus AI Studio can make mistakes. Verify important factual and coding details.
                    </p>
                </div>
            </div>

            {{-- Fullscreen Image Lightbox Modal --}}
            <div id="image-lightbox" class="hidden fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4 cursor-pointer" onclick="closeLightbox()">
                <button type="button" onclick="closeLightbox()" class="absolute top-4 right-4 p-2.5 rounded-full bg-white/10 hover:bg-white/20 text-white transition active:scale-95">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
                <img id="lightbox-img" src="" alt="Enlarged Art" class="max-w-[90vw] max-h-[90vh] object-contain rounded-2xl shadow-2xl cursor-default" onclick="event.stopPropagation()">
            </div>
        </main>
    </div>

    {{-- SCRIPTS --}}
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentConversationId = {{ $activeConversation ? $activeConversation->id : 'null' }};
        let currentModel = '{{ $activeConversation?->model ?? $defaultModel }}';
        let currentPersona = '{{ $activeConversation?->persona ?? "default" }}';

        // Elements
        const appContainer = document.getElementById('app-container');
        const chatContainer = document.getElementById('chat-container');
        const messagesList = document.getElementById('messages-list');
        const emptyState = document.getElementById('empty-state');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const sendBtn = document.getElementById('send-btn');
        const stopBtn = document.getElementById('stop-btn');
        const attachBtn = document.getElementById('attach-btn');
        const fileInput = document.getElementById('file-input');
        const attachmentsBar = document.getElementById('attachments-bar');
        const voiceInputBtn = document.getElementById('voice-input-btn');
        const micIcon = document.getElementById('mic-icon');
        const webSearchBtn = document.getElementById('web-search-btn');
        const webSearchDot = document.getElementById('web-search-dot');
        const webSearchIcon = document.getElementById('web-search-icon');
        let isWebSearchActive = false;
        const charCounter = document.getElementById('char-counter');
        const errorBanner = document.getElementById('error-banner');
        const errorMessage = document.getElementById('error-message');
        const dismissError = document.getElementById('dismiss-error');
        const newChatBtn = document.getElementById('new-chat-btn');
        const conversationsList = document.getElementById('conversations-list');
        const exportChatBtn = document.getElementById('export-chat-btn');
        const speechToggleBtn = document.getElementById('speech-toggle-btn');
        const speechIcon = document.getElementById('speech-icon');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');

        // Model Switcher Elements
        const modelSwitcherBtn = document.getElementById('model-switcher-btn');
        const modelMenu = document.getElementById('model-menu');
        const modelDisplayName = document.getElementById('model-display-name');
        const modelDisplayBadge = document.getElementById('model-display-badge');
        const modelDisplayIcon = document.getElementById('model-display-icon');

        // Sidebar Elements
        const sidebar = document.getElementById('sidebar');
        const toggleSidebarBtn = document.getElementById('toggle-sidebar-btn');
        const closeSidebarBtn = document.getElementById('close-sidebar-btn');
        const mobileBackdrop = document.getElementById('mobile-backdrop');

        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const themeText = document.getElementById('theme-text');

        // State variables
        let pendingAttachments = [];
        let activeAbortController = null;
        let isTTSAutoPlay = false;
        let speechRecognition = null;
        let isRecording = false;

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
        function applyTheme(isDark) {
            document.documentElement.classList.toggle('dark', isDark);
            themeIcon.setAttribute('data-lucide', isDark ? 'sun' : 'moon');
            themeText.textContent = isDark ? 'Light mode' : 'Dark mode';
            lucide.createIcons();
            localStorage.setItem('nexus_theme', isDark ? 'dark' : 'light');
        }

        const savedTheme = localStorage.getItem('nexus_theme');
        if (savedTheme) {
            applyTheme(savedTheme === 'dark');
        } else {
            applyTheme(window.matchMedia('(prefers-color-scheme: dark)').matches);
        }

        themeToggle.addEventListener('click', () => {
            applyTheme(!document.documentElement.classList.contains('dark'));
        });

        // Toast Helper
        function showToast(msg) {
            toastMessage.textContent = msg;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Sidebar Toggle (Mobile & Desktop)
        function toggleSidebar(show) {
            if (window.innerWidth < 1024) {
                if (show) {
                    sidebar.classList.remove('-translate-x-full');
                    mobileBackdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    mobileBackdrop.classList.add('hidden');
                }
            } else {
                sidebar.classList.toggle('hidden');
            }
        }

        toggleSidebarBtn.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                toggleSidebar(true);
            } else {
                toggleSidebar();
            }
        });

        closeSidebarBtn?.addEventListener('click', () => toggleSidebar(false));
        mobileBackdrop?.addEventListener('click', () => toggleSidebar(false));

        // Model Switcher Popover
        modelSwitcherBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            modelMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!modelMenu.contains(e.target) && !modelSwitcherBtn.contains(e.target)) {
                modelMenu.classList.add('hidden');
            }
        });

        const providerThemes = {
            auto: {
                badgeClass: 'text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300',
                iconHtml: '<i data-lucide="sparkles" class="w-3.5 h-3.5 text-purple-500"></i>',
                activeClass: 'bg-purple-50/70 dark:bg-dark-800/90 ring-1 ring-purple-500/30',
            },
            gemini: {
                badgeClass: 'text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
                iconHtml: '<i data-lucide="sparkles" class="w-3.5 h-3.5 text-blue-500"></i>',
                activeClass: 'bg-blue-50/60 dark:bg-dark-800/90 ring-1 ring-blue-500/30',
            },
            openai: {
                badgeClass: 'text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
                iconHtml: '<i data-lucide="bot" class="w-3.5 h-3.5 text-emerald-500"></i>',
                activeClass: 'bg-emerald-50/60 dark:bg-dark-800/90 ring-1 ring-emerald-500/30',
            },
            claude: {
                badgeClass: 'text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
                iconHtml: '<i data-lucide="flame" class="w-3.5 h-3.5 text-amber-500"></i>',
                activeClass: 'bg-amber-50/60 dark:bg-dark-800/90 ring-1 ring-amber-500/30',
            },
            deepseek: {
                badgeClass: 'text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300',
                iconHtml: '<i data-lucide="cpu" class="w-3.5 h-3.5 text-sky-500"></i>',
                activeClass: 'bg-sky-50/60 dark:bg-dark-800/90 ring-1 ring-sky-500/30',
            },
            grok: {
                badgeClass: 'text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300',
                iconHtml: '<i data-lucide="zap" class="w-3.5 h-3.5 text-rose-500"></i>',
                activeClass: 'bg-rose-50/60 dark:bg-dark-800/90 ring-1 ring-rose-500/30',
            },
            qwen: {
                badgeClass: 'text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
                iconHtml: '<i data-lucide="code-2" class="w-3.5 h-3.5 text-emerald-500"></i>',
                activeClass: 'bg-emerald-50/60 dark:bg-dark-800/90 ring-1 ring-emerald-500/30',
            },
            meta: {
                badgeClass: 'text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300',
                iconHtml: '<i data-lucide="layers" class="w-3.5 h-3.5 text-indigo-500"></i>',
                activeClass: 'bg-indigo-50/60 dark:bg-dark-800/90 ring-1 ring-indigo-500/30',
            },
            web: {
                badgeClass: 'text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300',
                iconHtml: '<i data-lucide="globe" class="w-3.5 h-3.5 text-sky-500"></i>',
                activeClass: 'bg-sky-50/60 dark:bg-dark-800/90 ring-1 ring-sky-500/30',
            },
        };

        function applySelectedModel(modelKey, syncBackend = false) {
            const btn = document.querySelector(`.model-option[data-model="${modelKey}"]`) || document.querySelector('.model-option[data-model="auto"]');
            if (!btn) return;

            const selectedKey = btn.getAttribute('data-model');
            const selectedName = btn.getAttribute('data-name');
            const selectedBadge = btn.getAttribute('data-badge');
            const providerKey = btn.getAttribute('data-provider') || 'auto';
            const theme = providerThemes[providerKey] || providerThemes.auto;

            currentModel = selectedKey;
            modelDisplayName.textContent = selectedName;
            modelDisplayBadge.textContent = selectedBadge;
            modelDisplayBadge.className = theme.badgeClass;
            modelDisplayIcon.innerHTML = theme.iconHtml;

            document.querySelectorAll('.model-option').forEach(opt => {
                opt.classList.remove(
                    'bg-purple-50/70', 'bg-blue-50/60', 'bg-sky-50/60', 'bg-emerald-50/60', 'bg-amber-50/60', 'bg-rose-50/60',
                    'dark:bg-dark-800/90', 'ring-1',
                    'ring-purple-500/30', 'ring-blue-500/30', 'ring-sky-500/30', 'ring-emerald-500/30', 'ring-amber-500/30', 'ring-rose-500/30'
                );
                opt.querySelector('.check-icon')?.classList.add('hidden');
            });

            const activeParts = theme.activeClass.split(' ');
            activeParts.forEach(cls => btn.classList.add(cls));
            btn.querySelector('.check-icon')?.classList.remove('hidden');

            lucide.createIcons();

            if (syncBackend && currentConversationId) {
                try {
                    fetch(`/chat/api/conversations/${currentConversationId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({ model: currentModel }),
                    }).catch(() => {});
                } catch (err) {}
            }
        }

        document.querySelectorAll('.model-option').forEach(btn => {
            btn.addEventListener('click', () => {
                const selectedKey = btn.getAttribute('data-model');
                applySelectedModel(selectedKey, true);
                modelMenu.classList.add('hidden');
                showToast(`Switched to ${btn.getAttribute('data-name')}`);
            });
        });

        // Auto-resizing textarea
        messageInput.addEventListener('input', () => {
            messageInput.style.height = 'auto';
            messageInput.style.height = Math.min(messageInput.scrollHeight, 192) + 'px';
            charCounter.textContent = `${messageInput.value.length}/10000`;
        });

        messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });

        // Ctrl+K Shortcut for New Chat
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                startNewChat();
            }
        });

        // File Attachment Upload Pipeline
        attachBtn.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', async (e) => {
            const files = Array.from(e.target.files);
            for (const file of files) {
                await uploadSingleFile(file);
            }
            fileInput.value = '';
        });

        // Drag & Drop
        window.addEventListener('dragover', (e) => {
            e.preventDefault();
            document.body.classList.add('dragging');
        });
        window.addEventListener('dragleave', (e) => {
            if (e.clientX === 0 || e.clientY === 0) {
                document.body.classList.remove('dragging');
            }
        });
        window.addEventListener('drop', async (e) => {
            e.preventDefault();
            document.body.classList.remove('dragging');
            const files = Array.from(e.dataTransfer.files);
            for (const file of files) {
                await uploadSingleFile(file);
            }
        });

        // Clipboard Paste Image / Screenshot
        window.addEventListener('paste', async (e) => {
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;
            for (const item of items) {
                if (item.type.indexOf('image') !== -1) {
                    const blob = item.getAsFile();
                    await uploadSingleFile(blob);
                }
            }
        });

        async function uploadSingleFile(file) {
            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('/chat/upload', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    body: formData,
                });

                if (!response.ok) throw new Error('Upload failed');
                const data = await response.json();
                pendingAttachments.push(data);
                renderAttachmentChips();
                showToast(`Attached: ${data.name}`);
            } catch (err) {
                showToast('Failed to upload file');
            }
        }

        function renderAttachmentChips() {
            if (pendingAttachments.length === 0) {
                attachmentsBar.classList.add('hidden');
                attachmentsBar.innerHTML = '';
                return;
            }

            attachmentsBar.classList.remove('hidden');
            attachmentsBar.innerHTML = '';

            pendingAttachments.forEach((att, idx) => {
                const chip = document.createElement('div');
                chip.className = 'flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-dark-800 border border-slate-200 dark:border-slate-700 text-xs';
                chip.innerHTML = `
                    <i data-lucide="${att.type === 'image' ? 'image' : 'file'}" class="w-3.5 h-3.5 text-blue-500"></i>
                    <span class="truncate max-w-[120px] font-medium text-slate-700 dark:text-slate-200">${att.name}</span>
                    <button type="button" onclick="removeAttachment(${idx})" class="p-0.5 rounded-full hover:bg-slate-200 dark:hover:bg-dark-700 text-slate-400 hover:text-red-500">
                        <i data-lucide="x" class="w-3 h-3"></i>
                    </button>
                `;
                attachmentsBar.appendChild(chip);
            });
            lucide.createIcons();
        }

        window.removeAttachment = function(index) {
            pendingAttachments.splice(index, 1);
            renderAttachmentChips();
        };

        function clearAttachments() {
            pendingAttachments = [];
            renderAttachmentChips();
        }

        function scrollToBottom() {
            chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
        }

        // Copy Code block helper
        window.copyCode = function(button) {
            const pre = button.closest('pre');
            const code = pre.querySelector('code').innerText;
            navigator.clipboard.writeText(code).then(() => {
                const orig = button.innerHTML;
                button.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400"></i> <span>Copied!</span>';
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

        // Code block and Image decoration
        function decorateCodeBlocks(container) {
            container.querySelectorAll('pre').forEach(pre => {
                if (pre.querySelector('.code-header')) return;
                const code = pre.querySelector('code');
                const langMatch = code ? code.className.match(/language-(\w+)/) : null;
                const lang = langMatch ? langMatch[1] : 'code';

                const header = document.createElement('div');
                header.className = 'code-header flex items-center justify-between px-4 py-2 bg-[#0c0e14] text-slate-400 text-xs border-b border-white/10 select-none';
                header.innerHTML = `
                    <span class="font-mono text-[11px] uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <i data-lucide="code" class="w-3.5 h-3.5 text-blue-400"></i>
                        <span>${lang}</span>
                    </span>
                    <button type="button" onclick="copyCode(this)" class="flex items-center gap-1 text-[11px] hover:text-white transition px-2 py-0.5 rounded hover:bg-white/5">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        <span>Copy</span>
                    </button>
                `;
                pre.prepend(header);
            });

            decorateImages(container);
            lucide.createIcons();
        }

        // Image decoration with full-screen lightbox & direct download
        function decorateImages(container) {
            container.querySelectorAll('img:not(.decorated-img):not(.avatar-img)').forEach(img => {
                img.classList.add('decorated-img', 'cursor-pointer');
                
                img.addEventListener('click', () => {
                    openLightbox(img.src);
                });

                if (img.parentElement.classList.contains('ai-image-card')) return;

                const card = document.createElement('div');
                card.className = 'ai-image-card relative group inline-block my-2 overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 shadow-md hover:shadow-xl transition-all';
                img.parentNode.insertBefore(card, img);
                card.appendChild(img);

                const overlay = document.createElement('div');
                overlay.className = 'absolute bottom-2 right-2 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-900/85 backdrop-blur-md px-2.5 py-1.5 rounded-xl border border-white/10 text-white text-xs shadow-lg';
                overlay.innerHTML = `
                    <button type="button" title="View Fullscreen" class="p-1 hover:text-blue-400 transition" onclick="event.stopPropagation(); openLightbox('${img.src}')">
                        <i data-lucide="maximize-2" class="w-3.5 h-3.5"></i>
                    </button>
                    <a href="${img.src}" download="nexus-art.jpg" target="_blank" title="Download Image" class="p-1 hover:text-emerald-400 transition" onclick="event.stopPropagation()">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    </a>
                    <button type="button" title="Copy Image Link" class="p-1 hover:text-purple-400 transition" onclick="event.stopPropagation(); navigator.clipboard.writeText('${img.src}'); showToast('Image link copied');">
                        <i data-lucide="link" class="w-3.5 h-3.5"></i>
                    </button>
                `;
                card.appendChild(overlay);
            });
        }

        window.openLightbox = function(src) {
            const lb = document.getElementById('image-lightbox');
            const lbImg = document.getElementById('lightbox-img');
            if (!lb || !lbImg) return;
            lbImg.src = src;
            lb.classList.remove('hidden');
            lucide.createIcons();
        };

        window.closeLightbox = function() {
            const lb = document.getElementById('image-lightbox');
            if (lb) lb.classList.add('hidden');
        };

        window.quickFillPrompt = function(prefix) {
            messageInput.value = prefix;
            messageInput.focus();
            messageInput.dispatchEvent(new Event('input'));
        };

        window.toggleWebSearch = function(forceState = null) {
            if (forceState !== null) {
                isWebSearchActive = forceState;
            } else {
                isWebSearchActive = !isWebSearchActive;
            }

            if (isWebSearchActive) {
                webSearchBtn.className = 'flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-semibold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/60 border border-sky-300 dark:border-sky-800 shadow-xs ring-1 ring-sky-400/20 active:scale-95 transition';
                webSearchDot.classList.remove('hidden');
                showToast('Real-Time Web Search: Enabled 🌐');
            } else {
                webSearchBtn.className = 'flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-sky-600 dark:text-slate-400 dark:hover:text-sky-400 hover:bg-slate-100 dark:hover:bg-dark-800 transition border border-transparent active:scale-95';
                webSearchDot.classList.add('hidden');
                showToast('Real-Time Web Search: Disabled');
            }
        };

        webSearchBtn?.addEventListener('click', () => toggleWebSearch());

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLightbox();
        });

        // Append User Message to UI
        function appendUserMessage(text, attachments) {
            emptyState.classList.add('hidden');
            const wrap = document.createElement('div');
            wrap.className = 'message-wrapper flex gap-3 sm:gap-4 justify-end';

            let attachmentsHtml = '';
            if (attachments.length > 0) {
                attachmentsHtml = '<div class="flex flex-wrap gap-2 mb-2 justify-end">';
                attachments.forEach(att => {
                    if (att.type === 'image') {
                        attachmentsHtml += `
                            <a href="${att.url}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 max-w-[220px] shadow-xs hover:opacity-95 transition">
                                <img src="${att.url}" alt="${att.name}" class="h-32 w-auto object-cover">
                            </a>`;
                    } else {
                        attachmentsHtml += `
                            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-dark-800 border border-slate-200 dark:border-slate-700 text-xs shadow-xs">
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
                    ${text ? `<div class="bg-blue-600 text-white rounded-2xl rounded-tr-md px-4 py-3 shadow-xs text-sm whitespace-pre-wrap break-words leading-relaxed font-normal">${escapeHtml(text)}</div>` : ''}
                </div>
                <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-slate-700 to-slate-900 text-white flex items-center justify-center shrink-0 shadow-sm ring-2 ring-slate-200 dark:ring-zinc-800">
                    <i data-lucide="user" class="w-4 h-4"></i>
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
                <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-blue-600 via-indigo-600 to-violet-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20 ring-2 ring-blue-500/15 mt-0.5">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                </div>
                <div class="max-w-[88%] sm:max-w-[80%] space-y-2">
                    <div class="assistant-content bg-transparent text-slate-800 dark:text-slate-200 prose-chat text-[14.5px]">
                        <span class="cursor-blink"></span>
                    </div>
                    <div class="assistant-actions hidden items-center gap-3 px-1 pt-1 text-xs text-slate-400">
                        <button type="button" class="copy-msg-btn flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i> <span>Copy</span>
                        </button>
                        <button type="button" class="listen-msg-btn flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                            <i data-lucide="volume-2" class="w-3.5 h-3.5"></i> <span>Listen</span>
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
                web_search: isWebSearchActive,
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
                    buffer = lines.pop(); // keep last incomplete line

                    for (const line of lines) {
                        const trimmed = line.trim();
                        if (!trimmed || !trimmed.startsWith('data:')) continue;
                        const dataStr = trimmed.substring(5).trim();
                        if (dataStr === '[DONE]') break;

                        try {
                            const parsed = JSON.parse(dataStr);
                            if (parsed.conversation_id && !currentConversationId) {
                                currentConversationId = parsed.conversation_id;
                                updateSidebarConversations(parsed.conversation_id, parsed.title);
                                history.pushState({}, '', `/chat/c/${parsed.conversation_id}`);
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
                    showToast('Copied to clipboard');
                });

                bubble.listenBtn.addEventListener('click', () => {
                    readAloud(accumulatedText);
                });

                if (isTTSAutoPlay && accumulatedText) {
                    readAloud(accumulatedText);
                }
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
                messageInput.focus();
                activeAbortController = null;
                lucide.createIcons();
            }
        });

        // Stop generation button
        stopBtn.addEventListener('click', () => {
            if (activeAbortController) {
                activeAbortController.abort();
            }
        });

        // New Chat Handler
        function startNewChat() {
            currentConversationId = null;
            applySelectedModel('auto', false);
            toggleWebSearch(false);
            messagesList.innerHTML = '';
            emptyState.classList.remove('hidden');
            history.pushState({}, '', '/chat');
            document.querySelectorAll('.conversation-item').forEach(el => {
                el.classList.remove('bg-slate-100', 'dark:bg-dark-800', 'text-slate-900', 'dark:text-white', 'font-medium', 'shadow-xs', 'ring-1');
            });
            clearAttachments();
            messageInput.value = '';
            messageInput.style.height = 'auto';
            messageInput.focus();
            toggleSidebar(false);
        }

        newChatBtn.addEventListener('click', startNewChat);

        // LOAD CONVERSATION
        window.loadConversation = async function(id) {
            try {
                const res = await fetch(`/chat/api/conversations/${id}`);
                const data = await res.json();
                currentConversationId = data.conversation.id;

                if (data.conversation.model) {
                    applySelectedModel(data.conversation.model, false);
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
                                <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-blue-600 via-indigo-600 to-violet-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20 ring-2 ring-blue-500/15 mt-0.5">
                                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                                </div>
                                <div class="max-w-[88%] sm:max-w-[80%] space-y-2">
                                    <div class="assistant-content bg-transparent text-slate-800 dark:text-slate-200 prose-chat text-[14.5px]">
                                        ${DOMPurify.sanitize(marked.parse(msg.content))}
                                    </div>
                                    <div class="assistant-actions flex items-center gap-3 px-1 pt-1 text-xs text-slate-400">
                                        <button type="button" class="copy-msg-btn flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                                            <i data-lucide="copy" class="w-3.5 h-3.5"></i> <span>Copy</span>
                                        </button>
                                        <button type="button" class="listen-msg-btn flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 transition">
                                            <i data-lucide="volume-2" class="w-3.5 h-3.5"></i> <span>Listen</span>
                                        </button>
                                    </div>
                                </div>
                            `;
                            wrap.querySelector('.copy-msg-btn').addEventListener('click', () => {
                                navigator.clipboard.writeText(msg.content);
                                showToast('Copied to clipboard');
                            });
                            wrap.querySelector('.listen-msg-btn').addEventListener('click', () => {
                                readAloud(msg.content);
                            });
                        } else {
                            let attHtml = '';
                            if (msg.attachments && msg.attachments.length > 0) {
                                attHtml = '<div class="flex flex-wrap gap-2 mb-2 justify-end">';
                                msg.attachments.forEach(att => {
                                    if (att.type === 'image') {
                                        attHtml += `
                                            <a href="${att.url}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 max-w-[220px] shadow-xs hover:opacity-95 transition">
                                                <img src="${att.url}" alt="${att.name}" class="h-32 w-auto object-cover">
                                            </a>`;
                                    } else {
                                        attHtml += `
                                            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-dark-800 border border-slate-200 dark:border-slate-700 text-xs shadow-xs">
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
                                    <div class="bg-blue-600 text-white rounded-2xl rounded-tr-md px-4 py-3 shadow-xs text-sm whitespace-pre-wrap break-words leading-relaxed font-normal">${escapeHtml(msg.content)}</div>
                                </div>
                                <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-slate-700 to-slate-900 text-white flex items-center justify-center shrink-0 shadow-sm ring-2 ring-slate-200 dark:ring-zinc-800">
                                    <i data-lucide="user" class="w-4 h-4"></i>
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
                    el.classList.toggle('ring-1', match);
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
            item.className = 'conversation-item group relative flex items-center justify-between px-3 py-2.5 rounded-xl text-xs cursor-pointer transition bg-slate-100 dark:bg-dark-800 text-slate-900 dark:text-white font-medium shadow-xs ring-1 ring-slate-200 dark:ring-slate-700/60';
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
