<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::all();
        $logs = \App\Models\LoginActivity::with('user')->latest()->paginate(20);

        return view('users.index', compact('users', 'logs'));
    }
}
