<?php

declare(strict_types=1);

namespace EScooters\Importers;

use DOMElement;
use EScooters\Importers\DataSources\HtmlDataSource;
use Symfony\Component\DomCrawler\Crawler;

class NeuronDataImporter extends DataImporter implements HtmlDataSource
{
    /** @var array<string, Crawler> */
    protected array $sections;

    public function getBackground(): string
    {
        return "#445261";
    }

    public function extract(): static
    {
        $html = file_get_contents("https://www.rideneuron.com/customer-support-contact/");

        $crawler = new Crawler($html);
        $countries = $crawler->filter("h2.elementor-heading-title > a");

        /** @var DOMElement $section */
        foreach ($countries as $country) {
            $url = $country->getAttribute("href");
            $html = file_get_contents($url);
            $crawler = new Crawler($html);
            $this->sections[trim($country->nodeValue)] = $crawler->filter(".elementor-element .elementor-clearfix strong");
        }

        return $this;
    }

    public function transform(): static
    {
        foreach ($this->sections as $countryName => $section) {
            $country = $this->countries->retrieve($countryName);

            /** @var DOMElement $element */
            foreach ($section as $element) {
                $cityName = $element->nodeValue;
                if ($cityName === "Frankston") {
                    continue;
                }

                $city = $this->cities->retrieve($cityName, $country);
                $this->provider->addCity($city);
            }
        }

        return $this;
    }
}
