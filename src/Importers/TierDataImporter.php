<?php

declare(strict_types=1);

namespace EScooters\Importers;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

class TierDataImporter extends DataImporter
{
    protected Crawler $sections;

    public function getBackground(): string
    {
        return "#0E1A50";
    }

    public function extract(): static
    {
        $html = file_get_contents("https://www.tier.app/en/where-to-find-us/");

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter("section.cTWYMJ:not(.w-full)");

        return $this;
    }

    public function transform(): static
    {
        /** @var DOMElement $section */
        foreach ($this->sections as $section) {
            foreach ($section->childNodes as $node) {
                $country = null;

                $crawler = new Crawler($node->ownerDocument->saveHTML($node));
                $countryHeader = $crawler->filter("h5");
                $country = $this->countries->retrieve($countryHeader->innerText());

                $cityHeaders = $crawler->filter("div[role=listbox] p");
                foreach ($cityHeaders as $cityHeader) {
                    $city = $this->cities->retrieve(trim($cityHeader->nodeValue), $country);
                    $this->provider->addCity($city);
                }
            }
        }

        return $this;
    }
}
