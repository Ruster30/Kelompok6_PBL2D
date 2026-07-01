<?php

namespace App\Http\Controllers;

use App\Services\LandingPageService;

class LandingPageController extends Controller
{
    public function __construct(
        private LandingPageService $landingPageService
    ) {}

    public function index()
    {
        $data = $this->landingPageService->getLandingPageData();

        return view("landing.index", $data);
    }
}