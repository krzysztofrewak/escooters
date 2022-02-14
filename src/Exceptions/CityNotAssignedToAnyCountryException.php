<?php

declare(strict_types=1);

namespace EScooters\Exceptions;

use Exception;
use Throwable;

class CityNotAssignedToAnyCountryException extends Exception
{
    public function __construct(string $city, Throwable $previous = null)
    {
        parent::__construct(previous: $previous);
        $this->message = "City {$city} was not assigned to any country.";
    }
}
