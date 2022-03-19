<?php

declare(strict_types=1);

namespace EScooters\Importers;

use EScooters\Importers\DataSources\HardcodedDataSource;

class WhooshDataImporter extends DataImporter implements HardcodedDataSource
{
    public function getBackground(): string
    {
        return "#FFCA47";
    }

    public function extract(): static
    {
        // static analysis of file
        // https://static.tildacdn.com/tild3965-3939-4333-b834-646637663932/map_2.png
        return $this;
    }

    public function transform(): static
    {
        $country = $this->countries->retrieve("Portugal");
        $city = $this->cities->retrieve("Lisbon", $country);
        $this->provider->addCity($city);

        $country = $this->countries->retrieve("Russia");
        foreach (static::getRussianCities() as $cityName) {
            $city = $this->cities->retrieve($cityName, $country);
            $this->provider->addCity($city);
        }

        return $this;
    }

    protected static function getRussianCities(): array
    {
        return [
            "Moscow",
            "Saint Petersburg",
            "Kaluga",
            "Tula",
            "Voronezh",
            "Nizhny Novogorod",
            "Rostov-on-Don",
            "Krasnodar",
            "Sochi",
            "Kazan",
            "Samara",
            "Ufa",
            "Czelyabinsk",
            "Yekaterinburg",
            "Tyumen",
            "Novosibirsk",
            "Tomsk",
        ];
    }
}
