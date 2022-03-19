<?php

declare(strict_types=1);

namespace EScooters\Models\Repositories;

use EScooters\Exceptions\CityNotAssignedToAnyCountryException;
use EScooters\Models\City;
use EScooters\Models\Country;
use EScooters\Normalizers\CityNamesNormalizer;
use Illuminate\Support\Str;
use JsonSerializable;

class Cities implements JsonSerializable
{
    /** @var array<City> */
    protected array $cities = [];

    /**
     * @throws CityNotAssignedToAnyCountryException
     */
    public function retrieve(string $name, ?Country $country = null): City
    {
        $name = CityNamesNormalizer::normalize($name);
        $slug = Str::slug($name);

        if (array_key_exists($slug, $this->cities)) {
            return $this->cities[$slug];
        }

        if ($country === null) {
            $this->assignCountry($slug);
        }

        $city = new City($slug, $name, $country);
        $this->cities[$slug] = $city;

        return $city;
    }

    public function all(): array
    {
        return $this->cities;
    }

    public function jsonSerialize(): array
    {
        return array_map(fn(City $city): array => [
            "name" => $city->getName(),
            "country" => $city->getCountry()->getId(),
            "providers" => $city->getProvidersIds(),
            "coordinates" => $city->getCoordinates(),
            "icon" => $city->getProvidersIconId(),
        ], $this->all());
    }

    /**
     * @throws CityNotAssignedToAnyCountryException
     */
    protected function assignCountry(string $slug): Country
    {
        if (!array_key_exists($slug, $this->cities)) {
            throw new CityNotAssignedToAnyCountryException($slug);
        }

        return $this->cities[$slug]->getCountry();
    }
}
