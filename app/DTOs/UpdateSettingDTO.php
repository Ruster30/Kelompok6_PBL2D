<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class UpdateSettingDTO
{
    public function __construct(
        public string $key,
        public mixed $value,
        public ?string $description = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data["key"],
            value: $data["value"],
            description: $data["description"] ?? null,
        );
    }
}

