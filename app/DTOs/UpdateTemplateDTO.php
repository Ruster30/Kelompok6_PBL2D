<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\DocumentTemplate;

readonly class UpdateTemplateDTO
{
    public function __construct(
        public DocumentTemplate $template,
        public string $name,
        public ?string $code = null,
        public string $bladeView = "",
        public ?string $description = null,
        public bool $isActive = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            template: $data["template"],
            name: $data["name"],
            code: $data["code"] ?? null,
            bladeView: $data["blade_view"] ?? "",
            description: $data["description"] ?? null,
            isActive: $data["is_active"] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            "name"        => $this->name,
            "code"        => $this->code,
            "blade_view"  => $this->bladeView,
            "description" => $this->description,
            "is_active"   => $this->isActive,
        ];
    }
}

