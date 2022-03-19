<?php

declare(strict_types=1);

namespace EScooters\Services;

use EScooters\Models\Provider;
use EScooters\Models\Repositories\Cities;

class QuickChartIconsService
{
    protected array $combinations = [];

    public function generateCityIcons(Cities $cities): static
    {
        foreach ($cities->all() as $city) {
            $label = $city->getProvidersIconId();
            $this->combinations[$label] = $city->getProviders();
        }

        foreach ($this->combinations as $index => $combination) {
            if (file_exists("./public/cities/${index}.png")) {
                continue;
            }

            $json = json_encode([
                "type" => "doughnut",
                "data" => [
                    "datasets" => [
                        [
                            "data" => array_map(fn(Provider $provider): int => 1, array_values($combination)),
                            "backgroundColor" => array_map(fn(Provider $provider): string => str_replace("#", "%23", $provider->getBackground()), array_values($combination)),
                        ],
                    ],
                ],
                "options" => [
                    "plugins" => [
                        "datalabels" => [
                            "display" => false,
                        ],
                    ],
                ],
            ]);

            $url = "https://quickchart.io/chart?w=48&h=48&c=${json}";
            file_put_contents("./public/cities/${index}.png", file_get_contents($url));
        }

        return $this;
    }
}
