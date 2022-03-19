<?php

declare(strict_types=1);

namespace EScooters\Models\Repositories;

use EScooters\Models\Provider;
use JsonSerializable;

class Providers implements JsonSerializable
{
    /** @var array<Provider> */
    protected array $providers = [];

    public function add(Provider $provider): static
    {
        $this->providers[$provider->getId()] = $provider;
        return $this;
    }

    public function jsonSerialize(): array
    {
        uasort($this->providers, fn(Provider $a, Provider $b) => count($b->getCities()) <=> count($a->getCities()));
        return $this->providers;
    }
}
