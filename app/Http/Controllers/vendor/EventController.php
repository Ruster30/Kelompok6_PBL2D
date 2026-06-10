<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        return view('vendor.event');
    }
}