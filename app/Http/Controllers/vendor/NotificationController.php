<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        return view('vendor.notification');
    }
}