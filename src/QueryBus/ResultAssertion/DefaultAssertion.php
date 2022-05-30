<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\QueryBus\ResultAssertion;

use Cushon\HealthBundle\Message\Result\HealthCheck;
use Cushon\HealthBundle\QueryBus\ResultAssertion;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\ResultClassOrInterfaceNotFound;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\ResultTypeClassDoesNotMatchExpected;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\UnexpectedResultType;
use ReflectionClass;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class DefaultAssertion implements ResultAssertion
{
    private string $classOrInterfaceName;

    /**
     * @param string $classOrInterfaceName
     */
    public function __construct(string $classOrInterfaceName = HealthCheck::class)
    {
        $this->assertValidClassOrInterface($classOrInterfaceName);
        $this->classOrInterfaceName = $classOrInterfaceName;
    }

    /**
     * @inheritDoc
     */
    public function assertValidResult(mixed $result): void
    {
        if (!$result instanceof $this->classOrInterfaceName) {
            throw UnexpectedResultType::fromResult($result, $this->classOrInterfaceName);
        }
    }

    /**
     * @param string $classOrInterfaceName
     * @return void
     */
    private function assertValidClassOrInterface(string $classOrInterfaceName): void
    {
        if (!(class_exists($classOrInterfaceName) || interface_exists($classOrInterfaceName))) {
            throw ResultClassOrInterfaceNotFound::fromString($classOrInterfaceName);
        }

        $reflector = new ReflectionClass($classOrInterfaceName);

        if (!$reflector->implementsInterface(HealthCheck::class)) {
            throw ResultTypeClassDoesNotMatchExpected::create($classOrInterfaceName);
        }
    }
}
