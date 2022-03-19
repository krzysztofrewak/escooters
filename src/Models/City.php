<?php

declare(strict_types=1);

namespace EScooters\Models;

use JsonSerializable;

class City implements JsonSerializable
{
    /** @var array<string, Provider> */
    protected array $providers = [];
    protected array $coordinates = [
        "lat" => null,
        "lng" => null,
    ];

    public function __construct(
        protected string $id,
        protected string $name,
        protected Country $country,
    ) {}

    public function getId()
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function getProvidersIds(): array
    {
        $ids = array_values(array_map(fn(Provider $provider): string => $provider->getId(), $this->providers));
        sort($ids);
        return $ids;
    }

    public function getProvidersIconId(): string
    {
        $providers = $this->getProvidersIds();
        sort($providers);

        return implode("-", $providers);
    }

    public function addProvider(Provider $provider): static
    {
        $this->providers[$provider->getId()] = $provider;

        return $this;
    }

    public function setCoordinates(array $coordinates): void
    {
        $this->coordinates["lat"] = $coordinates[1];
        $this->coordinates["lng"] = $coordinates[0];
    }

    public function getCoordinates(): array
    {
        return $this->coordinates;
    }

    public function jsonSerialize(): array
    {
        return [
            "name" => $this->name,
            "country" => $this->country,
        ];
    }
}
