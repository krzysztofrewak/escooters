<?php

declare(strict_types=1);

namespace EScooters\Normalizers;

class CountryNamesNormalizer
{
    public static function normalize(string $name): string
    {
        if (static::isUSState($name)) {
            return "United States";
        }

        return match ($name) {
            "Czech Republic" => "Czechia",
            "UAE" => "United Arab Emirates",
            "Uk" => "United Kingdom",
            default => $name,
        };
    }

    protected static function isUSState(string $name): bool
    {
        return in_array($name, [
            "Alabama",
            "Alaska",
            "Arizona",
            "Arkansas",
            "California",
            "Colorado",
            "Connecticut",
            "Delaware",
            "District of Columbia",
            "Florida",
            "Georgia",
            "Hawaii",
            "Idaho",
            "Illinois",
            "Indiana",
            "Iowa",
            "Kansas",
            "Kentucky",
            "Louisiana",
            "Maine",
            "Maryland",
            "Massachusetts",
            "Michigan",
            "Minnesota",
            "Mississippi",
            "Missouri",
            "Montana",
            "Nebraska",
            "Nevada",
            "New Hampshire",
            "New Jersey",
            "New Mexico",
            "New York",
            "North Carolina",
            "North Dakota",
            "Ohio",
            "Oklahoma",
            "Oregon",
            "Pennsylvania",
            "Rhode Island",
            "South Carolina",
            "South Dakota",
            "Tennessee",
            "Texas",
            "Utah",
            "Vermont",
            "Virginia",
            "Washington",
            "West Virginia",
            "Wisconsin",
            "Wyoming",
        ]);
    }

}
