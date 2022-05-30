<?php

declare(strict_types=1);

namespace App\Repository\Exception;

use App\ApplicationHealth\DependencyCheck\RandomOrgApiCheck\Exception\HealthDependencyException;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

final class SymfonyHttpException extends RuntimeException implements HealthDependencyException
{
    /**
     * @param ExceptionInterface $e
     * @return static
     */
    public static function fromPrevious(ExceptionInterface $e): self
    {
        return new self(
            sprintf('An error occurred while contacting random.org: %s', $e->getMessage()),
            $e->getCode(),
            $e
        );
    }
}
