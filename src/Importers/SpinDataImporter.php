<?php

declare(strict_types=1);

namespace EScooters\Importers;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

class SpinDataImporter extends DataImporter
{
    protected Crawler $sections;

    public function getBackground(): string
    {
        return "#FF5436";
    }

    public function extract(): static
    {
        $html = file_get_contents("https://www.spin.app/");

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter(".locations-container .w-dyn-item");

        return $this;
    }

    public function transform(): static
    {
        /** @var DOMElement $section */
        foreach ($this->sections as $section) {
            $cityName = $section->nodeValue;
            if (str_contains($cityName, "University")) {
                continue;
            }

            $nodeClasses = $section->parentNode->parentNode->parentNode->getAttribute("class");
            $countryName = match (true) {
                str_contains($nodeClasses, "locations-list-") => ucfirst(str_replace(
                    "locations-list-",
                    "",
                    implode("", array_filter(explode(" ", $nodeClasses), fn(string $c): bool => $c !== "locations-list-wrapper"))
                )),
                default => "United States",
            };

            if($countryName === "United States") {
                $cityName = explode(", ", $cityName)[0];
            }

            $country = $this->countries->retrieve($countryName);
            $city = $this->cities->retrieve($cityName, $country);
            $this->provider->addCity($city);
        }

        return $this;
    }
}
