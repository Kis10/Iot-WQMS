<!DOCTYPE html>
<html>
<head>
    <title>Gemini Chat</title>
</head>
<body>

<h2>Laravel Gemini Chat</h2>

<input type="text" id="prompt" placeholder="Ask something">
<button onclick="send()">Send</button>

<p><b>Reply:</b></p>
<pre id="reply"></pre>

<script>
async function send() {
    let prompt = document.getElementById("prompt").value;

    let res = await fetch("/api/chat", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify({ prompt })
    });

    let data = await res.json();
    document.getElementById("reply").innerText = data.reply;
}
</script>

</body>
</html>
