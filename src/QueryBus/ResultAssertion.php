<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\QueryBus;

use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\AssertionError;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface ResultAssertion
{
    /**
     * @param mixed $result
     * @return void
     * @throws AssertionError
     */
    public function assertValidResult(mixed $result): void;
}
