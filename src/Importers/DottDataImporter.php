<?php

declare(strict_types=1);

namespace EScooters\Importers;

use EScooters\Utils\HardcodedCitiesToCountriesAssigner;
use GuzzleHttp\Client;

class DottDataImporter extends DataImporter
{
    protected array $markers = [];

    public function getBackground(): string
    {
        return "#F5C605";
    }

    public function extract(): static
    {
        $client = new Client();
        $response = $client->get("https://ridedott.com/iframe/map-iframe")->getBody()->getContents();
        $script = explode("const DATA = '", $response)[1];
        $script = explode("';</script><script>const", $script);

        $json = json_decode($script[0], true);
        $this->markers = $json["markers"];

        return $this;
    }

    public function transform(): static
    {
        foreach ($this->markers as $marker) {
            $url = $marker["url"];
            $parts = explode("/", $url);

            $cityName = ucfirst($parts[count($parts) - 1]);

            $country = null;
            $hardcoded = HardcodedCitiesToCountriesAssigner::assign($cityName);
            if($hardcoded) {
                $country = $this->countries->retrieve($hardcoded);
            }

            $city = $this->cities->retrieve($cityName, $country);
            $this->provider->addCity($city);
        }

        return $this;
    }
}
