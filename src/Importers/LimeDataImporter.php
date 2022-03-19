<?php

declare(strict_types=1);

namespace EScooters\Importers;

use DOMElement;
use EScooters\Importers\DataSources\HtmlDataSource;
use Symfony\Component\DomCrawler\Crawler;

class LimeDataImporter extends DataImporter implements HtmlDataSource
{
    protected Crawler $sections;

    public function getBackground(): string
    {
        return "#00DE00";
    }

    public function extract(): static
    {
        $html = file_get_contents("https://www.li.me/locations");

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter("li.mb-5");

        return $this;
    }

    public function transform(): static
    {
        /** @var DOMElement $section */
        foreach ($this->sections as $section) {
            $country = null;

            foreach ($section->childNodes as $node) {
                if ($node->nodeName === "strong") {
                    $countryName = trim($node->nodeValue ?? "");
                    $country = $this->countries->retrieve($countryName);
                }

                if ($node->nodeName === "ul") {
                    foreach ($node->childNodes as $city) {
                        if ($city->nodeName === "li") {
                            if (str_contains($city->nodeValue, "University")) {
                                continue;
                            }

                            $city = $this->cities->retrieve($city->nodeValue, $country);
                            $this->provider->addCity($city);
                        }
                    }
                }
            }
        }

        return $this;
    }
}
