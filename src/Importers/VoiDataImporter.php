<?php

declare(strict_types=1);

namespace EScooters\Importers;

use DOMElement;
use EScooters\Importers\DataSources\HtmlDataSource;
use Symfony\Component\DomCrawler\Crawler;

class VoiDataImporter extends DataImporter implements HtmlDataSource
{
    protected Crawler $sections;

    public function getBackground(): string
    {
        return "#F46C63";
    }

    public function extract(): static
    {
        $html = file_get_contents("https://www.voiscooters.com/locations/");
        $crawler = new Crawler($html);
        $this->sections = $crawler->filter("section.locations-list .holder > div > .s-col-6.col-4.mb-4");

        return $this;
    }

    public function transform(): static
    {
        /** @var DOMElement $section */
        foreach ($this->sections as $section) {
            $country = null;

            foreach ($section->childNodes as $node) {
                if ($node->nodeName === "h4") {
                    $countryName = trim($node->nodeValue ?? "");
                    $country = $this->countries->retrieve($countryName);
                }

                if ($node->nodeName === "ul") {
                    foreach ($node->childNodes as $city) {
                        if ($city->nodeName === "li") {
                            $city = $this->cities->retrieve(trim($city->nodeValue), $country);
                            $this->provider->addCity($city);
                        }
                    }
                }
            }
        }

        return $this;
    }
}
