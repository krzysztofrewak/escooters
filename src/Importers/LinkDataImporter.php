<?php

declare(strict_types=1);

namespace EScooters\Importers;

use DOMElement;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class LinkDataImporter extends DataImporter
{
    protected Crawler $sections;

    public function getBackground(): string
    {
        return "#DEF700";
    }

    public function extract(): static
    {
        $client = new Client();
        $html = $client->get("http://www.link.city/cities")->getBody()->getContents();

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter("#content .sqs-row.row > .col p > strong");

        return $this;
    }

    public function transform(): static
    {
        /** @var DOMElement $section */
        foreach ($this->sections as $section) {
            foreach ($section->childNodes as $node) {
                $countryName = trim($node->nodeValue);
                if (in_array($countryName, ["Tech-Enabled Compliance", "COVID-19 RAPID RESPONSE CASE STUDY"])) {
                    continue;
                }

                $country = $this->countries->retrieve($countryName);

                foreach ($node->parentNode->parentNode->parentNode->childNodes as $i => $cityName) {
                    if ($i === 0 || !trim($cityName->nodeValue)) {
                        continue;
                    }

                    $name = $cityName->nodeValue;
                    if ($country->getId() === "us" && str_contains($name, ", ")) {
                        $name = explode(",", $name)[0];
                    }

                    $cities = [];
                    if (str_contains($name, "(") && str_contains($name, ")")) {
                        $names = explode("(", $name)[1];
                        $names = explode(")", $names)[0];
                        $names = explode(", ", $names);
                        foreach ($names as $name) {
                            $cities[] = str_replace("*", "", $name);
                        }
                    } else {
                        $cities[] = $name;
                    }

                    foreach ($cities as $name) {
                        $city = $this->cities->retrieve($name, $country);
                        $this->provider->addCity($city);
                    }
                }
            }
        }

        return $this;
    }
}
