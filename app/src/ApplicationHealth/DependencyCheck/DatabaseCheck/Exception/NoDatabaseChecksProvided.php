<?php
declare(strict_types=1);

namespace App\ApplicationHealth\DependencyCheck\DatabaseCheck\Exception;

use InvalidArgumentException;

final class NoDatabaseChecksProvided extends InvalidArgumentException
{
    /**
     * @return static
     */
    public static function create(): self
    {
        return new self('No database checks were provided');
    }
}
