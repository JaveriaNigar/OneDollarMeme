<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meme Agent</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --brand-purple: #3e1e86;
            --brand-yellow: #fbbf24;
            --brand-orange: #f97316;
            --bg-body: #ffffff;
            --sidebar-bg: #ffffff;
            --chat-bg: #ffffff;
        }
        body {
            background-color: var(--bg-body);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: #333;
            overflow: hidden;
        }
        .chat-container {
            display: flex;
            height: calc(100vh - 2rem);
            max-width: 1600px;
            margin: 1rem auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .sidebar-header {
            padding: 1rem;
            /* border-bottom removed */
        }
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #e5e7eb;
        }
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--chat-bg);
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            background: #ffffff;
        }
        .message-wrapper {
            display: flex;
            width: 100%;
            padding: 0 1rem;
            max-width: 900px;
            margin: 0 auto;
            gap: 0.5rem;
        }
        .user-wrapper {
            justify-content: flex-end;
        }
        .agent-wrapper {
            justify-content: flex-start;
        }
        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.8rem;
        }
        .user-avatar {
            background-color: #5436da;
            color: white;
        }
        .agent-avatar {
            background-color: #10a37f;
            color: white;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message-block {
            display: flex;
            flex-direction: column;
            max-width: 70%;
        }
        .user-block {
            align-items: flex-end;
        }
        .agent-block {
            align-items: flex-start;
        }
        .message {
            width: fit-content;
            padding: 0.75rem 1.25rem;
            border-radius: 20px;
            line-height: 1.6;
            position: relative;
            animation: fadeIn 0.3s ease-out;
            font-size: 0.95rem;
        }
        .user-message {
            background-color: #f4f4f4;
            color: #2d2d2d;
            border-bottom-right-radius: 4px;
        }
        .agent-message, .meme-message {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            color: #2d2d2d;
            border-bottom-left-radius: 4px;
        }
        .nested-meme-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
            font-size: 0.9em;
        }
        /* Specialized meme border removed for consistency */
        .message-header {
            display: none; /* ChatGPT style doesn't use headers inside bubbles */
        }
        .input-area {
            padding: 1.5rem;
            background: white;
            /* border-top removed */
        }
        .gemini-input-container {
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 16px;
            padding: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .gemini-input-container:focus-within {
            border-color: var(--brand-purple);
            box-shadow: 0 4px 12px rgba(62, 30, 134, 0.15);
        }
        .message-input {
            width: 100%;
            padding: 0.5rem;
            border: none;
            background: transparent;
            resize: none;
            min-height: 40px;
            max-height: 200px;
            outline: none;
            font-size: 1rem;
        }
        .input-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            /* border-top removed */
        }
        .input-options {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .option-select {
            padding: 0.35rem 0.6rem;
            font-size: 0.75rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            color: #475569;
            cursor: pointer;
            outline: none;
            transition: all 0.2s;
        }
        .option-select:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
        }
        .send-button {
            background-color: var(--brand-purple);
            color: white;
            border: none;
            border-radius: 12px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
        }
        .send-button:hover {
            background-color: #31186a;
            transform: scale(1.05);
        }
        .send-button:disabled {
            background-color: #94a3b8;
            cursor: not-allowed;
            transform: none;
        }
        .typing-indicator {
            display: none;
            padding: 1rem;
            text-align: center;
            background: white;
            border-radius: 20px;
            margin-top: 0.5rem;
        }
        .dot-flashing {
            position: relative;
            width: 10px;
            height: 10px;
            border-radius: 5px;
            background-color: var(--brand-purple);
            color: var(--brand-purple);
            animation: dotFlashing 1s infinite linear alternate;
            animation-delay: .5s;
        }
        .dot-flashing::before, .dot-flashing::after {
            content: '';
            display: inline-block;
            position: absolute;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 5px;
            background-color: var(--brand-purple);
            color: var(--brand-purple);
        }
        .dot-flashing::before {
            left: -15px;
            animation: dotFlashing 1s infinite alternate;
            animation-delay: 0s;
        }
        .dot-flashing::after {
            left: 15px;
            animation: dotFlashing 1s infinite alternate;
            animation-delay: 1s;
        }
        @keyframes dotFlashing {
            0% { background-color: var(--brand-purple); }
            50%, 100% { background-color: #d1d0ff; }
        }
        .conversation-item {
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 0.5rem;
            transition: background-color 0.2s;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .conversation-item:hover {
            background-color: #f3f4f6;
        }
        .conversation-item.active {
            background-color: #e0d8f0;
            color: var(--brand-purple);
            font-weight: 500;
        }
        .conversation-item .delete-conv-btn {
            opacity: 0;
            transition: opacity 0.2s;
            color: #9ca3af;
        }
        .conversation-item:hover .delete-conv-btn {
            opacity: 1;
        }
        .delete-conv-btn:hover {
            color: #dc3545 !important;
        }
        .new-chat-btn {
            background-color: var(--brand-purple);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            width: 100%;
            margin-bottom: 1rem;
        }
        .search-box {
            position: relative;
            margin-bottom: 1rem;
        }
        .search-box input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 24px;
        }
        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        .agent-reply-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .message-actions-outside {
            display: flex;
            gap: 0.15rem;
            margin-top: 0.35rem;
            opacity: 1;
            min-width: fit-content;
            padding: 0 0.5rem;
        }
        .action-icon-btn {
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 0.9rem;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            text-decoration: none !important;
        }
        .action-icon-btn:hover {
            color: var(--brand-purple);
            transform: scale(1.1);
        }
        .toast-container {
            z-index: 1060;
        }
        .chat-area.new-chat-mode {
            justify-content: center;
            align-items: center;
        }
        .chat-area.new-chat-mode .chat-messages, 
        .chat-area.new-chat-mode .typing-indicator {
            display: none !important;
        }
        .chat-area.new-chat-mode .input-area {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            border: none;
            background: transparent;
            padding: 0 1rem;
        }
        .new-chat-heading {
            display: none;
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .chat-area.new-chat-mode .new-chat-heading {
            display: block;
        }
        .chat-area.new-chat-mode .gemini-input-container {
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        /* Single loading dot */
        .loading-dot {
            width: 10px;
            height: 10px;
            background-color: var(--brand-purple);
            border-radius: 50%;
            display: inline-block;
            animation: pulse-dot 0.8s infinite ease-in-out alternate;
        }
        @keyframes pulse-dot {
            0% { transform: scale(0.8); opacity: 0.5; }
            100% { transform: scale(1.2); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Sidebar with conversation history -->
        <div class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center mb-0" style="color: #5B2E91; font-weight: 800; font-size: 1.4rem;">
                    <img src="{{ asset('image/my-logo.jpg') }}" width="35" height="35" class="rounded-circle shadow-sm me-2" alt="Logo">
                    OneDollarMeme
                </a>
            </div>
            <div class="sidebar-content">
                <button class="new-chat-btn" id="newChatBtn">
                    <i class="fas fa-plus me-1"></i> New Chat
                </button>
                <button class="btn btn-sm btn-outline-secondary w-100 mb-3" id="searchChatsBtn">
                    <i class="fas fa-search me-1"></i> Search All Chats
                </button>
                <div id="conversationsList">
                    <!-- Conversations will be loaded here -->
                </div>
            </div>
        </div>
        
        <!-- Main chat area -->
        <div class="chat-area">
            <div class="chat-messages" id="chatMessages">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-comments fa-3x mb-3"></i>
                    <p>Start a conversation with the Meme Agent</p>
                    <p class="small">Share your stories, situations, or experiences. Ask for a meme when you're ready!</p>
                </div>
            </div>
            
            <div class="input-area">
                <div class="new-chat-heading">Enter any text to get memes</div>
                <div class="gemini-input-container">
                    <textarea 
                        class="message-input" 
                        id="messageInput" 
                        placeholder="Ask the Meme Agent anything..."
                        rows="1"
                    ></textarea>
                    
                    <div class="input-footer">
                        <div class="input-options">
                            <select class="option-select" id="brandSelect" title="Brand Campaign">
                                <option value="">Select Brand (optional)</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ (isset($selected_brand_id) && $selected_brand_id == $brand->id) ? 'selected' : '' }}>
                                        {{ $brand->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <select class="option-select" id="styleSelect" title="Style">
                                <option value="">Style (optional)</option>
                                <option value="relatable">Relatable</option>
                                <option value="savage">Savage</option>
                                <option value="wholesome">Wholesome</option>
                                <option value="desi">Desi</option>
                                <option value="absurd">Absurd</option>
                                <option value="observational">Observational</option>
                            </select>
                            
                            <select class="option-select" id="toneSelect" title="Tone">
                                <option value="">Tone (optional)</option>
                                <option value="funny">Funny</option>
                                <option value="sarcastic">Sarcastic</option>
                                <option value="dark">Dark</option>
                                <option value="wholesome">Wholesome</option>
                                <option value="cringe">Cringe</option>
                            </select>
                            
                            <select class="option-select" id="templateSelect" title="Template">
                                <option value="">Template (optional)</option>
                                <option value="AUTO">Auto</option>
                                <option value="T01">Me When</option>
                                <option value="T02">Got Me Like</option>
                                <option value="T03">Style Take</option>
                                <option value="T04">Trying To</option>
                                <option value="T05">POV</option>
                            </select>
                        </div>
                        
                        <button class="send-button" id="sendMessageBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Typing indicator moved to chat area -->
            </div>
        </div>
    </div>
    
    <!-- Search modal -->
    <div id="searchModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search Chats</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="search-box mb-3">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="globalSearchInput" class="form-control" placeholder="Search all your chats...">
                    </div>
                    <div id="searchResults">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-search fa-3x mb-3"></i>
                            <p>Enter a search term to find conversations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmationModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Delete Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-0 text-center fs-5">Are you sure you want to delete this chat?</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                    <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM elements
            const chatMessages = document.getElementById('chatMessages');
            const messageInput = document.getElementById('messageInput');
            const sendMessageBtn = document.getElementById('sendMessageBtn');
            const typingIndicator = document.getElementById('typingIndicator');
            const conversationsList = document.getElementById('conversationsList');
            const newChatBtn = document.getElementById('newChatBtn');
            const brandSelect = document.getElementById('brandSelect');
            const styleSelect = document.getElementById('styleSelect');
            const toneSelect = document.getElementById('toneSelect');
            const templateSelect = document.getElementById('templateSelect');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const userId = '{{ auth()->id() }}';

            // State variables
            let currentConversationId = null;
            let currentConversationTitle = null;
            let isLoadingConversation = false; // Flag to track if we're loading a conversation
            let lastSendTrigger = null; // Ensure send only occurs from explicit user action
            let currentAgentReplyContainer = null; // Track current agent reply container for grouping
            
            // Delete modal state
            let convIdToDelete = null;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            
            // Confirm delete button listener
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if (convIdToDelete) {
                    deleteConversation(convIdToDelete);
                    deleteModal.hide();
                    convIdToDelete = null;
                }
            });
            
            // Auto-resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 150) + 'px';
            });
            
            // Handle Enter key for sending message (Shift+Enter for new line)
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    lastSendTrigger = 'enter';
                    sendMessage();
                }
            });
            
            // Prevent any form submission on page load by clearing the input
            messageInput.value = '';
            
            // Send button click handler
            sendMessageBtn.addEventListener('click', function() {
                lastSendTrigger = 'button';
                sendMessage();
            });
            
            // New chat button handler
            newChatBtn.addEventListener('click', startNewConversation);
            
            // Load available styles and tones from the backend
            async function loadOptions() {
                try {
                    const [stylesRes, tonesRes] = await Promise.all([
                        fetch('/meme-agent/styles'),
                        fetch('/meme-agent/tones')
                    ]);

                    const stylesData = await stylesRes.json();
                    const tonesData = await tonesRes.json();

                    // Update style select options
                    if (stylesData.styles) {
                        styleSelect.innerHTML = '<option value="">Style (optional)</option>';
                        stylesData.styles.forEach(style => {
                            const option = document.createElement('option');
                            option.value = style;
                            option.textContent = style.charAt(0).toUpperCase() + style.slice(1);
                            styleSelect.appendChild(option);
                        });
                    }

                    // Update tone select options
                    if (tonesData.tones) {
                        toneSelect.innerHTML = '<option value="">Tone (optional)</option>';
                        tonesData.tones.forEach(tone => {
                            const option = document.createElement('option');
                            option.value = tone;
                            option.textContent = tone.charAt(0).toUpperCase() + tone.slice(1);
                            toneSelect.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Error loading options:', error);
                }
            }
            
            // Debounce function for search
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
            
            // Filter conversations based on search input
            function filterConversations() {
                const searchTerm = searchInput.value.toLowerCase();
                const conversationItems = document.querySelectorAll('.conversation-item');
                
                conversationItems.forEach(item => {
                    const title = item.getAttribute('data-title').toLowerCase();
                    if (title.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
            
            // Start a new conversation
            async function startNewConversation() {
                isLoadingConversation = true; // Set flag while starting new conversation
                document.querySelector('.chat-area').classList.add('new-chat-mode');
                
                try {
                    // Clear current messages
                    chatMessages.innerHTML = '';
                    currentAgentReplyContainer = null;

                    // Create new conversation in DB
                    const response = await fetch('/meme-agent/conversation', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            user_id: userId
                        })
                    });

                    const data = await response.json();
                    if (data.conversation_id) {
                        currentConversationId = data.conversation_id;
                        sessionStorage.setItem('meme_agent_active_id', currentConversationId);
                        currentConversationTitle = 'New Chat';

                        // Add welcome message removed to show clean state centered input
                        // addMessageToChat('agent', 'Hello! I\'m your Meme Agent...');

                        // Reload conversations list
                        // loadConversations(); // Removed to prevent empty chat from appearing in sidebar immediately
                    }
                } catch (error) {
                    console.error('Error creating conversation:', error);
                } finally {
                    isLoadingConversation = false; // Reset flag after starting conversation
                }
            }
            
            // Load conversations for the sidebar
            async function loadConversations() {
                try {
                    const response = await fetch(`/meme-agent/conversations/${userId}`);
                    const data = await response.json();
                    
                    conversationsList.innerHTML = '';
                    
                    if (data.conversations && data.conversations.length > 0) {
                        data.conversations.forEach(conv => {
                            const convItem = document.createElement('div');
                            convItem.className = 'conversation-item';
                            convItem.setAttribute('data-id', conv.id);
                            convItem.setAttribute('data-title', conv.title || 'Untitled Chat');
                            
                            // Truncate title if too long
                            let displayTitle = conv.title || 'Untitled Chat';
                            if (displayTitle.length > 30) {
                                displayTitle = displayTitle.substring(0, 30) + '...';
                            }
                            
                            convItem.innerHTML = `
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-truncate me-2">${displayTitle}</div>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-link btn-sm p-0 delete-conv-btn" data-id="${conv.id}" title="Delete Chat">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                            
                            // Click handler for loading conversation
                            convItem.addEventListener('click', (e) => {
                                // Don't trigger if clicking the delete button
                                if (e.target.closest('.delete-conv-btn')) return;
                                
                                loadConversation(conv.id);
                                
                                // Update active state
                                document.querySelectorAll('.conversation-item').forEach(item => {
                                    item.classList.remove('active');
                                });
                                convItem.classList.add('active');
                            });
                            
                            // Click handler for delete button
                            const deleteBtn = convItem.querySelector('.delete-conv-btn');
                            deleteBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                convIdToDelete = conv.id;
                                deleteModal.show();
                            });
                            
                            conversationsList.appendChild(convItem);
                        });
                    } else {
                        conversationsList.innerHTML = '<div class="text-muted text-center">No chats yet</div>';
                    }
                } catch (error) {
                    console.error('Error loading conversations:', error);
                }
            }
            
            // Load a specific conversation
            async function loadConversation(conversationId) {
                isLoadingConversation = true; // Set flag to indicate we're loading
                
                try {
                    const response = await fetch(`/meme-agent/conversation/${conversationId}`);
                    const data = await response.json();

                    if (data.conversation) {
                        // Handle new chat mode based on message history
                        if (data.messages && data.messages.length > 0) {
                             document.querySelector('.chat-area').classList.remove('new-chat-mode');
                        } else {
                             document.querySelector('.chat-area').classList.add('new-chat-mode');
                        }
                        
                        currentConversationId = conversationId;
                        // Save to session storage for reload
                        sessionStorage.setItem('meme_agent_active_id', conversationId);
                        
                        currentConversationTitle = data.conversation.title || 'Untitled Chat';

                        // Clear current messages
                        chatMessages.innerHTML = '';
                        currentAgentReplyContainer = null;

                        // Add messages to chat
                        data.messages.forEach(msg => {
                            if (msg.sender_type === 'agent') {
                                let content = msg.content;
                                let parsedJSON = null;
                                
                                // Attempt to parse JSON content
                                try {
                                    if (typeof content === 'string' && content.trim().startsWith('{')) {
                                        parsedJSON = JSON.parse(content);
                                    }
                                } catch (e) { /* Not JSON */ }

                                if (parsedJSON && (parsedJSON.reply || Array.isArray(parsedJSON.memes))) {
                                    // Structured JSON Message
                                    const reply = parsedJSON.reply || "";
                                    const memes = Array.isArray(parsedJSON.memes) ? parsedJSON.memes : [];

                                    // Render Reply Bubble
                                    if (reply) {
                                        addMessageToChat('agent', reply, msg.style, msg.tone, msg.template, true);
                                    }

                                    // Render Memes 
                                    if (memes.length > 0) {
                                        memes.forEach(memeText => {
                                            addMessageToChat('agent', '[MEME]' + (typeof memeText === 'object' ? memeText.caption : memeText), msg.style, msg.tone, msg.template, false);
                                        });
                                    }
                                } else {
                                    // Legacy / Plain Text Handling
                                    // Check if it looks like the old list format "1. [Template] ..."
                                    const lines = content.split('\n');
                                    let hasMemeFormat = lines.some(l => l.match(/^\d+\.\s*\[/));

                                    if (hasMemeFormat) {
                                        let legacyIntro = "";
                                        let legacyMemes = [];
                                        
                                        lines.forEach(line => {
                                            const trimmed = line.trim();
                                            if (!trimmed) return;
                                            const match = trimmed.match(/^(\d+\.\s*)?(\[.*)/);
                                            if (match) {
                                                legacyMemes.push('[MEME]' + match[2]);
                                            } else {
                                                if (legacyIntro) legacyIntro += "\n";
                                                legacyIntro += trimmed;
                                            }
                                        });

                                        if (legacyIntro) addMessageToChat('agent', legacyIntro, msg.style, msg.tone, msg.template, true);
                                        legacyMemes.forEach(m => addMessageToChat('agent', m));
                                    } else {
                                        // Standard text message (or single meme line if manually formatted)
                                        // Only start new reply if it is NOT a meme, so memes can append to previous text
                                        const isMemeLine = content.trim().startsWith('[MEME]');
                                        addMessageToChat('agent', content, msg.style, msg.tone, msg.template, !isMemeLine);
                                    }
                                }
                            } else {
                                // User Message
                                addMessageToChat('user', msg.content, msg.style, msg.tone, msg.template);
                            }
                        });

                        // Scroll to bottom
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                        
                        // Ensure input field is clear after loading conversation
                        messageInput.value = '';
                        messageInput.style.height = 'auto';
                    }
                } catch (error) {
                    console.error('Error loading conversation:', error);
                } finally {
                    isLoadingConversation = false; // Reset flag after loading
                }
            }
            
            // Add a message to the chat display
            function addMessageToChat(senderType, content, style = null, tone = null, template = null, startNewReply = false) {
                // Determine if this is a meme message
                const isMeme = typeof content === 'string' && content.startsWith('[MEME]');
                let displayContent = isMeme ? content.substring(6).trim() : content;

                if (isMeme) {
                    // Clean up meme format [TXX] and trailing tags
                    displayContent = displayContent.replace(/^\[T\d+\]\s*/, '').replace(/\s*\[[^\]]+\]$/, '').replace(/\s*—\s*/, ' ');
                    // Final safety check: strip out any ' - fallback' text
                    displayContent = displayContent.replace(/\s*-\s*fallback\s*\d*/gi, '').trim();
                }

                // USER MESSAGE LOGIC
                if (senderType === 'user') {
                    // Clear current agent container reference so next agent msg starts fresh
                    currentAgentReplyContainer = null;

                    const wrapper = document.createElement('div');
                    wrapper.className = 'message-wrapper user-wrapper';
                    
                    const messageBlock = document.createElement('div');
                    messageBlock.className = 'message-block user-block';
                    
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message user-message';
                    messageDiv.textContent = displayContent;
                    
                    const actionsDiv = document.createElement('div');
                    actionsDiv.className = 'message-actions-outside';
                    actionsDiv.innerHTML = `
                        <button class="action-icon-btn copy-meme-btn" data-meme="${escapeHtmlAttribute(displayContent)}" title="Copy">
                            <i class="far fa-copy"></i>
                        </button>
                    `;

                    messageBlock.appendChild(messageDiv);
                    messageBlock.appendChild(actionsDiv);
                    wrapper.appendChild(messageBlock);
                    chatMessages.appendChild(wrapper);

                    // Add copy event listener
                    wrapper.querySelector('.copy-meme-btn').addEventListener('click', function() {
                        navigator.clipboard.writeText(this.getAttribute('data-meme')).then(() => showToast('Copied!'));
                    });
                    
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    return;
                }

                // AGENT MESSAGE LOGIC
                
                // 1. Create specific container ONLY if forced or none exists
                if (!currentAgentReplyContainer || startNewReply) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'message-wrapper agent-wrapper';
                    
                    const messageBlock = document.createElement('div');
                    messageBlock.className = 'message-block agent-block';
                    
                    // This creates the main visual bubble
                    currentAgentReplyContainer = document.createElement('div');
                    currentAgentReplyContainer.className = 'message agent-message';
                    
                    messageBlock.appendChild(currentAgentReplyContainer);
                    wrapper.appendChild(messageBlock);
                    chatMessages.appendChild(wrapper);
                }

                if (isMeme) {
                    // 2. Append Meme (Nested Box) IN THE SAME CONTAINER
                    const memeBox = document.createElement('div');
                    memeBox.className = 'nested-meme-box';
                    
                    // Meme Text content
                    const textDiv = document.createElement('div');
                    textDiv.className = 'mb-2 fw-medium'; 
                    textDiv.innerHTML = `<i class="fas fa-quote-left text-muted me-2 small"></i>${escapeHtml(displayContent)}`;
                    memeBox.appendChild(textDiv);

                    // Meme Actions (Copy, Like, Dislike, Post)
                    const actionsRow = document.createElement('div');
                    actionsRow.className = 'd-flex justify-content-end gap-2 border-top pt-2 mt-2';
                    actionsRow.innerHTML = `
                        <button class="btn btn-sm btn-light border-0 py-0 px-2 copy-nested-btn" data-text="${escapeHtmlAttribute(displayContent)}" title="Copy Text">
                            <i class="far fa-copy small text-secondary"></i>
                        </button>
                        <button class="btn btn-sm btn-light border-0 py-0 px-2 feedback-nested-btn" data-type="up" data-meme="${escapeHtmlAttribute(displayContent)}" title="Like">
                            <i class="far fa-thumbs-up small text-success"></i>
                        </button>
                        <button class="btn btn-sm btn-light border-0 py-0 px-2 feedback-nested-btn" data-type="down" data-meme="${escapeHtmlAttribute(displayContent)}" title="Dislike">
                            <i class="far fa-thumbs-down small text-danger"></i>
                        </button>
                        <a href="{{ route('upload-meme.create') }}?title=${encodeURIComponent(displayContent)}" class="btn btn-sm btn-light border-0 py-0 px-2 text-primary fw-bold" title="Create Meme">
                            <i class="fas fa-plus small me-1"></i> Post
                        </a>
                    `;
                    memeBox.appendChild(actionsRow);

                    // Append nested box to main bubble
                    currentAgentReplyContainer.appendChild(memeBox);

                    // Add listeners
                    actionsRow.querySelector('.copy-nested-btn').addEventListener('click', function() {
                        navigator.clipboard.writeText(this.dataset.text).then(() => showToast('Meme text copied!'));
                    });

                    actionsRow.querySelectorAll('.feedback-nested-btn').forEach(btn => {
                        btn.addEventListener('click', async function() {
                            const type = this.dataset.type;
                            const memeText = this.getAttribute('data-meme');
                            try {
                                await fetch('/meme-agent/feedback', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                                    body: JSON.stringify({
                                        topic: memeText.substring(0, 50),
                                        style: style || '', tone: tone || '',
                                        meme_text: memeText, rating: type === 'up' ? 5 : 1
                                    })
                                });
                                this.classList.add(type === 'up' ? 'text-success' : 'text-danger');
                                this.disabled = true;
                                showToast('Thanks for your feedback!');
                            } catch (e) { console.error(e); }
                        });
                    });

                } else {
                    // 3. Append Text (Intro)
                    if (displayContent && displayContent.trim().length > 0) {
                        const p = document.createElement('div');
                        p.style.whiteSpace = 'pre-wrap';
                        p.textContent = displayContent;
                        currentAgentReplyContainer.appendChild(p);
                    }
                }

                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            
            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            // Escape HTML attribute
            function escapeHtmlAttribute(text) {
                return text.replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            }
            
            // Loading indicator helpers
            let currentLoadingElement = null;

            function showLoadingIndicator() {
                // Ensure chat mode is correct
                document.querySelector('.chat-area').classList.remove('new-chat-mode');
                
                // Find or create a spot for the loading indicator
                // We append it to chatMessages directly to avoid messing with currentAgentReplyContainer
                const wrapper = document.createElement('div');
                wrapper.className = 'message-wrapper agent-wrapper loading-wrapper';
                
                const messageBlock = document.createElement('div');
                messageBlock.className = 'message-block agent-block';
                
                const messageDiv = document.createElement('div');
                messageDiv.className = 'd-flex align-items-center';
                messageDiv.style.padding = '10px 1rem';
                // Min dimensions for visibility
                messageDiv.style.minWidth = '20px';
                messageDiv.style.minHeight = '20px'; 
                
                // Add the loading dot (3-dot flashing style)
                const dot = document.createElement('div');
                dot.className = 'dot-flashing';
                dot.style.marginLeft = '15px'; // Offset for the ::before dot
                messageDiv.appendChild(dot);
                
                messageBlock.appendChild(messageDiv);
                wrapper.appendChild(messageBlock);
                
                chatMessages.appendChild(wrapper);
                currentLoadingElement = wrapper;
                
                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function hideLoadingIndicator() {
                if (currentLoadingElement) {
                    currentLoadingElement.remove();
                    currentLoadingElement = null;
                }
            }

            // Send a message
            async function sendMessage() {
                const wasUserInitiated = lastSendTrigger === 'enter' || lastSendTrigger === 'button';
                document.querySelector('.chat-area').classList.remove('new-chat-mode');
                lastSendTrigger = null;

                if (!wasUserInitiated) {
                    return;
                }

                // Don't send messages if we're currently loading a conversation
                if (isLoadingConversation) {
                    return;
                }
                
                const message = messageInput.value.trim();
                if (!message) return;

                const style = styleSelect.value;
                const tone = toneSelect.value;
                const template = templateSelect.value;

                if (!currentConversationId) {
                    await startNewConversation();
                    if (!currentConversationId) {
                        showToast('Error starting conversation. Please try again.');
                        return;
                    }
                }

                addMessageToChat('user', message);
                messageInput.value = '';
                messageInput.style.height = 'auto';

                showLoadingIndicator();
                sendMessageBtn.disabled = true;

                try {
                    // Call the new conversational endpoint
                    const response = await fetch('/meme-agent/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            conversation_id: currentConversationId,
                            message: message,
                            style: style || null,
                            tone: tone || null,
                            template: template || null,
                            user_id: userId || 'default_user'
                        })
                    });
                    
                    const data = await response.json();
                    
                    hideLoadingIndicator(); // valid call, function exists in scope

                    if (data.success && data.response) {
                        // Save User Message FIRST to ensure correct chronological order in DB
                        await saveMessageToDB('user', message, style, tone, template);

                        let fullResponse = data.response;
                        let introText = "";
                        let memes = [];

                        // Detect if fullResponse is parsed object or needs parsing
                        let parsedData = null;
                        if (typeof fullResponse === 'object' && fullResponse !== null) {
                            parsedData = fullResponse;
                        } else if (typeof fullResponse === 'string') {
                            try {
                                parsedData = JSON.parse(fullResponse);
                            } catch (e) {
                                // Not JSON, treat as legacy text
                            }
                        }

                        if (parsedData) {
                             // Structured JSON path
                             introText = parsedData.reply || "";
                             if (Array.isArray(parsedData.memes) && parsedData.memes.length > 0) {
                                 // Prefix with [MEME] so addMessageToChat knows what to do
                                 // Handle both string captions and full meme objects
                                 memes = parsedData.memes.map(m => {
                                     if (typeof m === 'string') {
                                         return '[MEME]' + m;
                                     } else if (typeof m === 'object' && m.caption) {
                                         return '[MEME]' + m.caption;
                                     }
                                     return '[MEME]' + JSON.stringify(m);
                                 });
                             }
                        }
 else {
                             // Legacy Text Separation Logic
                             const lines = fullResponse.split('\n');
                             lines.forEach(line => {
                                 const trimmed = line.trim();
                                 if (!trimmed) return;

                                 // Pattern: "1. [Template] ..." or just "[Template] ..."
                                 const match = trimmed.match(/^(\d+\.\s*)?(\[.*)/);
                                 if (match) {
                                     memes.push('[MEME]' + match[2]);
                                 } else {
                                     if (introText) introText += "\n";
                                     introText += trimmed;
                                 }
                             });
                        }

                        // 1. Display Intro (Conversational part)
                        // This starts a NEW bubble (startNewReply=true)
                        if (introText) {
                            addMessageToChat('agent', introText, null, null, null, true);
                            saveMessageToDB('agent', introText); // Don't await, show immediately
                        }

                        // 2. Display Memes (Nested boxes in same bubble)
                        if (memes.length > 0) {
                            for (const memeMsg of memes) {
                                addMessageToChat('agent', memeMsg, null, null, null, false);
                                saveMessageToDB('agent', memeMsg); // Don't await
                            }
                        } else {
                            // No memes - just conversational reply (already displayed above)
                            console.log('[FRONTEND] Conversational mode - no memes displayed');
                        }

                        // Update title if needed
                        if (!currentConversationTitle || currentConversationTitle === 'New Chat') {
                             const newTitle = message.substring(0, 30);
                             await updateConversationTitle(newTitle);
                             // Reload sidebar now that we have a real conversation
                             loadConversations();
                        }
                    } else {
                        const errorMsg = data.error || 'Unknown error';
                        addMessageToChat('agent', `Sorry, I encountered an error: ${errorMsg}`);
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    hideLoadingIndicator();
                    addMessageToChat('agent', 'Sorry, I encountered a connection error. Please try again.');
                } finally {
                    sendMessageBtn.disabled = false;
                }
            }
            
            // Check if the message is a request for a meme
            function isMemeRequestMessage(message) {
                const lowerMsg = message.toLowerCase().trim();
                
                // If the message is just one or two words and one is "meme", it's likely a request
                const words = lowerMsg.split(/\s+/);
                if (words.length <= 2 && words.includes('meme')) return true;

                // Specific clear request phrases
                const clearRequests = [
                    'make me laugh', 'create a meme', 'generate meme', 
                    'give me a meme', 'can you make a meme', 'turn this into a meme',
                    'meme bana', 'meme dikhao', 'ready for meme', 'make meme',
                    'ready for the meme', 'post meme'
                ];
                if (clearRequests.some(phrase => lowerMsg.includes(phrase))) return true;

                // Check for action words + meme keywords
                const actionWords = ['create', 'generate', 'make', 'give', 'show', 'bana', 'dikhao', 'send'];
                const memeWords = ['meme', 'memes', 'mimi'];
                
                const hasAction = actionWords.some(word => lowerMsg.includes(word));
                const hasMeme = memeWords.some(word => lowerMsg.includes(word));
                
                return (hasAction && hasMeme);
            }
            
            // Send a regular chat message
            async function sendChatMessage(message) {
                try {
                    const response = await fetch('/meme-agent/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            conversation_id: currentConversationId,
                            message: message,
                            brand_id: brandSelect.value || null,
                            style: styleSelect.value || null,
                            tone: toneSelect.value || null,
                            template: templateSelect.value || null,
                            user_id: userId
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success && data.response) {
                        // Add agent response to chat
                        addMessageToChat('agent', data.response);
                        
                        // Save the user message to DB
                        await saveMessageToDB('user', message, styleSelect.value, toneSelect.value, templateSelect.value);
                        
                        // Save the agent response to DB
                        await saveMessageToDB('agent', data.response);
                    }
                } catch (error) {
                    console.error('Error sending chat message:', error);
                    addMessageToChat('agent', 'Sorry, I had trouble processing your message.');
                }
            }
            
            // Generate a meme from the conversation
            async function generateMemeFromConversation(requestMessage) {
                try {
                    const response = await fetch('/meme-agent/generate-meme-from-conversation', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            conversation_id: currentConversationId,
                            request_message: requestMessage,
                            brand_id: brandSelect.value || null,
                            style: styleSelect.value || null,
                            tone: toneSelect.value || null,
                            template: templateSelect.value || null,
                            user_id: userId
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        await saveMessageToDB('user', requestMessage, styleSelect.value, toneSelect.value, templateSelect.value);

                        // Track if we've started the reply container for this turn
                        let replyStarted = false;

                        // Add and persist the agent's conversational intro first if it exists
                        if (data.intro) {
                            addMessageToChat('agent', data.intro, null, null, null, true); // Always start new container for the first part of response
                            saveMessageToDB('agent', data.intro); // Don't await
                            replyStarted = true;
                        }

                        // Then add and persist the generated memes
                        if (data.memes && data.memes.length > 0) {
                            for (const meme of data.memes) {
                                // Extract caption if meme is an object
                                const caption = (typeof meme === 'object' && meme !== null) ? (meme.caption || JSON.stringify(meme)) : meme;
                                const memeMessage = `[MEME]${caption}`;
                                // If we haven't started the reply container yet (no intro), start it now
                                // Otherwise, continue appending to the existing container
                                addMessageToChat('agent', memeMessage, 
                                               styleSelect.value || null, 
                                               toneSelect.value || null, 
                                               templateSelect.value || null,
                                               !replyStarted); // startNewReply if not yet started
                                
                                saveMessageToDB('agent', memeMessage, styleSelect.value || null, toneSelect.value || null, templateSelect.value || null); // Don't await
                                replyStarted = true;
                            }
                        }
                        
                        if (!currentConversationTitle || currentConversationTitle === 'New Chat') {
                            await updateConversationTitle(data.summary || 'Meme Conversation');
                        }
                    } else {
                        const errorMsg = data.error ? ` (Error: ${data.error})` : '';
                        addMessageToChat('agent', `I had a bit of a brain freeze. ${errorMsg} Please try again!`);
                    }
                } catch (error) {
                    console.error('Error generating meme:', error);
                    addMessageToChat('agent', 'I encountered a technical glitch while connecting to the meme server. Please check your connection and try again.');
                }
            }
            
            // Save message to database
            async function saveMessageToDB(senderType, content, style = null, tone = null, template = null) {
                try {
                    await fetch('/meme-agent/message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            conversation_id: currentConversationId,
                            sender_type: senderType,
                            content: content,
                            style: style,
                            tone: tone,
                            template: template,
                            user_id: userId
                        })
                    });
                } catch (error) {
                    console.error('Error saving message:', error);
                }
            }
            
            // Update conversation title
            async function updateConversationTitle(title) {
                try {
                    await fetch(`/meme-agent/conversation/${currentConversationId}/title`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            title: title
                        })
                    });
                    
                    currentConversationTitle = title;
                    
                    // Update the conversation item in the sidebar
                    const activeItem = document.querySelector(`.conversation-item[data-id="${currentConversationId}"]`);
                    if (activeItem) {
                        activeItem.setAttribute('data-title', title);
                        let displayTitle = title;
                        if (displayTitle.length > 30) {
                            displayTitle = displayTitle.substring(0, 30) + '...';
                        }
                        activeItem.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-truncate me-2">${displayTitle}</div>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-link btn-sm p-0 delete-conv-btn" data-id="${currentConversationId}" title="Delete Chat">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        
                        // Re-attach delete listener since we replaced innerHTML
                        const deleteBtn = activeItem.querySelector('.delete-conv-btn');
                        if (deleteBtn) {
                            deleteBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                convIdToDelete = currentConversationId;
                                deleteModal.show();
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error updating conversation title:', error);
                }
            }
            
            // Show toast notification
            function showToast(message) {
                const toastContainer = document.getElementById('toastContainer');
                const toastId = 'toast_' + Date.now();
                const toastHtml = `
                    <div id="${toastId}" class="toast align-items-center text-white bg-dark border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2 text-success"></i> ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;
                toastContainer.insertAdjacentHTML('beforeend', toastHtml);
                const toastElement = document.getElementById(toastId);
                const bsToast = new bootstrap.Toast(toastElement, { delay: 3000 });
                bsToast.show();
                toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
            }
            
            // Delete a conversation
            async function deleteConversation(id) {
                try {
                    const response = await fetch(`/meme-agent/conversation/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        showToast('Conversation deleted');
                        
                        // If we deleted the current conversation, clear the chat area
                        if (currentConversationId == id) {
                            currentConversationId = null;
                            currentConversationTitle = null;
                            // Start a new conversation immediately to show centered input
                            startNewConversation();
                            currentAgentReplyContainer = null;
                        }
                        
                        // Reload conversations list
                        loadConversations();
                    }
                } catch (error) {
                    console.error('Error deleting conversation:', error);
                    showToast('Error deleting conversation');
                }
            }
            
            // Initialize the app
            async function initApp() {
                // Load available options
                // await loadOptions(); 
                // Using existing static options for now as per previous code structure implies

                // Load conversations list sidebar
                await loadConversations();

                // Logic: 
                // 1. If reloading page (sessionStorage has ID), load that chat.
                // 2. If fresh open, start NEW chat.
                
                const lastActiveId = sessionStorage.getItem('meme_agent_active_id');
                
                if (lastActiveId) {
                    // It's a reload or navigation within same tab
                    // Check if this ID actually exists in the list we just loaded
                    const convElement = document.querySelector(`.conversation-item[data-id="${lastActiveId}"]`);
                    if (convElement) {
                        await loadConversation(lastActiveId);
                        convElement.classList.add('active');
                    } else {
                        // ID in session but not in list (maybe deleted?), start new
                        startNewConversation();
                    }
                } else {
                    // First open in this tab -> New Chat
                    startNewConversation();
                }
                
                // Clear any potential input values
                messageInput.value = '';
                messageInput.style.height = 'auto';
            }
            
            // Global search functionality
            const searchChatsBtn = document.getElementById('searchChatsBtn');
            const searchModal = new bootstrap.Modal(document.getElementById('searchModal'));
            const globalSearchInput = document.getElementById('globalSearchInput');
            const searchResults = document.getElementById('searchResults');
            
            searchChatsBtn.addEventListener('click', function() {
                searchModal.show();
                globalSearchInput.focus();
            });
            
            globalSearchInput.addEventListener('input', debounce(globalSearch, 500));
            
            async function globalSearch() {
                const searchTerm = globalSearchInput.value.trim();
                
                if (searchTerm.length < 2) {
                    searchResults.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-search fa-3x mb-3"></i><p>Enter at least 2 characters to search</p></div>';
                    return;
                }
                
                try {
                    const response = await fetch(`/meme-agent/search-conversations?q=${encodeURIComponent(searchTerm)}`);
                    const data = await response.json();
                    
                    if (data.conversations && data.conversations.length > 0) {
                        let resultsHtml = '';
                        
                        data.conversations.forEach(conv => {
                            let displayTitle = conv.title || 'Untitled Chat';
                            if (displayTitle.length > 50) {
                                displayTitle = displayTitle.substring(0, 50) + '...';
                            }
                            
                            resultsHtml += `
                                <div class="conversation-item p-3 mb-2" data-id="${conv.id}" data-title="${escapeHtmlAttribute(conv.title || 'Untitled Chat')}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="fw-medium">${escapeHtml(displayTitle)}</div>
                                            <small class="text-muted">${new Date(conv.created_at).toLocaleDateString()}</small>
                                        </div>
                                        <i class="fas fa-chevron-right text-muted ms-2"></i>
                                    </div>
                                </div>
                            `;
                        });
                        
                        searchResults.innerHTML = resultsHtml;
                        
                        // Add click handlers to search results
                        document.querySelectorAll('#searchResults .conversation-item').forEach(item => {
                            item.addEventListener('click', function() {
                                const convId = this.getAttribute('data-id');
                                loadConversation(convId);
                                
                                // Update active state in sidebar
                                document.querySelectorAll('.conversation-item').forEach(sbItem => {
                                    sbItem.classList.remove('active');
                                });
                                const sidebarItem = document.querySelector(`.conversation-item[data-id="${convId}"]`);
                                if (sidebarItem) {
                                    sidebarItem.classList.add('active');
                                }
                                
                                searchModal.hide();
                            });
                        });
                    } else {
                        searchResults.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-search fa-3x mb-3"></i><p>No conversations found</p></div>';
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="text-center text-danger py-5"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>Error performing search</p></div>';
                }
            }
            
            // Start the app
            initApp();
        });
    </script>
</body>
</html>




