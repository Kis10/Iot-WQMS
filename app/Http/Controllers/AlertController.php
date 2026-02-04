<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::latest()->get();
        return view('alerts', compact('alerts'));
    }
}
