<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Show approved, non-removed users (blocked users stay visible with label)
        $users = \App\Models\User::where('role', '!=', 'admin')
                                 ->where('is_approved', true)
                                 ->whereNull('removed_at')
                                 ->get();
        // Also exclude logs for admins
        $logs = \App\Models\LoginActivity::with('user')->whereHas('user', function($q) {
            $q->where('role', '!=', 'admin');
        })->latest()->paginate(20);

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
        $user->update([
            'is_approved' => true,
            'is_blocked' => false,
            'removed_at' => null,
        ]);
        return redirect()->back()->with('success', 'User Approved!');
    }

    public function deny(\App\Models\User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $user->delete();
        return redirect()->back()->with('success', 'User Denied!');
    }

    public function block(\App\Models\User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $user->update(['is_blocked' => true]);
        return response()->json(['message' => 'User blocked successfully']);
    }

    public function remove(\App\Models\User $user)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $user->update([
            'is_approved' => false,
            'is_blocked' => false, // Reset block status so they can see the 'removed' message
            'removed_at' => now(),
        ]);
        return response()->json(['message' => 'User removed successfully']);
    }
}
