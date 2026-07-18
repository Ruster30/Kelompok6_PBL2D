<?php

namespace App\Http\Controllers;

use App\Services\CompanyProfileService;
use Barryvdh\DomPDF\Facade\Pdf;

class CompanyProfileController extends Controller
{
    public function __construct(
        private CompanyProfileService $companyProfileService
    ) {}

    public function downloadPdf()
    {
        $data = $this->companyProfileService->getCompanyData();

        $pdf = Pdf::loadView("pdf.company-profile", $data)
            ->setPaper([0, 0, 1280, 720], "landscape")
            ->setOptions([
                "dpi"                  => 150,
                "defaultFont"          => "sans-serif",
                "isRemoteEnabled"      => false,
                "isHtml5ParserEnabled" => true,
                "chroot"               => public_path(),
            ]);

        $filename = "Company-Profile-Alpha-Organizer-" . now()->format("Ymd") . ".pdf";

        return $pdf->download($filename);
    }
}