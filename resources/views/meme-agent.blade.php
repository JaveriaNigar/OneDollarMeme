<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meme Agent - OneDollarMeme</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/my-logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('image/my-logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --brand-purple: #5B2E91;
            --brand-orange: #f2994a;
            --brand-bg: #f3f4f6;
            --card-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        body {
            background-color: var(--brand-bg);
            min-height: 100vh;
            font-family: 'Figtree', sans-serif;
            color: #333;
        }

        /* Layout */
        .chat-container {
            display: flex;
            height: calc(100vh - 80px); /* Account for navbar */
            max-width: 1400px;
            margin: 0 auto;
            gap: 1.5rem;
            padding: 1.5rem 1rem;
        }

        /* Sidebar (Matching Homepage left-sidebar-box) */
        .sidebar {
            width: 300px;
            background: white;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: none;
        }
        .sidebar-header {
            padding: 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 800;
            font-size: 0.85rem;
            color: #9ca3af;
            text-transform: uppercase;
        }
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 0.75rem;
        }
        .conversation-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            color: #4b5563;
            font-weight: 600;
            font-size: 0.88rem;
            border-left: 3px solid transparent;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s;
            cursor: pointer;
            gap: 0.5rem;
        }
        .conversation-item:hover, .conversation-item.active {
            background-color: #f8f0fc;
            color: var(--brand-purple);
            border-left-color: var(--brand-purple);
        }
        .conversation-item .conv-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .conversation-item .delete-btn {
            opacity: 0;
            border: none;
            background: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 0.8rem;
            transition: opacity 0.15s, color 0.15s;
            flex-shrink: 0;
        }
        .conversation-item:hover .delete-btn {
            opacity: 1;
        }
        .conversation-item .delete-btn:hover {
            color: #dc2626;
        }
        /* Inline confirmation card */
        .delete-confirm-card {
            background: white;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            padding: 10px 12px;
            margin: 2px 0 6px 0;
            font-size: 0.8rem;
            color: #374151;
            animation: fadeIn 0.2s ease;
        }
        .delete-confirm-card p {
            margin: 0 0 8px 0;
            font-weight: 600;
        }
        .delete-confirm-card .d-flex { gap: 6px; }
        .delete-confirm-card .btn-accept {
            flex: 1;
            padding: 4px 0;
            font-size: 0.78rem;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            transition: background 0.15s;
        }
        .delete-confirm-card .btn-accept:hover { background: #b91c1c; }
        .delete-confirm-card .btn-cancel {
            flex: 1;
            padding: 4px 0;
            font-size: 0.78rem;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.15s;
        }
        .delete-confirm-card .btn-cancel:hover { background: #e5e7eb; }

        /* Chat Area (Centered Feed Style) */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            max-width: 800px;
            margin: 0 auto;
            height: 100%;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            scrollbar-width: thin;
        }

        /* Premium Message Cards */
        .message-wrapper {
            display: flex;
            width: 100%;
            gap: 1rem;
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-wrapper { justify-content: flex-end; }
        .agent-wrapper { justify-content: flex-start; }

        .message-card {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: var(--card-shadow);
            border: none;
            max-width: 85%;
            position: relative;
        }

        .user-message-card {
            background: #f8f0fc;
            border: 1px solid #e9d5ff;
            border-bottom-right-radius: 4px;
        }
        .agent-message-card {
            border-bottom-left-radius: 4px;
        }

        .message-title {
            font-weight: 800;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--brand-purple);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .message-content {
            font-size: 1rem;
            line-height: 1.7;
            color: #1e293b;
        }

        /* Specialized Trend Insight Box */
        .trend-insight-box {
            margin-top: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #fdf4ff 100%);
            border: 1px solid #f5d0fe;
            border-radius: 0.75rem;
            font-size: 0.9rem;
        }
        .trend-tag {
            display: inline-block;
            background: var(--brand-purple);
            color: white;
            padding: 2px 10px;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        /* Input Area (Modern & Floating) */
        .input-area {
            background: white;
            border-radius: 1.25rem;
            padding: 0.75rem 1.25rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
        }
        .gemini-input-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .message-input {
            flex: 1;
            border: none;
            outline: none;
            padding: 0.5rem 0;
            font-size: 1rem;
            max-height: 150px;
            resize: none;
            background: transparent;
        }
        .send-button {
            background: var(--brand-purple);
            color: white;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            cursor: pointer;
        }
        .send-button:hover {
            transform: scale(1.1) rotate(-10deg);
            background: #4a257a;
            box-shadow: 0 4px 12px rgba(91, 46, 145, 0.3);
        }

        .btn-outline-purple {
            border: 1px solid var(--brand-purple);
            color: var(--brand-purple);
        }
        .btn-outline-purple:hover {
            background: var(--brand-purple);
            color: white;
        }

        /* Indicators */
        .typing-indicator {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse { 0%, 100% { opacity: 0.5; } 50% { opacity: 1; } }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* Responsive Design */
        @media (max-width: 991.98px) {
            .chat-container {
                padding: 1rem 0.75rem;
                gap: 1rem;
            }
            .sidebar {
                width: 260px;
            }
        }

        @media (max-width: 767.98px) {
            .chat-container {
                height: calc(100vh - 120px);
                padding: 0.75rem 0.5rem;
                gap: 0;
                flex-direction: column;
            }

            /* Hide sidebar on mobile, show only new chat button in sidebar area */
            .sidebar {
                display: none;
            }

            .chat-area {
                max-width: 100%;
                gap: 1rem;
            }

            .chat-messages {
                padding: 0.5rem 0.25rem;
                gap: 1rem;
            }

            .message-card {
                max-width: 95%;
                padding: 1rem;
            }

            .input-area {
                padding: 0.6rem 1rem;
                border-radius: 1rem;
                margin-bottom: 0.5rem;
            }

            .send-button {
                width: 40px;
                height: 40px;
            }

            .message-input {
                font-size: 0.95rem;
            }

            .meme-delivery-box {
                gap: 8px;
            }

            .meme-item-card {
                padding: 10px;
                font-size: 0.9rem;
            }

            .meme-actions {
                gap: 8px;
            }

            .meme-action-btn {
                font-size: 0.75rem;
                padding: 4px 8px;
            }

            .trend-insight-box {
                padding: 0.75rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 575.98px) {
            body {
                min-height: 100vh;
            }

            .chat-container {
                height: calc(100vh - 140px);
                padding: 0.5rem 0.25rem;
            }

            .message-card {
                padding: 0.875rem;
                max-width: 100%;
                border-radius: 0.75rem;
            }

            .message-title {
                font-size: 0.75rem;
            }

            .message-content {
                font-size: 0.95rem;
                line-height: 1.6;
            }

            .input-area {
                padding: 0.5rem 0.75rem;
            }

            .send-button {
                width: 36px;
                height: 36px;
            }

            .message-input {
                font-size: 0.9rem;
            }

            .typing-indicator {
                font-size: 0.8rem;
                padding: 0.4rem 0.75rem;
            }
        }

        /* Mobile Sidebar Toggle */
        .mobile-sidebar-toggle {
            display: none;
        }

        @media (max-width: 767.98px) {
            .mobile-sidebar-toggle {
                display: block;
                position: fixed;
                bottom: 80px;
                left: 15px;
                right: auto;
                z-index: 1000;
                background: var(--brand-purple);
                color: white;
                border: none;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                box-shadow: 0 4px 12px rgba(91, 46, 145, 0.3);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
            }

            .mobile-sidebar-toggle:hover {
                background: #4a257a;
                transform: scale(1.05);
            }

            /* Mobile sidebar overlay */
            .sidebar-mobile-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }

            .sidebar-mobile-overlay.active {
                display: block;
            }

            /* Make sidebar slide in on mobile */
            .sidebar.sidebar-mobile-active {
                display: flex !important;
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 85%;
                max-width: 320px;
                z-index: 1050;
                border-radius: 0;
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
                animation: slideInLeft 0.3s ease;
            }

            @keyframes slideInLeft {
                from { transform: translateX(-100%); }
                to { transform: translateX(0); }
            }
        }

        /* Meme Delivery Sub-boxes */
        .meme-delivery-box {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 5px;
            width: 100%;
        }
        .meme-item-card {
            background: #ffffff;
            border: 1px solid #f3e8ff;
            border-left: 4px solid var(--brand-purple);
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 0.95rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }
        .meme-item-card:hover {
            transform: scale(1.01);
            background: #fdfbff;
            border-color: var(--brand-purple);
        }
        .meme-badge {
            display: inline-block;
            font-size: 0.65rem;
            font-weight: 800;
            color: var(--brand-purple);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
            opacity: 0.7;
        }

        /* Meme Item Actions */
        .meme-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 6px;
        }
        .meme-action-btn {
            background: #f8f7ff;
            border: 1px solid #e9d5ff;
            cursor: pointer;
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 6px;
            transition: all 0.2s;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .meme-action-btn:hover {
            background: #f3e8ff;
            color: var(--brand-purple);
            transform: translateY(-1px);
        }
        .meme-action-btn b, .meme-action-btn i {
            color: var(--brand-purple);
            font-size: 0.9rem;
            font-style: normal;
        }
    </style>
</head>
<body>
    @include('partials._main-nav')

    <div class="chat-container">
        <!-- Sidebar (History) -->
        <aside class="sidebar" id="agent-sidebar">
            <div class="sidebar-header">
                <i class="fas fa-history me-2"></i> Recent Chats
            </div>
            <div class="sidebar-content" id="conversation-list"></div>
            <div class="p-3 border-top">
                <button class="btn btn-outline-purple w-100 rounded-pill btn-sm" onclick="startNewConversation()">
                    <i class="fas fa-plus me-1"></i> New Chat
                </button>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div class="sidebar-mobile-overlay" id="sidebarOverlay"></div>

        <!-- Mobile Sidebar Toggle Button -->
        <button class="mobile-sidebar-toggle" id="mobileSidebarToggle" title="Chat History">
            <i class="fas fa-history"></i>
        </button>

        <!-- Main Chat Area -->
        <main class="chat-area">
            <!-- Mobile New Chat Button -->
            <div class="mobile-new-chat-bar d-lg-none" style="display: flex; justify-content: center; margin-bottom: 0.5rem;">
                <button class="btn btn-outline-purple btn-sm rounded-pill" onclick="startNewConversation()" style="font-size: 0.85rem; padding: 0.4rem 1.2rem;">
                    <i class="fas fa-plus me-1"></i> New Chat
                </button>
            </div>
            
            <div class="chat-messages" id="chat-messages"></div>

            <div id="typing-indicator" class="typing-indicator" style="display:none;">
                Hunting social trends...
            </div>

            <div class="input-area">
                <form id="chat-form" class="gemini-input-container">
                    <textarea id="user-input" class="message-input" placeholder="Ask about any trend or topic..." rows="1"></textarea>
                    <button type="submit" class="send-button" id="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </main>
    </div>

    <!-- Toast Component if needed -->
    @include('partials._toast')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const chatMessages = document.getElementById('chat-messages');
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('user-input');
            const sendMessageBtn = document.getElementById('send-btn');
            const conversationList = document.getElementById('conversation-list');
            const typingIndicator = document.getElementById('typing-indicator');
            
            // Mobile sidebar elements
            const mobileToggle = document.getElementById('mobileSidebarToggle');
            const sidebar = document.getElementById('agent-sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            let currentConversationId = null;
            let userId = {{ auth()->id() ?? 'null' }};

            // ─── Mobile Sidebar Toggle ─────────────────────────────
            if (mobileToggle && sidebar && overlay) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.add('sidebar-mobile-active');
                    overlay.classList.add('active');
                });

                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('sidebar-mobile-active');
                    overlay.classList.remove('active');
                });

                // Close sidebar when a conversation is clicked on mobile
                if (conversationList) {
                    conversationList.addEventListener('click', function(e) {
                        if (window.innerWidth < 768) {
                            sidebar.classList.remove('sidebar-mobile-active');
                            overlay.classList.remove('active');
                        }
                    });
                }
            }

            // ─── Init ───────────────────────────────────────────────
            async function initApp() {
                await loadConversations();

                // Persistence: Check if there's a stored conversation ID
                const savedConvId = localStorage.getItem(`activeMemeChat_${userId}`);
                if (savedConvId) {
                    // Try to load the existing conversation
                    await loadConversation(savedConvId);
                } else {
                    // Fallback to new chat if nothing stored
                    startNewConversation();
                }
            }

            // ─── Sidebar: Load conversation list ────────────────────
            async function loadConversations() {
                try {
                    const res = await fetch(`/meme-agent/history/${userId}`);
                    const data = await res.json();
                    renderConversationList(data.conversations || []);
                } catch (e) { console.error('Load conversations error:', e); }
            }

            function renderConversationList(conversations) {
                conversationList.innerHTML = '';

                if (!conversations || conversations.length === 0) {
                    conversationList.innerHTML = '<div class="text-muted small p-3 text-center" style="opacity:0.6;">No recent chats yet.<br>Start a conversation!</div>';
                    return;
                }

                conversations.slice(0, 15).forEach(conv => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'mb-1';

                    const row = document.createElement('div');
                    row.className = 'conversation-item' + (conv.id === currentConversationId ? ' active' : '');
                    row.dataset.convId = conv.id;
                    row.innerHTML = `
                        <span class="conv-text" title="${conv.title}">
                            <i class="fas fa-comment-dots me-2" style="opacity:0.4;font-size:0.75rem;"></i>${conv.title.substring(0, 26)}${conv.title.length > 26 ? '...' : ''}
                        </span>
                        <button class="delete-btn" title="Delete conversation">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `;

                    // Click row → load that conversation
                    row.querySelector('.conv-text').addEventListener('click', () => loadConversation(conv.id));

                    // Confirm card
                    const card = document.createElement('div');
                    card.className = 'delete-confirm-card';
                    card.style.display = 'none';
                    card.innerHTML = `
                        <p>Delete this chat?</p>
                        <div class="d-flex">
                            <button class="btn-accept">Delete</button>
                            <button class="btn-cancel">Cancel</button>
                        </div>
                    `;

                    row.querySelector('.delete-btn').addEventListener('click', (e) => {
                        e.stopPropagation();
                        // Close all other confirm cards first
                        document.querySelectorAll('.delete-confirm-card').forEach(c => c.style.display = 'none');
                        card.style.display = 'block';
                    });

                    card.querySelector('.btn-accept').addEventListener('click', async () => {
                        await fetch(`/meme-agent/history/${userId}/${conv.id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken }
                        });
                        wrapper.remove();
                        // If we deleted the active conversation, start a new one
                        if (conv.id === currentConversationId) startNewConversation();
                        // Check if sidebar is now empty
                        if (conversationList.children.length === 0) {
                            conversationList.innerHTML = '<div class="text-muted small p-3 text-center" style="opacity:0.6;">No recent chats yet.<br>Start a conversation!</div>';
                        }
                    });

                    card.querySelector('.btn-cancel').addEventListener('click', () => {
                        card.style.display = 'none';
                    });

                    wrapper.appendChild(row);
                    wrapper.appendChild(card);
                    conversationList.appendChild(wrapper);
                });
            }

            // ─── Load a specific conversation into the chat area ─────
            async function loadConversation(convId) {
                try {
                    const res = await fetch(`/meme-agent/history/${userId}/${convId}`);
                    const data = await res.json();

                    if (!data.success) {
                        // If it fails (e.g. deleted), start a new one
                        startNewConversation();
                        return;
                    }

                    chatMessages.innerHTML = '';
                    currentConversationId = convId;
                    localStorage.setItem(`activeMemeChat_${userId}`, convId);

                    // Mark active in sidebar
                    document.querySelectorAll('.conversation-item').forEach(el => {
                        el.classList.toggle('active', el.dataset.convId === convId);
                    });

                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            addMessageToChat(msg.role === 'user' ? 'user' : 'assistant', msg.content);
                        });
                    }
                } catch(e) {
                    console.error('Load conversation error:', e);
                    startNewConversation();
                }
            }

            // ─── New Chat ────────────────────────────────────────────
            function startNewConversation() {
                currentConversationId = 'conv_' + Date.now();
                localStorage.setItem(`activeMemeChat_${userId}`, currentConversationId);
                chatMessages.innerHTML = '';
                addMessageToChat('assistant', "Hello! I'm your Social Media Trend Agent. Ask me about any topic on Trend!");
                // Deactivate all sidebar items
                document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
            }
            window.startNewConversation = startNewConversation;

            // ─── Render a message card ───────────────────────────────
            function addMessageToChat(role, content) {
                const wrapper = document.createElement('div');
                wrapper.className = `message-wrapper ${role}-wrapper`;
                const isAgent = role === 'assistant';
                const cardClass = isAgent ? 'agent-message-card' : 'user-message-card';
                const title = isAgent ? 'Meme Agent' : 'You';
                const icon = isAgent ? 'fa-robot' : 'fa-user';
                wrapper.innerHTML = `
                    <div class="message-card ${cardClass}">
                        <div class="message-title"><i class="fas ${icon}"></i> ${title}</div>
                        <div class="message-content">${formatMessage(content, isAgent)}</div>
                    </div>
                `;
                chatMessages.appendChild(wrapper);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function formatMessage(text, isAgent = false) {
                if (!text) return '';

                // Trend insight handling
                if (text.includes('LIVE TRENDS') || text.includes('SOCIAL TRENDS')) {
                    return `<div class="trend-insight-box">
                        <span class="trend-tag">LIVE SCAN</span>
                        <div style="white-space:pre-wrap;font-size:0.85rem;color:#475569;">${text}</div>
                    </div>`;
                }

                // Check for multiple memes/jokes (separated by \n\n)
                if (isAgent && text.includes('\n\n')) {
                    const parts = text.split('\n\n').filter(p => p.trim().length > 0);
                    if (parts.length > 1) {
                        let html = '<div class="meme-delivery-box">';
                        parts.forEach((part, index) => {
                            // Escape single quotes for the onclick string
                            const cleanText = part.replace(/'/g, "\\'").replace(/\n/g, " ");
                            html += `
                                <div class="meme-item-card">
                                    <div class="meme-badge">Meme Option #${index + 1}</div>
                                    <div class="meme-text-content">${part.replace(/\n/g, '<br>')}</div>
                                    <div class="meme-actions">
                                        <button class="meme-action-btn" onclick="copyMemeText('${cleanText}', this)" title="Copy text">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                        <button class="meme-action-btn" onclick="postMeme('${cleanText}')" title="Post this meme">
                                            <i class="fas fa-plus"></i> Post
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        return html;
                    }
                }

                return text.replace(/\n/g, '<br>');
            }

 // ─── Actions ─────────────────────────────────────────────
            window.copyMemeText = function(text, btn) {
                navigator.clipboard.writeText(text).then(() => {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check" style="color:#10b981"></i> Copied!';
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                    }, 2000);
                }).catch(err => {
                    console.error('Copy failed:', err);
                });
            };

            window.postMeme = function(text) {
                window.location.href = `/upload-meme?title=${encodeURIComponent(text)}`;
            };

            // ─── Send message ────────────────────────────────────────
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const message = messageInput.value.trim();
                if (!message || sendMessageBtn.disabled) return;

                // Auto-create a conversation ID if somehow null
                if (!currentConversationId) currentConversationId = 'conv_' + Date.now();

                addMessageToChat('user', message);
                messageInput.value = '';
                messageInput.style.height = 'auto';
                typingIndicator.style.display = 'block';
                sendMessageBtn.disabled = true;

                try {
                    const res = await fetch('/meme-agent/chat', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({
                            message: message,
                            user_id: userId,
                            conversation_id: currentConversationId
                        })
                    });
                    const data = await res.json();
                    typingIndicator.style.display = 'none';

                    if (data.success) {
                        const memes = data.memes || [];
                        const reply = data.response?.reply || '';
                        const memeIntent = data.meme_intent !== false;

                        if (memeIntent && memes.length > 0) {
                            // Show reply first if exists
                            if (reply) addMessageToChat('assistant', reply);
                            // Show memes as combined content
                            const combinedContent = memes.map(m => m.caption || m).join('\n\n');
                            addMessageToChat('assistant', combinedContent);
                        } else {
                            // Normal chat response
                            addMessageToChat('assistant', reply || memes[0]?.caption || "Kuch toh bolo! 😄");
                        }

                        if (data.conversation_id) {
                            currentConversationId = data.conversation_id;
                            localStorage.setItem(`activeMemeChat_${userId}`, currentConversationId);
                        }
                        await loadConversations();
                    } else {
                        addMessageToChat('assistant', "Something went wrong. Please try again.");
                    }
                } catch (err) {
                    typingIndicator.style.display = 'none';
                    addMessageToChat('assistant', "Connection error. Is the agent running?");
                } finally {
                    sendMessageBtn.disabled = false;
                }
            });

            // ─── Keyboard shortcuts ──────────────────────────────────
            messageInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    chatForm.dispatchEvent(new Event('submit'));
                }
            });
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });

            initApp();
        });
    </script>
</body>
</html>