<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\DocumentTemplate;
use App\Repositories\Contracts\DocumentTemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DocumentTemplateRepository implements DocumentTemplateRepositoryInterface
{
    public function __construct(
        private readonly DocumentTemplate $model,
    ) {}

    public function all(): Collection
    {
        return $this->model->latest()->get();
    }

    public function findActive(): Collection
    {
        return $this->model->where('is_active', true)->latest()->get();
    }

    public function findById(int $id): ?DocumentTemplate
    {
        return $this->model->find($id);
    }

    public function findByCode(string $code): ?DocumentTemplate
    {
        return $this->model->where('code', $code)->first();
    }

    public function create(array $data): DocumentTemplate
    {
        return $this->model->create($data);
    }

    public function update(DocumentTemplate $template, array $data): DocumentTemplate
    {
        $template->update($data);
        return $template->fresh();
    }

    public function delete(DocumentTemplate $template): void
    {
        $template->delete();
    }
}
