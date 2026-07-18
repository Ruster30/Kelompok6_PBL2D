<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private AdminDashboardService $dashboardService,
    ) {}

    public function index()
    {
        return view('admin.dashboard.index', $this->dashboardService->getDashboardData());
    }
}
