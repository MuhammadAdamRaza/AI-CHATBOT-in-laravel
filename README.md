# Nexus AI Studio — Advanced Multimodal AI Workspace


<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12"></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3"></a>
  <a href="https://ai.google.dev/"><img src="https://img.shields.io/badge/Google%20Gemini-Official%20API-4285F4?style=for-the-badge&logo=google&logoColor=white" alt="Google Gemini"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS"></a>
  <a href="https://www.mysql.com/"><img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-emerald?style=for-the-badge" alt="MIT License"></a>
</p>

---

## 🌟 Overview

**Nexus AI Studio** is an enterprise-grade multimodal AI chatbot platform built on **Laravel 12 (PHP 8.3)** and powered by **Google Gemini's official Generative Language API**. 

Designed to deliver a real ChatGPT/Claude workspace experience, it features **real-time Server-Sent Events (SSE) token streaming**, **instant AI image generation**, **multimodal vision & document analysis**, a **real-time model switcher**, and **persistent MySQL thread management**.

---

## ✨ Key Features

### ⚡ 1. Real-Time Token Streaming (Server-Sent Events)
* Instant, low-latency token-by-token response streaming via native PHP Generators (`yield`) and Server-Sent Events (`text/event-stream`).
* Live blinking cursor, animated response rendering, and immediate interactive stop generation controls.

### 🎨 2. Autonomous AI Image Generation (Text-to-Image)
* **Instant Text-to-Image Engine:** Formulates vivid, photorealistic visual prompts powered by neural FLUX/SDXL models via Pollinations.ai.
* **Deterministic Intent Interceptor:** Automatically detects image generation requests (e.g., *"spider ki image bana kr do"*, *"generate an image of a cybernetic tiger"*, *"draw a futuristic car"*), and renders high-definition graphics directly inside the chat bubble without conversational lag.
* **Custom Image Styling:** Supports anime, 3D renders, cyberpunk, cinematic photography, and hyper-realistic art styles.

### 👁️ 3. Multimodal Vision & Document Analysis (Image-to-Text)
* **Universal Uploads:** Supports Images (`PNG`, `JPG`, `WEBP`, `GIF`), Documents (`PDF`, `TXT`, `CSV`, `MD`), and Code files (`PHP`, `JS`, `PY`, etc.).
* **Multiple Ingestion Methods:** 
  * Click to attach (`+` button).
  * Direct file Drag & Drop with full-screen backdrop overlay.
  * Direct clipboard screenshot paste (`Ctrl + V`).
* **Base64 Local Vision Pipeline:** Automatically transforms images into Base64 Data URIs, allowing seamless visual understanding on local development environments where external servers cannot probe `localhost`.

### 🎛️ 4. ChatGPT-Style Model Switcher
Switch between Google Gemini models on the fly through an interactive dropdown popover:
* **Gemini 3.1 Flash (`gemini-3.1-flash-lite`):** Default ultra-fast workhorse for instantaneous code, writing, and everyday assistance.
* **Gemini 3.5 Flash (`gemini-3.5-flash-lite`):** High-throughput model with 1M token context window for large files and long documents.
* **Gemini 4 Argon (`gemini-4-argon`):** Deep reasoning flagship model for complex mathematical logic, system architecture, and algorithmic design.
* **Gemini 3.8 Flash (`gemini-3.8-flash`):** Balanced multimodal intelligence for multi-step workflows.

### 🛡️ 5. Enterprise Error Shield Architecture
* **Zero Technical Leakage:** End-users are never exposed to raw API errors, 401 Unauthorized codes, expired token alerts, or internal `.env` advice.
* **Graceful Fallbacks:** Server-side failures (such as high-demand spikes 503 or rate limits 429) automatically trigger silent fallback to high-availability models while logging technical diagnostics strictly to `storage/logs/laravel.log`.

### 🎙️ 6. Voice Input & Text-to-Speech
* **Speech-to-Text (STT):** Dictate messages naturally using the microphone via the Web Speech API.
* **Text-to-Speech (TTS):** 1-click read-aloud button on assistant responses with auto-play toggle.

### 💾 7. Relational MySQL Thread Persistence
* All conversations, message histories, timestamps, and attachment metadata are securely stored in MySQL (`CHATBOT` database).
* Clean collapsible drawer sidebar on mobile, tablet, and desktop for managing past threads.

---

## 🏗️ Architecture & File Breakdown

```
ai_chatbots/
├── app/
│   ├── Http/Controllers/
│   │   └── ChatController.php      # SSE streaming, file uploads, model & persona registry
│   ├── Models/
│   │   ├── Conversation.php       # Eloquent model for chat threads
│   │   └── Message.php            # Eloquent model with JSON attachments casting
│   └── Services/
│       └── ChatbotService.php     # Google Gemini API client, image interceptor, auto-fallback
├── config/
│   └── services.php               # Gemini credentials & connection settings
├── database/
│   └── migrations/                # Schema definitions (conversations, messages, attachments)
├── public/
│   ├── uploads/                   # Direct web-accessible storage for uploaded media
│   └── Nexus_AI_Documentation.pdf # Comprehensive technical PDF guide
├── resources/
│   └── views/
│       └── chat.blade.php         # Reactive Tailwind UI workspace with Markdown & Highlight.js
├── routes/
│   └── web.php                    # Application routes (streaming, upload, CRUD)
└── .env                           # Environment configuration
```

---

## 🚀 Quick Start Guide

### Prerequisites
* **PHP:** >= 8.2 (PHP 8.3 recommended)
* **Composer:** Latest version
* **Node.js & npm:** >= 18.x
* **MySQL:** Database server (XAMPP / MySQL 8.x)
* **Google Gemini API Key:** Get a free key from [Google AI Studio](https://aistudio.google.com/app/apikey)

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

### Step 3: Environment Setup
Copy the example environment file:
```bash
cp .env.example .env
```

Generate your application key:
```bash
php artisan key:generate
```

Open `.env` and configure your MySQL database and Google Gemini API credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=CHATBOT
DB_USERNAME=root
DB_PASSWORD=

AI_PROVIDER=gemini
GEMINI_API_KEY=your_gemini_api_key_here
AI_MODEL=gemini-3.1-flash-lite
AI_API_URL=https://generativelanguage.googleapis.com/v1beta/openai/chat/completions
```

Flush configuration cache:
```bash
php artisan config:clear
```

### Step 4: Run Database Migrations
Make sure your MySQL database (`CHATBOT`) exists, then execute:
```bash
php artisan migrate
```

### Step 5: Start the Application
Run the Laravel development server:
```bash
php artisan serve --port=8000
```

Open your browser and visit:
👉 **`http://127.0.0.1:8000`**

---

## 🎨 Example Prompts

| Goal | Example Query |
| :--- | :--- |
| **Generate AI Image** | `spider ki image bana kr do` or `generate an image of a cybernetic tiger in a futuristic neon jungle` |
| **Code Architecture** | `Write a clean Laravel FormRequest and API Resource for a multi-tenant billing system.` |
| **Document Review** | *Upload any code file or PDF* ➔ `Identify security vulnerabilities and optimize query performance.` |
| **Vision Analysis** | *Attach an architecture diagram or screenshot* ➔ `Explain the data flow and suggest structural improvements.` |

---

## 📄 Complete Documentation PDF
A comprehensive, vector-formatted technical documentation PDF is included directly in the repository:
* **File:** [`Nexus_AI_Documentation.pdf`](public/Nexus_AI_Documentation.pdf)
* When running locally, download directly via browser: `http://127.0.0.1:8000/Nexus_AI_Documentation.pdf`

---

## 🤝 Contributing
Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/your-username/nexus-ai-studio/issues).

---

## 📜 License
This project is open-source software licensed under the [MIT License](LICENSE).
