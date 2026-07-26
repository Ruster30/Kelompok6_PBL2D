<?php

declare(strict_types=1);

namespace App\Http\Controllers\DDMS;

use App\Http\Requests\DDMS\UpdateSettingRequest;
use App\Http\Resources\DDMS\DdmsSettingResource;
use App\Models\DdmsSetting;
use App\Services\DdmsSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class DdmsSettingController extends Controller
{
    public function __construct(
        private readonly DdmsSettingService $settingService,
    ) {}

    public function index(): JsonResource
    {
        $this->authorize('viewAny', DdmsSetting::class);

        return DdmsSettingResource::collection(
            $this->settingService->getAllSettings(),
        );
    }

    public function show(DdmsSetting $setting): JsonResource
    {
        $this->authorize('view', $setting);

        return new DdmsSettingResource($setting);
    }

    public function store(UpdateSettingRequest $request): JsonResource
    {
        $this->authorize('create', DdmsSetting::class);

        $setting = $this->settingService->createSetting($request->validated());

        return new DdmsSettingResource($setting);
    }

    public function update(UpdateSettingRequest $request, DdmsSetting $setting): JsonResource
    {
        $this->authorize('update', $setting);

        $this->settingService->updateSetting(
            $request->toDTO()->key,
            $request->toDTO()->value,
        );

        return new DdmsSettingResource($setting->fresh());
    }

    public function destroy(DdmsSetting $setting): JsonResponse
    {
        $this->authorize('delete', $setting);

        $this->settingService->deleteSetting($setting);

        return response()->json(['message' => 'Pengaturan berhasil dihapus.']);
    }
}
