<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentationFile;
use App\Services\AdminDocumentationService;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function __construct(
        private AdminDocumentationService $documentationService
    ) {}

    public function index(Request $request)
    {
        $data = $this->documentationService->getDocumentations(
            $request->search,
            $request->status
        );

        return view("admin.documentations.index", $data);
    }

    public function approveFile(DocumentationFile $file)
    {
        $this->documentationService->approveFile($file);

        return back()->with("success", "File dokumentasi disetujui.");
    }

    public function rejectFile(DocumentationFile $file)
    {
        $this->documentationService->rejectFile($file);

        return back()->with("success", "File dokumentasi ditolak.");
    }
}