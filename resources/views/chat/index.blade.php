<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat</title>
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .chat-user {
            margin: 5px;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .chat-user:hover {
            background: #0056b3;
        }

        .chat-user.active {
            background: #28a745;
        }

        .chat-user.new-message {
            background: #dc3545;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        #chat-box {
            border: 1px solid #ccc;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
            margin: 20px 0;
            background: #f9f9f9;
        }

        .message {
            margin: 5px 0;
            padding: 5px;
        }

        .message.sent {
            text-align: right;
            background: #007bff;
            color: white;
            border-radius: 10px;
        }

        .message.received {
            text-align: left;
            background: #e9ecef;
            border-radius: 10px;
        }

        #chat-form {
            display: flex;
            gap: 10px;
        }

        #message {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .debug-message {
            font-size: 12px;
            color: #6c757d;
            margin: 2px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Chat Application</h3>
        <!-- Debug Information -->
        <div id="debug-info">
            <h5>Debug Information:</h5>
            <div class="debug-message">Current User ID: {{ auth()->id() }}</div>
            <div class="debug-message">Current User name: {{ auth()->user()->first_name }}</div>
            <div class="debug-message">Echo Status: <span id="echo-status">Not Connected</span></div>
            <div class="debug-message">Listening Channel: chat.{{ auth()->id() }}</div>
            <div id="debug-messages"></div>
        </div>

        <div class="users-section">
            <h4>Users</h4>
            @foreach ($users as $user)
                <button class="chat-user" data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                    {{ $user->first_name . ' ' . $user->email}}
                </button>
            @endforeach
        </div>

        <div id="current-chat" style="display: none;">
            <h4>Chat with: <span id="current-user-name"></span></h4>
            <div id="chat-box"></div>

            <form id="chat-form">
                <input type="hidden" name="receiver_id" id="receiver_id">
                <input type="text" name="message" id="message" placeholder="Type your message..." required>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    @vite(['resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let user = {{ auth()->id() }};
        console.log(user);
    </script>

    <script type='module'>
        const curUserId = {{ auth()->id() }};
        console.log(curUserId, window.Echo);
        function addDebugMessage(message) {
            const debugDiv = document.getElementById('debug-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'debug-message';
            messageDiv.textContent = new Date().toLocaleTimeString() + ': ' + message;
            debugDiv.appendChild(messageDiv);
            console.log('DEBUG:', message);
        }
        try {
            const channelName = `chat.user.6.user.12`;
            window.Echo.private(channelName)
                .listen('MessageSent', (e) => {
                    console.log('event listne good');
                    addDebugMessage('Message received via Echo: ' + JSON.stringify(e));

                    console.log('✅ Event received!', e);
                    // If we're currently chatting with the sender, show the message immediately
                    if (String(e.sender_id) === '6' || String(e.receiver_id) === '6') {
                        const chatBox = document.getElementById('chat-box');
                        const from = e.sender_id === {{ auth()->id() }} ? 'You' : e.sender_name;
                        chatBox.innerHTML += `<p><strong>${from}:</strong> ${e.message}</p>`;
                        chatBox.scrollTop = chatBox.scrollHeight;
                    } else {
                        addDebugMessage('Showing notification for user: ' + e.sender_id);
                        // Show notification on the user button
                        const btn = document.querySelector(`.chat-user[data-id="${e.sender_id}"]`);
                        if (btn) {
                            btn.classList.add('new-message');
                            addDebugMessage('Added notification to button');
                        } else {
                            addDebugMessage('Button not found for user: ' + e.sender_id);
                        }
                    }
                });

            console.log(`✅ Subscribed to ${channelName}`);
        } catch (error) {
            console.error(`❌ Failed to listen on ${channelName}:`, error);
        }

        {{--  document.addEventListener('DOMContentLoaded', function () {
            let selectedUserId = null;
            let selectedUserName = null;
            axios.defaults.withCredentials = true;

            // Debug function
            function addDebugMessage(message) {
                const debugDiv = document.getElementById('debug-messages');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'debug-message';
                messageDiv.textContent = new Date().toLocaleTimeString() + ': ' + message;
                debugDiv.appendChild(messageDiv);
                console.log('DEBUG:', message);
            }


            // Set up CSRF token for axios
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Check if Echo is available
            console.log(typeof window.Echo !== 'undefined');
            if (typeof window.Echo !== 'undefined') {
                document.getElementById('echo-status').textContent = 'Connected';
                addDebugMessage('Echo is available');

                // Listen for incoming messages
                try {
                    console.log('is run2');

                    window.currentUserId = @json(auth()->id());
                    window.Echo.private('chat.user.6.user.12')
                        .subscribed(() => {
                            addDebugMessage('Successfully subscribed to chat.{{ auth()->id() }}');
                        })
                        .error((error) => {
                            addDebugMessage('Subscription error: ' + JSON.stringify(error));
                        })
                        .listen('.MessageSent', (e) => {
                            console.log('event listne good');
                            addDebugMessage('Message received via Echo: ' + JSON.stringify(e));

                            console.log('✅ Event received!', e);
                            // If we're currently chatting with the sender, show the message immediately
                            console.log(String(e.sender_id) === selectedUserId || String(e.receiver_id) === selectedUserId);
                            if (String(e.sender_id) === selectedUserId || String(e.receiver_id) === selectedUserId) {
                                const chatBox = document.getElementById('chat-box');
                                const from = e.sender_id === {{ auth()->id() }} ? 'You' : e.sender_name;
                                chatBox.innerHTML += `<p><strong>${from}:</strong> ${e.message}</p>`;
                                chatBox.scrollTop = chatBox.scrollHeight;
                            } else {
                                addDebugMessage('Showing notification for user: ' + e.sender_id);
                                // Show notification on the user button
                                const btn = document.querySelector(`.chat-user[data-id="${e.sender_id}"]`);
                                if (btn) {
                                    btn.classList.add('new-message');
                                    addDebugMessage('Added notification to button');
                                } else {
                                    addDebugMessage('Button not found for user: ' + e.sender_id);
                                }
                            }
                        })

                } catch (error) {
                    addDebugMessage('Error setting up Echo listener: ' + error.message);
                }
            } else {
                document.getElementById('echo-status').textContent = 'Not Available';
                addDebugMessage('Echo is not available - check your bootstrap.js');
            }

            // Handle user selection
            document.querySelectorAll('.chat-user').forEach(button => {
                button.addEventListener('click', function () {
                    addDebugMessage('User button clicked: ' + this.dataset.name);

                    // Remove active class from all buttons
                    document.querySelectorAll('.chat-user').forEach(btn => {
                        btn.classList.remove('active', 'new-message');
                    });

                    // Add active class to selected button
                    this.classList.add('active');

                    selectedUserId = this.dataset.id;
                    selectedUserName = this.dataset.name;

                    document.getElementById('receiver_id').value = selectedUserId;
                    document.getElementById('current-user-name').textContent = selectedUserName;
                    document.getElementById('current-chat').style.display = 'block';

                    // Load chat history
                    loadChatHistory(selectedUserId);
                });
            });

            // Handle form submission
            document.getElementById('chat-form').addEventListener('submit', function (e) {
                e.preventDefault();

                const messageInput = document.getElementById('message');
                const message = messageInput.value.trim();

                if (!message || !selectedUserId) return;

                addDebugMessage('Sending message: ' + message + ' to user: ' + selectedUserId);

                axios.post('/chat/send', {
                    receiver_id: selectedUserId,
                    message: message
                })
                    .then(response => {
                        addDebugMessage('Message sent successfully');
                        addMessageToChat(message, 'sent', 'You');
                        messageInput.value = '';
                        scrollToBottom();
                    })
                    .catch(error => {
                        addDebugMessage('Error sending message: ' + (error.response?.data?.message || error.message));
                        console.error('Error sending message:', error);
                        alert('Error sending message. Please try again.');
                    });
            });

            // Functions
            function loadChatHistory(userId) {
                addDebugMessage('Loading chat history for user: ' + userId);
                axios.get(`/chat/messages/${userId}`)
                    .then(response => {
                        addDebugMessage('Chat history loaded: ' + response.data.length + ' messages');
                        const chatBox = document.getElementById('chat-box');
                        chatBox.innerHTML = '';

                        response.data.forEach(message => {
                            const isSent = parseInt(message.sender_id) === {{ auth()->id() }};
                            const senderName = isSent ? 'You' : (message ? message.sender.full_name : 'Unknown');
                            const messageType = isSent ? 'sent' : 'received';

                            addMessageToChat(message.message, messageType, senderName);
                        });

                        scrollToBottom();
                    })
                    .catch(error => {
                        addDebugMessage('Error loading chat history: ' + error.message);
                        console.error('Error loading messages:', error);
                    });
            }

            function addMessageToChat(message, type, senderName) {
                const chatBox = document.getElementById('chat-box');
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${type}`;
                messageDiv.innerHTML = `<strong>${senderName}:</strong> ${message}`;
                chatBox.appendChild(messageDiv);
            }

            function scrollToBottom() {
                const chatBox = document.getElementById('chat-box');
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });  --}}
    </script>

</body>

</html>
