<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\QueryBus\ResultAssertion\Exception;

use Cushon\HealthBundle\QueryBus\Exception\QueryBusError;
use RuntimeException;

final class UnexpectedResultType extends RuntimeException implements AssertionError
{
    private mixed $result;

    private string $expected;

    /**
     * @param mixed $result
     * @param string $expected
     * @return static
     */
    public static function fromResult(mixed $result, string $expected): self
    {
        $msg = sprintf(
            'Expected result of type %s, instead received an instance of %s',
            $expected,
            (is_object($result)) ? get_class($result) : gettype($result)
        );

        return new self($msg, $expected, $result);
    }

    /**
     * @param string $message
     * @param string $expected
     * @param mixed $result
     */
    private function __construct(string $message, string $expected, mixed $result)
    {
        $this->expected = $expected;
        $this->result = $result;

        parent::__construct($message);
    }

    /**
     * @return mixed
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getExpected(): string
    {
        return $this->expected;
    }
}
