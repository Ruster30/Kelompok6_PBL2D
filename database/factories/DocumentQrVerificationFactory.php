<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use App\Models\DocumentQrVerification;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentQrVerificationFactory extends Factory
{
    protected $model = DocumentQrVerification::class;

    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'verification_token' => bin2hex(random_bytes(16)),
            'qr_path' => 'qrcodes/' . fake()->uuid() . '.png',
            'generated_by' => User::factory(),
            'generated_at' => now(),
            'expires_at' => now()->addYear(),
        ];
    }
}
