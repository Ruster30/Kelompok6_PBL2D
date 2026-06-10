<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        return view('vendor.setting');
    }
}