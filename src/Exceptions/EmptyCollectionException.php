<?php

declare(strict_types=1);

namespace EScooters\Exceptions;

use EScooters\Models\Provider;
use Exception;
use Throwable;

class EmptyCollectionException extends Exception
{
    public function __construct(Provider $provider, ?Throwable $previous = null)
    {
        parent::__construct(previous: $previous);
        $this->message = "{$provider->getName()} import failed.";
    }
}
