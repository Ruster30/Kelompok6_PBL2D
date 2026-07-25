<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\DdmsSetting;
use App\Repositories\Contracts\DdmsSettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DdmsSettingRepository implements DdmsSettingRepositoryInterface
{
    public function __construct(
        private readonly DdmsSetting $model,
    ) {}

    public function all(): Collection
    {
        return $this->model->latest()->get();
    }

    public function findByKey(string $key): ?DdmsSetting
    {
        return $this->model->where('setting_key', $key)->first();
    }

    public function getValue(string $key, mixed $default = null): mixed
    {
        $setting = $this->findByKey($key);
        return $setting?->setting_value ?? $default;
    }

    public function setValue(string $key, mixed $value, ?string $description = null): void
    {
        $this->model->updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => (string) $value,
                'description'   => $description,
            ]
        );
    }

    public function create(array $data): DdmsSetting
    {
        return $this->model->create($data);
    }

    public function update(DdmsSetting $setting, array $data): DdmsSetting
    {
        $setting->update($data);
        return $setting->fresh();
    }

    public function delete(DdmsSetting $setting): void
    {
        $setting->delete();
    }
}
