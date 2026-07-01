<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAnalyticsService;

class AnalyticsController extends Controller
{
    public function __construct(
        private AdminAnalyticsService $analyticsService,
    ) {}

    public function index()
    {
        return view('admin.analytics.index', $this->analyticsService->getAnalyticsData());
    }
}
