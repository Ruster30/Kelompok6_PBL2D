<?php

declare(strict_types=1);

namespace App\Http\Controllers\DDMS;

use App\Http\Requests\DDMS\CreateTemplateRequest;
use App\Http\Requests\DDMS\UpdateTemplateRequest;
use App\Http\Resources\DDMS\DocumentTemplateResource;
use App\Models\DocumentTemplate;
use App\Services\DocumentTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class DocumentTemplateController extends Controller
{
    public function __construct(
        private readonly DocumentTemplateService $templateService,
    ) {}

    public function index(): JsonResource
    {
        $this->authorize('viewAny', DocumentTemplate::class);

        return DocumentTemplateResource::collection(
            $this->templateService->getAllTemplates(),
        );
    }

    public function show(DocumentTemplate $template): JsonResource
    {
        $this->authorize('view', $template);

        return new DocumentTemplateResource($template);
    }

    public function store(CreateTemplateRequest $request): JsonResource
    {
        $this->authorize('create', DocumentTemplate::class);

        $template = $this->templateService->createTemplate(
            $request->toDTO()->toArray(),
        );

        return new DocumentTemplateResource($template);
    }

    public function update(UpdateTemplateRequest $request, DocumentTemplate $template): JsonResource
    {
        $this->authorize('update', $template);

        $template = $this->templateService->updateTemplate(
            $template,
            $request->toDTO()->toArray(),
        );

        return new DocumentTemplateResource($template);
    }

    public function destroy(DocumentTemplate $template): JsonResponse
    {
        $this->authorize('delete', $template);

        $this->templateService->deleteTemplate($template);

        return response()->json(['message' => 'Template berhasil dihapus.']);
    }
}
