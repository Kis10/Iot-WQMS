<?php

namespace App\Http\Controllers;

use Ably\AblyRest;
use Illuminate\Http\Request;

class AblyController extends Controller
{
    public function auth(Request $request)
    {
        $apiKey = config('services.ably.key');
        if (!$apiKey) {
            return response()->json(['error' => 'Missing ABLY_API_KEY'], 500);
        }

        $ably = new AblyRest($apiKey);
        $tokenParams = [];
        $tokenRequest = $ably->auth->createTokenRequest($tokenParams);

        return response()->json($tokenRequest);
    }
}
