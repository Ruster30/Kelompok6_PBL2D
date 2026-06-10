<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    public function index()
    {
        return view('vendor.schedule');
    }
}