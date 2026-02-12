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

    public function activities(\App\Models\User $user)
    {
        $activities = \App\Models\UserActivity::where('user_id', $user->id)
            ->latest()
            ->paginate(50);

        return view('users.activities', compact('user', 'activities'));
    }

    public function approvals()
    {
        // Only Admin
        if (!auth()->user()->isAdmin()) abort(403);
        $pendingUsers = \App\Models\User::where('is_approved', false)->get();
        return view('users.approvals', compact('pendingUsers'));
    }

    public function approve(\App\Models\User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $user->update(['is_approved' => true]);
        return redirect()->back()->with('success', 'User Approved!');
    }

    public function deny(\App\Models\User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $user->delete();
        return redirect()->back()->with('success', 'User Denied!');
    }
}
