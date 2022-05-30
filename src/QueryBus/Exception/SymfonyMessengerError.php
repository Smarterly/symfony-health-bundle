<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\QueryBus\Exception;

use RuntimeException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class SymfonyMessengerError extends RuntimeException implements QueryBusError
{
    /**
     * Use the code of one of the Zeroid Dix-Huit.
     * @see https://en.wikipedia.org/wiki/Terrahawks#Characters
     */
    public const ERROR_CODE = 18;

    /**
     * @param ExceptionInterface $exc
     * @return self
     */
    public static function fromMessengerException(ExceptionInterface $exc): self
    {
        return new self(
            sprintf(
                'Symfony MessageBus threw an exception during handling application health check: %s',
                $exc->getMessage(),
            ),
            self::ERROR_CODE,
            $exc
        );
    }
}
