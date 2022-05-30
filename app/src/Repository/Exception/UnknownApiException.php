<?php

declare(strict_types=1);

namespace App\Repository\Exception;

use App\ApplicationHealth\DependencyCheck\RandomOrgApiCheck\Exception\HealthDependencyException;
use RuntimeException;
use Throwable;

final class UnknownApiException extends RuntimeException implements HealthDependencyException
{
    /**
     * @param Throwable $e
     * @return static
     */
    public static function create(Throwable $e): self
    {
        return new self(
            sprintf('An unknown error occurred contacting random.org: %s', $e->getMessage())
        );
    }
}
