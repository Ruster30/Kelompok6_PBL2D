<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\DdmsSetting;
use Illuminate\Database\Eloquent\Collection;

interface DdmsSettingRepositoryInterface
{
    /** Ambil semua setting */
    public function all(): Collection;

    /** Cari setting berdasarkan key */
    public function findByKey(string $key): ?DdmsSetting;

    /** Dapatkan nilai setting dengan default fallback */
    public function getValue(string $key, mixed $default = null): mixed;

    /** Set nilai setting (create or update) */
    public function setValue(string $key, mixed $value, ?string $description = null): void;

    /** Buat setting baru */
    public function create(array $data): DdmsSetting;

    /** Update setting */
    public function update(DdmsSetting $setting, array $data): DdmsSetting;

    /** Hapus setting */
    public function delete(DdmsSetting $setting): void;
}
