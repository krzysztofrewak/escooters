<?php

declare(strict_types=1);

namespace EScooters\Services;

use EScooters\Models\City;
use EScooters\Models\Repositories\Cities;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MapboxGeocodingService
{
    protected const CACHE_FILENAME = "./.mapbox_cities_cache";

    protected array $cache = [];

    public function __construct(
        protected string $token,
    ) {
        if (file_exists(static::CACHE_FILENAME)) {
            echo "Cached cities loaded." . PHP_EOL;
            $this->cache = json_decode(file_get_contents(static::CACHE_FILENAME), true);
        }
    }

    public function setCoordinatesToCities(Cities $cities): static
    {
        $client = new Client();

        foreach ($cities->all() as $city) {
            if (array_key_exists($city->getId(), $this->cache)) {
                $city->setCoordinates($this->cache[$city->getId()]);
            } else {
                $this->getFromAPI($client, $city);
            }
        }

        return $this;
    }

    protected function getFromAPI(Client $client, City $city): void
    {
        $name = $city->getName() . ", " . $city->getCountry()->getName();
        $token = $this->token;

        try {
            $response = $client->get(
                "https://api.mapbox.com/geocoding/v5/mapbox.places/${name}.json?access_token=${token}&types=place",
            );
            $coordinates = json_decode($response->getBody()->getContents(), true)["features"][0]["center"];
            $this->cache[$city->getId()] = $coordinates;
            $city->setCoordinates($coordinates);
        } catch (GuzzleException) {
            echo "Coordinates for $name were not fetched.";
        }

        file_put_contents(static::CACHE_FILENAME, json_encode($this->cache, JSON_UNESCAPED_UNICODE));
    }
}
