# Nexus AI Studio — Multi-Engine Enterprise AI Platform

<p align="center">
  <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80" alt="Nexus AI Banner" width="100%" style="border-radius: 16px; max-height: 340px; object-fit: cover;" />
</p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12"></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3"></a>
  <a href="https://ai.google.dev/"><img src="https://img.shields.io/badge/Google%20Gemini-Official%20API-4285F4?style=for-the-badge&logo=google&logoColor=white" alt="Google Gemini"></a>
  <a href="https://openrouter.ai/"><img src="https://img.shields.io/badge/OpenRouter-Multi--Engine-6366F1?style=for-the-badge&logo=fastapi&logoColor=white" alt="OpenRouter"></a>
  <a href="https://anthropic.com"><img src="https://img.shields.io/badge/Claude-3.5%20Sonnet-D97706?style=for-the-badge&logo=anthropic&logoColor=white" alt="Anthropic Claude"></a>
  <a href="https://platform.deepseek.com/"><img src="https://img.shields.io/badge/DeepSeek-V3%20%26%20R1-0284C7?style=for-the-badge&logo=cpu&logoColor=white" alt="DeepSeek AI"></a>
  <a href="https://x.ai/"><img src="https://img.shields.io/badge/xAI-Grok%202-E11D48?style=for-the-badge&logo=x&logoColor=white" alt="xAI Grok"></a>
  <a href="https://openai.com/"><img src="https://img.shields.io/badge/OpenAI-ChatGPT%204o--mini-10B981?style=for-the-badge&logo=openai&logoColor=white" alt="OpenAI ChatGPT"></a>
  <a href="https://qwenlm.github.io/"><img src="https://img.shields.io/badge/Alibaba-Qwen_2.5_Coder-059669?style=for-the-badge&logo=alibabadotcom&logoColor=white" alt="Alibaba Qwen"></a>
  <a href="https://ai.meta.com/llama/"><img src="https://img.shields.io/badge/Meta-Llama_3.3_70B-4F46E5?style=for-the-badge&logo=meta&logoColor=white" alt="Meta Llama"></a>
  <a href="https://openrouter.ai/"><img src="https://img.shields.io/badge/Live_Web-Search_Grounding-0284C7?style=for-the-badge&logo=googlechrome&logoColor=white" alt="Web Search Grounding"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS"></a>
  <a href="https://www.mysql.com/"><img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"></a>
</p>

---

## 🌟 Overview

**Nexus AI Studio** is an enterprise-grade multi-engine AI workspace built on **Laravel 12 (PHP 8.3)**. It seamlessly integrates **Google Gemini**, **Anthropic Claude**, **DeepSeek AI (V3 & R1)**, **Alibaba Qwen 2.5 Coder**, **Meta Llama 3.3 70B**, **Real-Time Live Web Search AI**, **OpenAI ChatGPT**, **xAI Grok**, and **FLUX Neural Image Generation** into a unified workspace.

Featuring a **Silent Smart Auto-Pilot Router (`auto`)** as the default engine, Nexus AI dynamically evaluates user intent on every single message turn, silently routing to the optimal AI model behind the scenes without distracting UI badges or conversational lag.

---

## ✨ Key Architectural Features

### ⚡ 1. Silent Smart Auto-Pilot Router (`auto` Mode)
* **Automatic Requirement Routing:** Users don't need to manually decide which model to choose. Every turn dynamically selects the industry-leading model for the specific task:
  * 🎭 **Pop Culture, Superheroes, Cinema & Creative Writing:** &rarr; **Anthropic Claude 3.5 Sonnet** (e.g., Marvel Hulk, movie rankings, creative storytelling).
  * 💻 **Web Frameworks & Architecture:** &rarr; **Anthropic Claude 3.5 Sonnet** (e.g., Laravel, Blade, Vue/React, Tailwind, full-stack design).
  * 🧑‍💻 **Code Synthesis & Software Engineering:** &rarr; **Alibaba Qwen 2.5 Coder 32B** (dedicated code generation, refactoring, algorithms).
  * 🛠️ **Backend Code, Python, MySQL, Debugging & Bug Fixes:** &rarr; **DeepSeek V3** (671B MoE benchmark leader for coding).
  * 🧠 **Open-Weights Reasoning & General Intelligence:** &rarr; **Meta Llama 3.3 70B** (high-precision open-weights reasoning).
  * 🧩 **Complex Math Proofs, Dijkstra, Big-O & Algorithms:** &rarr; **DeepSeek R1** (deep step-by-step reasoning with transparent thought process).
  * 🌐 **Live Internet & Web Grounding:** &rarr; **Real-Time Web Search AI** (`openai/gpt-4o-mini:online` with inline citations).
  * 🎨 **Direct Text-to-Image Prompts:** &rarr; **FLUX AI** (1024x1024 HD photorealistic rendering).
  * 🌍 **Live News, Current Trends & Crypto:** &rarr; **xAI Grok 2** (real-world knowledge & witty commentary).
  * ⚡ **Everyday Facts & Direct Q&A:** &rarr; **OpenAI ChatGPT 4o-mini** (ultra-fast compact intelligence).
  * 👁️ **File Attachments & OCR:** &rarr; **Google Gemini 3.1 Flash** (1M token multimodal context).
* **Silent Execution:** The router operates quietly behind the scenes — no artificial badges or model leak tags are inserted into the chat, giving users a direct, clean AI response every time.

### 🛡️ 2. Zero-Pollution Context Isolation
* **Zero Topic Bleeding:** Resolves the common LLM hallucination where switching topics (e.g. asking for movies after discussing SQL databases) causes the model to repeat the previous topic.
* **100% Prompt Focus:** The AI engine evaluates each user message with clean context isolation, ensuring accurate, high-fidelity answers tailored strictly to the current request.
* **Persistent MySQL Chat History:** Previous messages, timestamps, and uploaded files remain fully preserved in MySQL and rendered in the UI conversation drawer.

### ⚡ 3. Real-Time Token Streaming (Server-Sent Events)
* Instant, low-latency token-by-token response streaming via native PHP Generators (`yield`) and Server-Sent Events (`text/event-stream`).
* Live blinking cursor, animated response rendering, syntax-highlighted code blocks with 1-click copy, and interactive "Stop Generation" controls.

### 🎨 4. Autonomous AI Image Generation & Interactive Image Studio
* **Instant Text-to-Image:** Formulates vivid visual prompts powered by neural FLUX/SDXL models via Pollinations.ai.
* **Deterministic Intent Interceptor:** Automatically detects image generation requests in English or Roman Urdu (e.g., *"spider ki image banao"*, *"generate an image of a cybernetic tiger"*), rendering high-definition graphics directly inside the chat bubble.
* **Interactive Fullscreen Lightbox:** Click on any generated or uploaded image to view in a high-resolution, backdrop-blurred fullscreen modal with instant Escape key dismissal.
* **1-Click Image Toolbar:** Floating hover toolbar on every image offering:
  * 🔍 **Fullscreen View**
  * ⬇️ **Direct One-Click Download** (`nexus-art.jpg`)
  * 🔗 **Copy Image URL** to clipboard with toast notification.

### 🌐 5. Real-Time Web Search Grounding (`openai/gpt-4o-mini:online`)
* **Interactive Search Toggle:** Dedicated `🌐 Search Web` button with a pulsing indicator right inside the chat composer.
* **Live Web Intelligence:** Directly searches the live internet in real time to fetch breaking news, stock prices, live weather, sports scores, and recent event updates.
* **Inline Verifiable Citations:** Responses include verified sources, clickable links, and citation markers.

### 👁️ 6. Multimodal Vision & Document Analysis
* **Universal Uploads:** Supports Images (`PNG`, `JPG`, `WEBP`, `GIF`), Documents (`PDF`, `TXT`, `CSV`, `MD`), and Code files (`PHP`, `JS`, `PY`, etc.).
* **Multiple Ingestion Methods:**
  * File picker button (`+` button).
  * Direct file Drag & Drop with full-screen backdrop overlay.
  * Direct clipboard screenshot paste (`Ctrl + V`).
* **Base64 Local Vision Pipeline:** Automatically transforms images into Base64 Data URIs, allowing local development environments to inspect images without public IP requirements.

### 🎛️ 7. Universal Model Switcher & Quick Capability Chips
* **Quick Action Chips:** 1-click workflow accelerators right above the message input:
  * 🎨 `AI Image` (Pre-fills image generation prompt)
  * 🌐 `Web Search` (Enables live search toggle and pre-fills search query)
  * 💻 `Code` (Pre-fills code architecture prompt)
  * 🧠 `Reasoning` (Pre-fills step-by-step logic prompt)
* **Grouped Model Switcher Popover:**
  * **Auto (Smart Engine)** (Adaptive silent intelligence — Default)
  * **Alibaba Qwen 2.5 Coder 32B** (Enterprise software synthesis & refactoring)
  * **Meta Llama 3.3 70B Instruct** (High-efficiency open-weights reasoning)
  * **Real-Time Live Web Search** (Live web grounding via `gpt-4o-mini:online`)
  * **Claude 3.5 Sonnet** (Deep coding & creative writing)
  * **DeepSeek V3** (671B MoE code engine)
  * **DeepSeek R1** (Chain-of-thought reasoning)
  * **Grok 2** (Real-world trends & news)
  * **ChatGPT 4o-mini** (Everyday rapid assistance)
  * **Google Gemini 3.1 Flash / 3.5 Flash / 4 Argon** (Multimodal suite)
* Preset System Personas: General Assistant, AI Image Artist, Senior Code Architect, Copywriter & Editor, Business Analyst, and Direct & Concise.

### 🎙️ 8. Voice Input & Text-to-Speech
* **Speech-to-Text (STT):** Dictate messages naturally using the microphone via the Web Speech API.
* **Text-to-Speech (TTS):** 1-click read-aloud button on assistant responses with auto-play toggle.

---

## 🏗️ Architecture & Directory Layout

```
ai_chatbots/
├── app/
│   ├── Http/Controllers/
│   │   └── ChatController.php      # SSE streaming, context isolation, session & file uploads
│   ├── Models/
│   │   ├── Conversation.php       # Eloquent model for chat threads
│   │   └── Message.php            # Eloquent model with JSON attachments casting
│   └── Services/
│       └── ChatbotService.php     # Multi-engine router, OpenRouter + Gemini client, FLUX generator
├── config/
│   └── services.php               # Gemini & OpenRouter credentials & model mappings
├── database/
│   └── migrations/                # Schema definitions (conversations, messages, attachments)
├── public/
│   ├── uploads/                   # Local storage for user attachments
│   └── Nexus_AI_Documentation.pdf # Comprehensive technical PDF guide
├── resources/
│   └── views/
│       └── chat.blade.php         # Reactive Tailwind UI with Markdown, Highlight.js & DOMPurify
├── routes/
│   └── web.php                    # Application routes (streaming, upload, CRUD)
└── tests/
    └── Feature/
        └── ChatTest.php           # Automated Pest test suite (10 test cases, 33 assertions)
```

---

## 🚀 Quick Start Guide

### Prerequisites
* **PHP:** >= 8.2 (PHP 8.3 recommended)
* **Composer:** Latest version
* **Node.js & npm:** >= 18.x
* **MySQL:** Database server (XAMPP / MySQL 8.x)
* **API Keys:**
  * [OpenRouter API Key](https://openrouter.ai/keys) (powers Claude, DeepSeek, ChatGPT, Grok)
  * [Google AI Studio Gemini Key](https://aistudio.google.com/app/apikey) (powers Gemini models & vision fallback)

---

### Step 1: Clone the Repository
```bash
git clone https://github.com/your-username/nexus-ai-studio.git
cd nexus-ai-studio
```

### Step 2: Install Dependencies
```bash
composer install
npm install
```

### Step 3: Environment Configuration
Copy the example environment file:
```bash
cp .env.example .env
php artisan key:generate
```

Configure `.env` with your database and API keys:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=CHATBOT
DB_USERNAME=root
DB_PASSWORD=

AI_PROVIDER=auto
AI_MODEL=auto

# OpenRouter (Powers Claude, DeepSeek, ChatGPT, Grok)
OPENROUTER_API_KEY=sk-or-v1-your-openrouter-key-here
OPENROUTER_API_URL=https://openrouter.ai/api/v1/chat/completions

# Google Gemini (Powers Native Gemini & Vision)
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-3.1-flash-lite
GEMINI_API_URL=https://generativelanguage.googleapis.com/v1beta/openai/chat/completions
```

Clear cached configuration:
```bash
php artisan config:clear
```

### Step 4: Run Migrations
Ensure MySQL database `CHATBOT` is created, then run:
```bash
php artisan migrate
```

### Step 5: Run Automated Tests
```bash
php vendor/bin/pest
```
Expected output:
```
✓ tests/Feature/ChatTest.php (10 passed, 33 assertions)
```

### Step 6: Start Application
```bash
php artisan serve --port=8000
```
Open **`http://localhost:8000`** in your browser.

---

## 🎨 Example Queries & Smart Routing Matrix

| User Prompt | Auto-Routed Model | Routing Specialization |
| :--- | :--- | :--- |
| `hulk all movies list` | **Claude 3.5 Sonnet** | Character & Pop Culture Specialist |
| `php ecommerce landing page code` | **Claude 3.5 Sonnet** | Full-Stack Architecture & Web Frameworks |
| `refactor this python api service` | **Alibaba Qwen 2.5 Coder** | High-Performance Code Refactoring & Algorithms |
| `compare philosophy of stoicism and existentialism` | **Meta Llama 3.3 70B** | Open-Weights Deep Logic & Analytical Reasoning |
| `search the web for gold price today` | **Real-Time Web Search AI** | Live Internet Grounding & Real-Time Citations |
| `give me pdf of sql basics` | **DeepSeek V3** | Code Optimization & SQL Engineering |
| `solve dijkstra algorithm step by step` | **DeepSeek R1** | Deep Step-by-Step Reasoning & Algorithms |
| `spider ki image banao` | **FLUX AI** | Photorealistic 1024px Image Synthesis |
| `crypto price and market trends today` | **xAI Grok 2** | Real-Time Insights & Current Events |
| `what is the distance to the moon` | **ChatGPT 4o-mini** | Fast Everyday Intelligence |
| *[Uploaded Screenshot / Invoice]* | **Google Gemini** | Multimodal Vision & Document OCR |

---

## 📜 License
This project is open-source software licensed under the [MIT License](LICENSE).
