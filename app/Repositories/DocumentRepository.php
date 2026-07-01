<?php

namespace App\Repositories;

use App\Interfaces\DocumentRepositoryInterface;
use App\Models\Document;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DocumentRepository implements DocumentRepositoryInterface
{
    public function paginateWithFilters(?string $search, ?string $type): LengthAwarePaginator
    {
        $query = Document::with(['user', 'event'])->latest();

        if ($search) {
            $query->where('nama_file', 'like', '%' . $search . '%');
        }

        if ($type) {
            $query->where('tipe', $type);
        }

        return $query->paginate(10)->withQueryString();
    }

    public function create(array $data): Document
    {
        return Document::create($data);
    }

    public function delete(Document $document): void
    {
        $document->delete();
    }
}
