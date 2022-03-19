<?php

declare(strict_types=1);

namespace EScooters\Utils;

use JsonSerializable;

class BuildInfo implements JsonSerializable
{
    public function __construct(
        protected string $timestamp,
        protected int $citiesCount,
        protected int $providersCount
    ) {    }

    public function jsonSerialize(): array
    {
        return [
            "timestamp" => $this->timestamp,
            "citiesCount" => $this->citiesCount,
            "providersCount" => $this->providersCount,
        ];
    }
}
