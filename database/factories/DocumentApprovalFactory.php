<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use App\Models\DocumentApproval;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentApprovalFactory extends Factory
{
    protected $model = DocumentApproval::class;

    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'submitted_by' => User::factory(),
            'approver_id' => null,
            'status' => 'pending',
            'approval_note' => null,
            'submitted_at' => now(),
            'reviewed_at' => null,
        ];
    }
}
