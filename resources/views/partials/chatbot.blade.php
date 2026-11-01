{{-- Chatbot Widget - Swaratani IoT --}}
<div id="chatbot-widget">
    {{-- Floating Bubble Button --}}
    <button id="chatbot-bubble" title="Chat dengan Swaratani Bot" aria-label="Buka Chatbot">
        <i class="bi bi-chat-dots-fill" id="bubble-icon-chat"></i>
        <i class="bi bi-x-lg d-none" id="bubble-icon-close"></i>
        <span class="bubble-badge" id="bubble-badge">1</span>
    </button>

    {{-- Chat Window --}}
    <div id="chatbot-window" class="chatbot-hidden">
        {{-- Header --}}
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar">
                    <i class="bi bi-robot"></i>
                </div>
                <div>
                    <div class="chatbot-name">Swaratani Bot</div>
                    <div class="chatbot-status"><span class="status-dot"></span> Online</div>
                </div>
            </div>
            <button class="chatbot-close" id="chatbot-close-btn" aria-label="Tutup Chat">
                <i class="bi bi-dash-lg"></i>
            </button>
        </div>

        {{-- Messages Area --}}
        <div class="chatbot-messages" id="chatbot-messages">
            {{-- Welcome message --}}
            <div class="msg bot">
                <div class="msg-avatar"><i class="bi bi-robot"></i></div>
                <div class="msg-bubble">
                    Halo! 👋 Saya <strong>Swaratani Bot</strong>.<br>
                    Ada yang bisa saya bantu tentang sistem IoT?
                </div>
            </div>
            {{-- Quick Replies --}}
            <div class="quick-replies" id="quick-replies">
                <button class="quick-btn" data-msg="bantuan">📋 Lihat Fitur</button>
                <button class="quick-btn" data-msg="cara monitoring">📊 Monitoring</button>
                <button class="quick-btn" data-msg="cara tambah device">📱 Tambah Device</button>
                <button class="quick-btn" data-msg="cara kontrol relay">🔌 Kontrol Output</button>
                <button class="quick-btn" data-msg="report bug">🐛 Report Bug</button>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="chatbot-input-area">
            <input type="text" id="chatbot-input" placeholder="Ketik pesan..." autocomplete="off" maxlength="500">
            <button id="chatbot-send" aria-label="Kirim pesan">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>
</div>

<style>
    /* ===== Chatbot Widget Styles ===== */
    #chatbot-widget {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }

    /* Bubble Button */
    #chatbot-bubble {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #0e5f8a 0%, #0284c7 50%, #38bdf8 100%);
        color: #fff;
        font-size: 1.5rem;
        cursor: pointer;
        box-shadow: 0 8px 30px rgba(14, 95, 138, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        animation: bubblePulse 3s ease-in-out infinite;
    }

    #chatbot-bubble:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 40px rgba(14, 95, 138, 0.5);
    }

    #chatbot-bubble:active {
        transform: scale(0.95);
    }

    @keyframes bubblePulse {

        0%,
        100% {
            box-shadow: 0 8px 30px rgba(14, 95, 138, 0.4);
        }

        50% {
            box-shadow: 0 8px 30px rgba(14, 95, 138, 0.6), 0 0 0 12px rgba(14, 95, 138, 0.1);
        }
    }

    .bubble-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        animation: badgeBounce 2s ease-in-out infinite;
    }

    @keyframes badgeBounce {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.15);
        }
    }

    .bubble-badge.d-none {
        display: none !important;
    }

    /* Chat Window */
    #chatbot-window {
        position: absolute;
        bottom: 76px;
        right: 0;
        width: 380px;
        max-height: 520px;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(14, 95, 138, 0.15);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(14, 95, 138, 0.08);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: bottom right;
    }

    #chatbot-window.chatbot-hidden {
        opacity: 0;
        transform: scale(0.8) translateY(20px);
        pointer-events: none;
        visibility: hidden;
    }

    #chatbot-window.chatbot-visible {
        opacity: 1;
        transform: scale(1) translateY(0);
        pointer-events: auto;
        visibility: visible;
    }

    /* Header */
    .chatbot-header {
        background: linear-gradient(135deg, #0e5f8a 0%, #0284c7 100%);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .chatbot-header-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chatbot-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .chatbot-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: white;
    }

    .chatbot-status {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        background: #4ade80;
        border-radius: 50%;
        display: inline-block;
        animation: statusPulse 2s ease-in-out infinite;
    }

    @keyframes statusPulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .chatbot-close {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: background 0.2s;
    }

    .chatbot-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Messages Area */
    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-height: 280px;
        max-height: 340px;
        background: #f8fafc;
    }

    .chatbot-messages::-webkit-scrollbar {
        width: 4px;
    }

    .chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chatbot-messages::-webkit-scrollbar-thumb {
        background: rgba(14, 95, 138, 0.2);
        border-radius: 4px;
    }

    /* Messages */
    .msg {
        display: flex;
        gap: 8px;
        max-width: 90%;
        animation: msgSlide 0.3s ease-out;
    }

    @keyframes msgSlide {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .msg.bot {
        align-self: flex-start;
    }

    .msg.user {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .msg-avatar {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0e5f8a, #38bdf8);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .msg.user .msg-avatar {
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
    }

    .msg-bubble {
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 0.85rem;
        line-height: 1.5;
        word-wrap: break-word;
        white-space: pre-line;
    }

    .msg.bot .msg-bubble {
        background: white;
        color: #0f172a;
        border: 1px solid rgba(14, 95, 138, 0.1);
        border-top-left-radius: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .msg.user .msg-bubble {
        background: linear-gradient(135deg, #0e5f8a 0%, #0284c7 100%);
        color: white;
        border-top-right-radius: 4px;
    }

    /* Typing Indicator */
    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 12px 16px;
    }

    .typing-indicator span {
        width: 7px;
        height: 7px;
        background: #94a3b8;
        border-radius: 50%;
        animation: typingBounce 1.4s ease-in-out infinite;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typingBounce {

        0%,
        60%,
        100% {
            transform: translateY(0);
        }

        30% {
            transform: translateY(-6px);
        }
    }

    /* Quick Replies */
    .quick-replies {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 4px 0;
    }

    .quick-btn {
        background: white;
        border: 1px solid rgba(14, 95, 138, 0.2);
        color: #0e5f8a;
        font-size: 0.78rem;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .quick-btn:hover {
        background: #0e5f8a;
        color: white;
        border-color: #0e5f8a;
        transform: translateY(-1px);
    }

    /* Input Area */
    .chatbot-input-area {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-top: 1px solid rgba(14, 95, 138, 0.1);
        background: white;
        gap: 8px;
        flex-shrink: 0;
    }

    #chatbot-input {
        flex: 1;
        border: 1px solid rgba(14, 95, 138, 0.15);
        border-radius: 24px;
        padding: 10px 16px;
        font-size: 0.85rem;
        outline: none;
        background: #f8fafc;
        color: #0f172a;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    #chatbot-input:focus {
        border-color: #0e5f8a;
        box-shadow: 0 0 0 3px rgba(14, 95, 138, 0.1);
    }

    #chatbot-input::placeholder {
        color: #94a3b8;
    }

    #chatbot-send {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #0e5f8a, #0284c7);
        color: white;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    #chatbot-send:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 15px rgba(14, 95, 138, 0.3);
    }

    #chatbot-send:active {
        transform: scale(0.95);
    }

    /* ===== Mobile Responsive ===== */
    @media (max-width: 768px) {
        #chatbot-widget {
            bottom: 16px;
            right: 16px;
        }

        #chatbot-bubble {
            width: 54px;
            height: 54px;
            font-size: 1.35rem;
        }

        .bubble-badge {
            width: 20px;
            height: 20px;
            font-size: 0.65rem;
        }

        #chatbot-window {
            position: fixed;
            bottom: 0;
            right: 0;
            left: 0;
            width: 100%;
            max-height: 85vh;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.2);
        }

        #chatbot-window.chatbot-hidden {
            transform: translateY(100%);
        }

        #chatbot-window.chatbot-visible {
            transform: translateY(0);
        }

        .chatbot-header {
            padding: 14px 16px;
            border-radius: 20px 20px 0 0;
        }

        .chatbot-avatar {
            width: 36px;
            height: 36px;
            font-size: 1.1rem;
        }

        .chatbot-name {
            font-size: 0.9rem;
        }

        .chatbot-messages {
            min-height: 200px;
            max-height: calc(85vh - 140px);
            padding: 12px;
            gap: 10px;
        }

        .msg {
            max-width: 92%;
        }

        .msg-bubble {
            padding: 10px 12px;
            font-size: 0.82rem;
            border-radius: 14px;
        }

        .msg-avatar {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            font-size: 0.7rem;
        }

        .quick-replies {
            gap: 5px;
        }

        .quick-btn {
            font-size: 0.74rem;
            padding: 6px 10px;
        }

        .chatbot-input-area {
            padding: 10px 12px;
            padding-bottom: calc(10px + env(safe-area-inset-bottom, 0px));
            gap: 8px;
        }

        /* Prevent iOS zoom on focus - font-size must be >= 16px */
        #chatbot-input {
            font-size: 16px;
            padding: 10px 14px;
            border-radius: 22px;
        }

        #chatbot-send {
            width: 42px;
            height: 42px;
            font-size: 1rem;
        }
    }

    /* Extra small phones */
    @media (max-width: 380px) {
        #chatbot-window {
            max-height: 90vh;
        }

        .chatbot-messages {
            max-height: calc(90vh - 130px);
            padding: 10px;
        }

        .msg-bubble {
            font-size: 0.8rem;
            padding: 8px 10px;
        }

        .quick-btn {
            font-size: 0.7rem;
            padding: 5px 8px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bubble = document.getElementById('chatbot-bubble');
        const window_ = document.getElementById('chatbot-window');
        const closeBtn = document.getElementById('chatbot-close-btn');
        const input = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send');
        const messagesEl = document.getElementById('chatbot-messages');
        const iconChat = document.getElementById('bubble-icon-chat');
        const iconClose = document.getElementById('bubble-icon-close');
        const badge = document.getElementById('bubble-badge');
        let isOpen = false;

        function toggleChat() {
            isOpen = !isOpen;
            if (isOpen) {
                window_.classList.remove('chatbot-hidden');
                window_.classList.add('chatbot-visible');
                iconChat.classList.add('d-none');
                iconClose.classList.remove('d-none');
                badge.classList.add('d-none');
                bubble.style.animation = 'none';
                input.focus();
            } else {
                window_.classList.add('chatbot-hidden');
                window_.classList.remove('chatbot-visible');
                iconChat.classList.remove('d-none');
                iconClose.classList.add('d-none');
            }
        }

        bubble.addEventListener('click', toggleChat);
        closeBtn.addEventListener('click', toggleChat);

        function scrollToBottom() {
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function addMessage(text, sender) {
            const msg = document.createElement('div');
            msg.className = `msg ${sender}`;

            const avatar = document.createElement('div');
            avatar.className = 'msg-avatar';
            avatar.innerHTML = sender === 'bot'
                ? '<i class="bi bi-robot"></i>'
                : '<i class="bi bi-person-fill"></i>';

            const bubble = document.createElement('div');
            bubble.className = 'msg-bubble';
            // Convert **text** to <strong>
            const formatted = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            bubble.innerHTML = formatted;

            msg.appendChild(avatar);
            msg.appendChild(bubble);
            messagesEl.appendChild(msg);
            scrollToBottom();
        }

        function showTyping() {
            const typing = document.createElement('div');
            typing.className = 'msg bot';
            typing.id = 'typing-msg';
            typing.innerHTML = `
            <div class="msg-avatar"><i class="bi bi-robot"></i></div>
            <div class="msg-bubble">
                <div class="typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
            messagesEl.appendChild(typing);
            scrollToBottom();
        }

        function removeTyping() {
            const typing = document.getElementById('typing-msg');
            if (typing) typing.remove();
        }

        async function sendMessage(text) {
            if (!text.trim()) return;
            addMessage(text, 'user');
            input.value = '';
            input.disabled = true;
            sendBtn.disabled = true;

            // Remove quick replies after first user message
            const qr = document.getElementById('quick-replies');
            if (qr) qr.remove();

            showTyping();

            try {
                const res = await fetch('{{ route("chatbot.respond") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: text }),
                });

                const data = await res.json();
                // Simulate brief typing delay
                await new Promise(r => setTimeout(r, 600));
                removeTyping();
                addMessage(data.reply, 'bot');
            } catch (err) {
                removeTyping();
                addMessage('⚠️ Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
            }

            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
        }

        sendBtn.addEventListener('click', () => sendMessage(input.value));
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendMessage(input.value);
            }
        });

        // Quick reply buttons
        document.querySelectorAll('.quick-btn').forEach(btn => {
            btn.addEventListener('click', () => sendMessage(btn.dataset.msg));
        });
    });
</script>