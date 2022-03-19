<?php

require "./vendor/autoload.php";

use Dotenv\Dotenv;
use EScooters\Importers\BirdDataImporter;
use EScooters\Importers\BoltDataImporter;
use EScooters\Importers\DataImporter;
use EScooters\Importers\DottDataImporter;
use EScooters\Importers\HelbizDataImporter;
use EScooters\Importers\LimeDataImporter;
use EScooters\Importers\LinkDataImporter;
use EScooters\Importers\NeuronDataImporter;
use EScooters\Importers\QuickDataImporter;
use EScooters\Importers\SpinDataImporter;
use EScooters\Importers\TierDataImporter;
use EScooters\Importers\VoiDataImporter;
use EScooters\Importers\WhooshDataImporter;
use EScooters\Models\Repositories\Cities;
use EScooters\Models\Repositories\Countries;
use EScooters\Models\Repositories\Providers;
use EScooters\Services\MapboxGeocodingService;
use EScooters\Services\QuickChartIconsService;

Dotenv::createImmutable(__DIR__)->load();
$token = $_ENV["VUE_APP_MAPBOX_TOKEN"];

$cities = new Cities();
$countries = new Countries();
$providers = new Providers();

/** @var array<DataImporter> $dataImporters */
$dataImporters = [
    new BoltDataImporter($cities, $countries),
    new LimeDataImporter($cities, $countries),
    new QuickDataImporter($cities, $countries),
    new TierDataImporter($cities, $countries),
    new VoiDataImporter($cities, $countries),
    new LinkDataImporter($cities, $countries),
    new SpinDataImporter($cities, $countries),
    new NeuronDataImporter($cities, $countries),
    new HelbizDataImporter($cities, $countries),
    new WhooshDataImporter($cities, $countries),
    new BirdDataImporter($cities, $countries),
    new DottDataImporter($cities, $countries),
];

echo "Build date: " . date("Y-m-d H:i:s") . PHP_EOL . PHP_EOL;

foreach ($dataImporters as $dataImporter) {
    try {
        $provider = $dataImporter->extract()->transform()->load();
    } catch (Throwable $exception) {
        echo $exception->getMessage() . PHP_EOL;
        continue;
    }

    $providers->add($provider);

    echo "{$provider->getCities()->count()} cities fetched for {$provider->getName()}." . PHP_EOL;
}

$count = count($cities->all());
echo PHP_EOL . "$count cities fetched." . PHP_EOL;

$mapbox = new MapboxGeocodingService($token);
$mapbox->setCoordinatesToCities($cities);

$mapbox = new QuickChartIconsService();
$mapbox->generateCityIcons($cities);

file_put_contents("./public/data/cities.json", json_encode($cities, JSON_UNESCAPED_UNICODE));
file_put_contents("./public/data/countries.json", json_encode($countries, JSON_UNESCAPED_UNICODE));
file_put_contents("./public/data/providers.json", json_encode($providers, JSON_UNESCAPED_UNICODE));
