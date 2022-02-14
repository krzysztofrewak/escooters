<?php

declare(strict_types=1);

namespace EScooters\Importers;

use DOMElement;
use EScooters\Exceptions\CityNotAssignedToAnyCountryException;
use EScooters\Utils\HardcodedCitiesToCountriesAssigner;
use Symfony\Component\DomCrawler\Crawler;

class BirdDataImporter extends DataImporter
{
    protected Crawler $sections;

    public function getBackground(): string
    {
        return "#26CCF0";
    }

    public function extract(): static
    {
        $html = file_get_contents("https://www.bird.co/map/");

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter("ul.region-list");

        return $this;
    }

    public function transform(): static
    {
        /** @var DOMElement $section */
        foreach ($this->sections as $section) {
            $country = null;

            foreach ($section->childNodes as $node) {
                if(is_null($country) || $country->getId() !== "us") {
                    $country = null;
                }

                $value = trim($node->nodeValue);
                if ($value) {

                    if ($node->getAttribute("class") === "region-title") {
                        if ($value === "United States") {
                            $country = $this->countries->retrieve("United States");
                        } else {
                            continue;
                        }
                    }

                    if (str_contains($value, "University")) {
                        break;
                    }

                    try {
                        $hardcoded = HardcodedCitiesToCountriesAssigner::assign($value);
                        if ($hardcoded) {
                            $country = $this->countries->retrieve($hardcoded);
                        }

                        $city = $this->cities->retrieve($value, $country);
                        $this->provider->addCity($city);
                    } catch (CityNotAssignedToAnyCountryException $exception) {
                        echo $exception->getMessage() . PHP_EOL;
                        continue;
                    }
                }
            }
        }

        return $this;
    }
}
