<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Client;
use App\Models\Team;

class LandingPageController extends Controller
{
    public function index()
    {
        // 1. Services
        $services = Service::where('is_active', true)->orderBy('urutan')->get();

        // 2. Team
        $teams = Team::where('is_active', true)->orderBy('urutan')->get();

        // 3. Portfolio
        $portfolios = Portfolio::where('is_active', true)->get();

        // 4. Clients
        $clients = Client::where('is_active', true)->get();

        return view('landing.index', compact(
            'services', 'teams', 'portfolios', 'clients'
        ));
    }
}
