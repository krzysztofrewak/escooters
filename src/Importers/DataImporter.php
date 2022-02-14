<?php

declare(strict_types=1);

namespace EScooters\Importers;

use EScooters\Exceptions\EmptyCollectionException;
use EScooters\Models\Provider;
use EScooters\Models\Repositories\Cities;
use EScooters\Models\Repositories\Countries;

abstract class DataImporter
{
    protected Provider $provider;

    public function __construct(
        protected Cities $cities,
        protected Countries $countries,
    ) {
        $this->provider = new Provider($this->getProviderName(), $this->getBackground());
    }

    abstract public function getBackground(): string;

    abstract public function extract(): static;

    abstract public function transform(): static;

    /**
     * @throws EmptyCollectionException
     */
    public function load(): Provider
    {
        if (count($this->cities->all()) === 0) {
            throw new EmptyCollectionException($this->getProvider());
        }

        foreach ($this->cities as $city) {
            $this->provider->getCities()->add($city);
        }

        return $this->getProvider();
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    protected function getProviderName(): string
    {
        $parted = explode("\\", static::class);
        return str_replace("DataImporter", "", $parted[count($parted) - 1]);
    }
}
