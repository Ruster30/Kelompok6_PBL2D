<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\User;

readonly class VerifyDocumentDTO
{
    public const SOURCE_PUBLIC = "public";
    public const SOURCE_ADMIN  = "admin";
    public const SOURCE_API    = "api";
    public const SOURCE_MOBILE = "mobile";
    public const SOURCE_SYSTEM = "system";

    public function __construct(
        public string $token,
        public ?User $verifiedBy = null,
        public string $ipAddress = "",
        public string $userAgent = "",
        public string $source = self::SOURCE_PUBLIC,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            token: $data["token"],
            verifiedBy: $data["verified_by"] ?? null,
            ipAddress: $data["ip_address"] ?? "",
            userAgent: $data["user_agent"] ?? "",
            source: $data["source"] ?? self::SOURCE_PUBLIC,
        );
    }
}

