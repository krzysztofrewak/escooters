<?php

declare(strict_types=1);

namespace EScooters\Models\Repositories;

use EScooters\Models\Country;
use EScooters\Normalizers\CountryNamesNormalizer;
use EScooters\Utils\CountryCode;
use JsonSerializable;

class Countries implements JsonSerializable
{
    /** @var array<Country> */
    protected array $countries = [];

    public function retrieve(string $name): Country
    {
        $name = CountryNamesNormalizer::normalize($name);
        $code = CountryCode::NAMES_TO_CODES[$name];

        if (array_key_exists($code, $this->countries)) {
            return $this->countries[$code];
        }

        $country = new Country($code, $name);
        $this->countries[$code] = $country;

        return $country;
    }

    public function jsonSerialize(): array
    {
        uasort($this->countries, fn(Country $a, Country $b) => $a->getName() <=> $b->getName());
        return array_map(fn(Country $country): array => [
            "code" => $country->getId(),
            "name" => $country->getName(),
            "providers" => $country->getProvidersIds(),
            "cities" => $country->getCitiesIds(),
        ], $this->countries);
    }
}
