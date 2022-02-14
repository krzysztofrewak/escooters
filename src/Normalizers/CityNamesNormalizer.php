<?php

declare(strict_types=1);

namespace EScooters\Normalizers;

class CityNamesNormalizer
{
    public static function normalize(string $name): string
    {
        $name = match ($name) {
            "Malta" => "La Valetta",
            "Ruhrpott" => "Düsseldorf",
            "Brunswick" => "Braunschweig",
            "Marijampolė" => "Marijampolis",
            "North Devon" => "Barnstaple",
            "Silesia" => "Katowice",
            "Trójmiasto" => "Gdańsk",
            "Tricity" => "Gdańsk",
            "Frankfurt" => "Frankfurt am Main",
            "Ruwais" => "Ar-Ruwais",
            "Velden" => "Velden am Wörther See",
            "Roma" => "Rome",
            "Firenze" => "Florence",
            "Torino" => "Turin",
            "Warszawa" => "Warsaw",
            "Newcastle" => "Newcastle upon Tyne",
            default => $name,
        };

        return str_replace("/", " ", $name);
    }
}
