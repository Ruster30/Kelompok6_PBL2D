<?php

namespace App\Interfaces;

use App\Models\Document;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DocumentRepositoryInterface
{
    public function paginateWithFilters(?string $search, ?string $type): LengthAwarePaginator;

    public function create(array $data): Document;

    public function delete(Document $document): void;
}
