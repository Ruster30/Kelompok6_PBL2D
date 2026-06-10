<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('vendor.dashboard');
    }
}