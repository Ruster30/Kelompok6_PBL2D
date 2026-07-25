<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Exceptions\DDMS\TemplateCodeAlreadyExistsException;
use App\Repositories\Contracts\DocumentTemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * DocumentTemplateService
 *
 * Mengelola siklus hidup template dokumen DDMS.
 * Business logic di sini, query database di Repository.
 */
class DocumentTemplateService
{
    public function __construct(
        private readonly DocumentTemplateRepositoryInterface $templateRepository,
    ) {}

    /**
     * Ambil semua template (untuk admin panel)
     */
    public function getAllTemplates(): Collection
    {
        return $this->templateRepository->all();
    }

    /**
     * Ambil template aktif saja (untuk dropdown Document Builder)
     */
    public function getActiveTemplates(): Collection
    {
        return $this->templateRepository->findActive();
    }

    /**
     * Cari template berdasarkan ID
     */
    public function getTemplateById(int $id): ?DocumentTemplate
    {
        return $this->templateRepository->findById($id);
    }

    /**
     * Cari template berdasarkan kode
     */
    public function getTemplateByCode(string $code): ?DocumentTemplate
    {
        return $this->templateRepository->findByCode($code);
    }

    /**
     * Buat template baru
     *
     * @throws \RuntimeException Jika kode sudah digunakan
     */
    public function createTemplate(array $data): DocumentTemplate
    {
        // Auto-generate code jika tidak disediakan
        if (empty($data['code'])) {
            $data['code'] = Str::slug($data['name']);
        }

        // Validasi unique code
        if ($this->templateRepository->findByCode($data['code'])) {
            throw new TemplateCodeAlreadyExistsException(
                "Kode template '{$data['code']}' sudah digunakan."
            );
        }

        return $this->templateRepository->create($data);
    }

    /**
     * Update template
     *
     * @throws \RuntimeException Jika kode baru bertabrakan dengan template lain
     */
    public function updateTemplate(DocumentTemplate $template, array $data): DocumentTemplate
    {
        // Jika kode diubah, validasi unique
        if (isset($data['code']) && $data['code'] !== $template->code) {
            $existing = $this->templateRepository->findByCode($data['code']);
            if ($existing && $existing->id !== $template->id) {
                throw new \App\Exceptions\DDMS\TemplateCodeAlreadyExistsException(
                    "Kode template '{$data['code']}' sudah digunakan."
                );
            }
        }

        return $this->templateRepository->update($template, $data);
    }

    /**
     * Hapus template
     *
     * @throws \RuntimeException Jika template masih digunakan oleh dokumen
     */
    public function deleteTemplate(DocumentTemplate $template): void
    {
        // Cek apakah template masih digunakan
        if ($template->documents()->exists()) {
            throw new \App\Exceptions\DDMS\DDMSException(
                "Template '{$template->name}' tidak dapat dihapus karena masih digunakan oleh dokumen. " .
                "Non-aktifkan saja (set is_active = false)."
            );
        }

        $this->templateRepository->delete($template);
    }
}
