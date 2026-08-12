<?php

namespace App\Sso;

final readonly class SsoConnectionResult
{
    public function __construct(
        public bool $ok,
        public string $message,
    ) {}
}
