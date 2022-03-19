<?php

declare(strict_types=1);

namespace EScooters\Importers;

use EScooters\Importers\DataSources\JsonDataSource;
use GuzzleHttp\Client;

class BoltDataImporter extends DataImporter implements JsonDataSource
{
    protected array $fetchedCities = [];
    protected array $fetchedCityDictionary = [];
    protected array $fetchedCountriesDictionary = [];

    public function getBackground(): string {
        return "#24f0a0";
    }

    public function extract(): static
    {
        $client = new Client();
        $response = $client->get("https://bolt.eu/page-data/en/scooters/page-data.json");
        $content = json_decode($response->getBody()->getContents(), true);

        $this->fetchedCountriesDictionary = json_decode($content["result"]["data"]["countries"]["edges"][0]["node"]["data"], true)["countries"];
        $this->fetchedCityDictionary = $content["result"]["data"]["cities"]["nodes"];
        $this->fetchedCities = $content["result"]["data"]["scooterCities"]["nodes"];

        return $this;
    }

    public function transform(): static
    {
        $fetchedCityDictionary = [];
        foreach ($this->fetchedCityDictionary as $city) {
            $fetchedCityDictionary[$city["slug"]] = $city;
        }

        foreach ($this->fetchedCities as $city) {
            if ($city["city"]) {
                $fetched = $fetchedCityDictionary[$city["city"]] ?? null;
                if ($fetched === null) {
                    continue;
                }

                $countryName = $this->fetchedCountriesDictionary[$fetched["country"]["countryCode"]]["name"] ?? null;

                $country = $this->countries->retrieve($countryName);
                $city = $this->cities->retrieve($fetched["name"] ?? $city["city"], $country);

                $this->provider->addCity($city);
            }
        }

        unset($fetchedCities);
        unset($fetchedCityDictionary);
        unset($fetchedCountriesDictionary);

        return $this;
    }
}
