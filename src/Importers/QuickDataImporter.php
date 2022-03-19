<?php

declare(strict_types=1);

namespace EScooters\Importers;

use DOMElement;
use EScooters\Importers\DataSources\HtmlDataSource;
use Symfony\Component\DomCrawler\Crawler;

class QuickDataImporter extends DataImporter implements HtmlDataSource
{
    protected const FIXED_COUNTRY = "Poland";

    protected Crawler $sections;

    public function getBackground(): string
    {
        return "#009AC7";
    }

    public function extract(): static
    {
        $html = file_get_contents("https://quick-app.eu/lokalizacje/");

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter(".tx-hd-desc > ul > li");

        return $this;
    }

    public function transform(): static
    {
        $country = $this->countries->retrieve(static::FIXED_COUNTRY);

        /** @var DOMElement $section */
        foreach ($this->sections as $section) {
            $city = $this->cities->retrieve($section->nodeValue, $country);
            $this->provider->addCity($city);
        }

        return $this;
    }
}
