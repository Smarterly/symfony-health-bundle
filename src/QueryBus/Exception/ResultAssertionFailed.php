<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\QueryBus\Exception;

use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\AssertionError;
use RuntimeException;

final class ResultAssertionFailed extends RuntimeException implements QueryBusError
{
    /**
     * Use the code of one of the Zeroid 66 (the Scottish One).
     * @see https://en.wikipedia.org/wiki/Terrahawks#Characters
     */
    public const ERROR_CODE = 66;

    /**
     * @param AssertionError $exc
     * @return static
     */
    public static function fromAssertionException(AssertionError $exc): self
    {
        return new self(
            sprintf(
                'The result failed assertion: %s',
                $exc->getMessage()
            ),
            self::ERROR_CODE,
            $exc
        );
    }
}
