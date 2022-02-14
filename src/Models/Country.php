<?php

declare(strict_types=1);

namespace EScooters\Models;

use JsonSerializable;

class Country implements JsonSerializable
{
    protected array $cities = [];
    protected array $providers = [];

    public function __construct(
        protected string $id,
        protected string $name,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addProvider(Provider $provider): static
    {
        $this->providers[$provider->getId()] = $provider;

        return $this;
    }

    public function addCity(City $city): static
    {
        $this->cities[$city->getId()] = $city;

        return $this;
    }

    public function getProvidersIds(): array
    {
        return array_values(array_map(fn(Provider $provider): string => $provider->getId(), $this->providers));
    }

    public function getCitiesIds(): array
    {
        return array_values(array_map(fn(City $city): string => $city->getId(), $this->cities));
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "cities" => $this->cities,
            "providers" => $this->providers,
        ];
    }
}
