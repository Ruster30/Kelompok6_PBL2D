<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function index()
    {
        return view('vendor.task');
    }
}