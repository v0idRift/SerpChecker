<?php

namespace App\Services\Serp;

final readonly class GoogleOrganicRankResult
{
    public const STATUS_FOUND = 'found';

    public const STATUS_NOT_FOUND = 'not_found';

    public const STATUS_API_ERROR = 'api_error';

    public const STATUS_CLIENT_ERROR = 'client_error';

    public function __construct(
        public string $status,
        public string $targetDomain,
        public ?int $rankGroup = null,
        public ?int $rankAbsolute = null,
        public ?string $url = null,
        public ?string $title = null,
        public ?string $message = null,
    ) {}

    public static function found(
        string $targetDomain,
        ?int $rankGroup,
        ?int $rankAbsolute,
        ?string $url,
        ?string $title,
        ?string $apiMessage = null,
    ): self {
        return new self(
            status: self::STATUS_FOUND,
            targetDomain: $targetDomain,
            rankGroup: $rankGroup,
            rankAbsolute: $rankAbsolute,
            url: $url,
            title: $title,
            message: $apiMessage,
        );
    }

    public static function notFound(string $targetDomain, ?string $apiMessage = null): self
    {
        return new self(
            status: self::STATUS_NOT_FOUND,
            targetDomain: $targetDomain,
            message: $apiMessage,
        );
    }

    public static function apiError(string $message, string $targetDomain): self
    {
        return new self(
            status: self::STATUS_API_ERROR,
            targetDomain: $targetDomain,
            message: $message,
        );
    }

    public static function clientError(string $message, string $targetDomain): self
    {
        return new self(
            status: self::STATUS_CLIENT_ERROR,
            targetDomain: $targetDomain,
            message: $message,
        );
    }

    public function isFound(): bool
    {
        return $this->status === self::STATUS_FOUND;
    }

    public function isNotFound(): bool
    {
        return $this->status === self::STATUS_NOT_FOUND;
    }

    public function isError(): bool
    {
        return $this->status === self::STATUS_API_ERROR || $this->status === self::STATUS_CLIENT_ERROR;
    }
}
