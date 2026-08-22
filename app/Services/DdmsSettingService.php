<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DdmsSetting;
use App\Repositories\Contracts\DdmsSettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * DdmsSettingService
 *
 * Mengelola konfigurasi global DDMS.
 * Menyediakan antarmuka key-value yang nyaman untuk Service Layer lain.
 */
class DdmsSettingService
{
    public function __construct(
        private readonly DdmsSettingRepositoryInterface $settingRepository,
    ) {}

    /**
     * Ambil semua setting
     */
    public function getAllSettings(): Collection
    {
        return $this->settingRepository->all();
    }

    /**
     * Cari setting berdasarkan key (lengkap dengan model)
     */
    public function getSetting(string $key): ?DdmsSetting
    {
        return $this->settingRepository->findByKey($key);
    }

    /**
     * Ambil nilai setting dengan default fallback
     *
     * Ini adalah method yang paling sering digunakan oleh service lain.
     * Contoh: $this->settingService->getSettingValue('approval_pin', '123456');
     */
    public function getSettingValue(string $key, mixed $default = null): mixed
    {
        return $this->settingRepository->getValue($key, $default);
    }

    /**
     * Ambil default DDMS per jenis surat sebagai map boolean.
     *
     * Nilai default (jika setting belum tersedia):
     *   proposal       = "1"
     *   surat_kontrak  = "1"
     *   invoice        = "0"
     *   rab            = "0"
     *
     * Setting ini HANYA default untuk initial UI state.
     * Keputusan final tetap diambil dari request generate + master switch global.
     */
    public function getDdmsDefaults(): array
    {
        return [
            'surat_kontrak'  => $this->getSettingValue('ddms_default_surat_kontrak', '1') === '1',
            'invoice'        => $this->getSettingValue('ddms_default_invoice', '0') === '1',
            'rab'            => $this->getSettingValue('ddms_default_rab', '0') === '1',
        ];
    }

    /**
     * Update atau buat setting (upsert)
     *
     * @throws \InvalidArgumentException Jika key kosong
     */
    public function updateSetting(string $key, mixed $value, ?string $description = null): void
    {
        if (empty(trim($key))) {
            throw new \InvalidArgumentException('Setting key tidak boleh kosong.');
        }

        $this->settingRepository->setValue($key, $value, $description);
    }

    /**
     * Buat setting baru
     *
     * @throws \RuntimeException Jika key sudah ada
     */
    public function createSetting(array $data): DdmsSetting
    {
        if (empty($data['setting_key'])) {
            throw new \InvalidArgumentException('Setting key tidak boleh kosong.');
        }

        // Validasi unique key
        if ($this->settingRepository->findByKey($data['setting_key'])) {
            throw new \App\Exceptions\DDMS\SettingKeyAlreadyExistsException(
                "Setting dengan key '{$data['setting_key']}' sudah ada. " .
                "Gunakan updateSetting() untuk mengubah nilai."
            );
        }

        return $this->settingRepository->create($data);
    }

    /**
     * Hapus setting
     *
     * @throws \RuntimeException Jika setting tidak ditemukan
     */
    public function deleteSetting(DdmsSetting $setting): void
    {
        $this->settingRepository->delete($setting);
    }
}
