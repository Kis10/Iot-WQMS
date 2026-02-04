<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ChatMessage;

class GeminiController extends Controller
{
    public function index()
    {
        $messages = ChatMessage::where("user_id", auth()->id())
            ->latest()
            ->take(20)
            ->get()
            ->reverse();

        return view("chat", compact("messages"));
    }

    public function chat(Request $request)
    {
        $request->validate([
            "prompt" => "required|string"
        ]);

        $apiKey = config("services.gemini.key");

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}",
            [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $request->prompt]
                        ]
                    ]
                ]
            ]
        );

        $reply =
            $response["candidates"][0]["content"]["parts"][0]["text"]
            ?? "No response from Gemini";

        // ✅ Save chat in database
        ChatMessage::create([
            "user_id" => auth()->id(),
            "prompt" => $request->prompt,
            "reply" => $reply,
        ]);

       return response()->json([
    "prompt" => $request->prompt,
    "reply"  => $reply
]);

    }
}
