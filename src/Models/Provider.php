<?php

declare(strict_types=1);

namespace EScooters\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonSerializable;

class Provider implements JsonSerializable
{
    protected Collection $cities;
    protected Collection $countries;
    protected string $id;

    public function __construct(
        protected readonly string $name,
        protected readonly string $background,
    ) {
        $this->id = Str::slug($name);
        $this->cities = new Collection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return Collection<City> */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function getBackground(): string
    {
        return $this->background;
    }

    public function addCity(City $city): static
    {
        $city->addProvider($this);
        $city->getCountry()->addProvider($this)->addCity($city);

        $this->cities->add($city);
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "background" => $this->background,
            "cities" => $this->cities->count(),
            "countries" => $this->cities->map(fn(City $city): string => $city->getCountry()->getId())->unique()->values(),
        ];
    }
}
