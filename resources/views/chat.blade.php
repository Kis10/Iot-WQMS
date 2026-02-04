<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <h2 class="text-2xl font-bold mb-4">
            Gemini Chat (Laravel 12)
        </h2>

        <!-- Chat Box -->
        <div id="chatBox"
            class="bg-white shadow rounded p-4 mb-4 h-96 overflow-y-scroll space-y-6">

            @foreach($messages as $msg)
                <!-- User -->
                <div>
                    <p class="font-semibold text-blue-600">You:</p>
                    <p>{{ $msg->prompt }}</p>
                </div>

                <!-- Gemini -->
                <div>
                    <p class="font-semibold text-green-600">Gemini:</p>
                    <p>{{ $msg->reply }}</p>
                </div>
            @endforeach

        </div>

        <!-- Input -->
        <div class="flex gap-2">
            <input
                id="prompt"
                type="text"
                class="w-full border rounded px-3 py-2"
                placeholder="Ask Gemini something..."
            />

            <button
                onclick="sendMessage()"
                class="bg-black text-white px-4 py-2 rounded"
            >
                Send
            </button>
        </div>

        <!-- Loading -->
        <p id="loading" class="text-gray-500 mt-2 hidden">
            Gemini is typing...
        </p>

    </div>

    <script>
        async function sendMessage() {

            let input = document.getElementById("prompt");
            let chatBox = document.getElementById("chatBox");
            let loading = document.getElementById("loading");

            let prompt = input.value.trim();
            if (!prompt) return;

            // ✅ Append user message instantly
            chatBox.innerHTML += `
                <div>
                    <p class="font-semibold text-blue-600">You:</p>
                    <p>${prompt}</p>
                </div>
            `;

            input.value = "";
            loading.classList.remove("hidden");

            // Scroll down
            chatBox.scrollTop = chatBox.scrollHeight;

            // ✅ Send AJAX request
            let response = await fetch("{{ route('chat.send') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ prompt })
            });

            let data = await response.json();

            loading.classList.add("hidden");

            // ✅ Append Gemini reply
            chatBox.innerHTML += `
                <div>
                    <p class="font-semibold text-green-600">Gemini:</p>
                    <p>${data.reply}</p>
                </div>
            `;

            // Scroll down again
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        document.getElementById("prompt").addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        sendMessage();
    }
});

    </script>
</x-app-layout>
