<?php

declare(strict_types=1);

namespace EScooters\Importers;

use EScooters\Importers\DataSources\JsonDataSource;
use EScooters\Normalizers\CountryNamesNormalizer;

class TierDataImporter extends DataImporter implements JsonDataSource
{
    protected array $entries;

    public function getBackground(): string
    {
        return "#0E1A50";
    }

    public function extract(): static
    {
        $json = file_get_contents("https://www.tier.app/page-data/sq/d/134304685.json");
        $this->entries = json_decode($json, associative: true)["data"]["craft"]["entries"];

        return $this;
    }

    public function transform(): static
    {
        $previousCountryName = null;

        foreach ($this->entries as $entry) {
            if ($previousCountryName === $entry["title"] || !isset($entry["headline"])) {
                continue;
            }

            if ($entry["title"] === "Sverige Göteborg") {
                continue;
            }

            $previousCountryName = $entry["title"];

            $countryName = CountryNamesNormalizer::normalize($entry["title"]);
            $country = $this->countries->retrieve($countryName);

            $cities = explode(",", $entry["headline"]);
            foreach ($cities as $cityName) {
                $cityName = trim($cityName);

                if ($cityName === "Kundtjänst" || $cityName === "Dornbirndie") {
                    continue;
                }

                $city = $this->cities->retrieve($cityName, $country);
                $this->provider->addCity($city);
            }
        }

        return $this;
    }
}
