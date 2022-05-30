<?php
declare(strict_types=1);

namespace App\Repository\Exception;

use App\ApplicationHealth\DependencyCheck\RandomOrgApiCheck\Exception\HealthDependencyException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class ResponseNotOk extends RuntimeException implements HealthDependencyException
{
    /**
     * @param int $statusCode
     * @return static
     */
    public static function fromResponseCode(int $statusCode): self
    {
        return new self(sprintf(
            'The status code was expected to be %d, received %d',
            Response::HTTP_OK,
            $statusCode
        ));
    }
}
