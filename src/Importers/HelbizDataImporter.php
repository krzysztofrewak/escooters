<?php

declare(strict_types=1);

namespace EScooters\Importers;

use EScooters\Importers\DataSources\HardcodedDataSource;

class HelbizDataImporter extends DataImporter implements HardcodedDataSource
{
    public function getBackground(): string
    {
        return "#000000";
    }

    public function extract(): static
    {
        // static analysis of file
        // https://helbiz.com/cities
        return $this;
    }

    public function transform(): static
    {
        $countries = [
            "United States" => "getUSCities",
            "Italy" => "getItalianCities",
            "France" => "getFrenchCities",
            "Serbia" => "getSerbianCities",
            "Singapore" => "getSingaporeanCities",
        ];

        foreach ($countries as $countryName => $method) {
            $country = $this->countries->retrieve($countryName);
            foreach (static::$method() as $cityName) {
                $city = $this->cities->retrieve($cityName, $country);
                $this->provider->addCity($city);
            }
        }

        return $this;
    }

    protected static function getUSCities(): array
    {
        return [
            "Arlington, VA",
            "Alexandria, VA",
            "Washington, DC",
            "Richmond, VA",
            "Miami, FL",
            "Jacksonville, FL",
            "Oklahoma City, OK",
            "Santa Barbara, CA",
            "Waterloo, IA",
            "Sacramento, CA",
            "Flint, MI",
            "Durham, NC",
            "Miami Lakes, FL",
        ];
    }

    protected static function getItalianCities(): array
    {
        return [
            "Milan",
            "Turin",
            "Cesena",
            "Bari",
            "Rome",
            "Ravenna",
            "Catania",
            "Parma",
            "Latina",
            "Modena",
            "Palermo",
            "Pescara",
            "Pisa",
            "Napoli",
            "Florence",
            "Genoa",
            "Ferrara",
            "Fiumicino",
            "Frosinone",
            "Reggio Emilia",
            "Collegno",
            "San Giovanni Teatino",
            "Montesilvano",
        ];
    }

    protected static function getFrenchCities(): array
    {
        return [
            "Bordeaux",
            "Paris",
        ];
    }

    protected static function getSerbianCities(): array
    {
        return [
            "Belgrade",
        ];
    }

    protected static function getSingaporeanCities(): array
    {
        return [
            "Singapore",
        ];
    }
}
