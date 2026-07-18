<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Team;

class LandingPageService
{
    public function getLandingPageData(): array
    {
        $services   = Service::where("is_active", true)->orderBy("urutan")->get();
        $teams      = Team::where("is_active", true)->orderBy("urutan")->get();
        $portfolios = Portfolio::where("is_active", true)->get();
        $clients    = Client::where("is_active", true)->get();

        return compact("services", "teams", "portfolios", "clients");
    }
}