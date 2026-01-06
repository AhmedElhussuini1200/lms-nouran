<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js'])  
</head>

<div id="messages">
    @foreach ($messages as $msg)
        <div>
            <strong>{{ $msg->sender->name }}:</strong> {{ $msg->message }}
        </div>
    @endforeach
</div>

@vite(['resources/js/app.js'])  {{-- This includes echo.js --}}

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const currentUserId = @json( $currentUser->id );
        console.log(currentUserId);//maha
        const otherUserId = {{ $user->id }}; //omer

        
        if (typeof window.Echo === 'undefined') {
            console.error("Echo is not defined. Make sure app.js is loaded before this script.");
            return;
        }

        window.Echo.channel(`chat.${currentUserId}`)
            .listen('.message.sent', (e) => {
                console.log("📥 Message received in real-time:", e);

                    const container = document.getElementById('messages');
                    const messageElement = document.createElement('div');
                    messageElement.textContent = `${e.sender.name}: ${e.message}`;
                    container.appendChild(messageElement);
            });
    });

    console.log(window.Echo)

</script>
