<?php

declare(strict_types=1);

namespace Tests\Unit\Message\Result\HealthCheck\Traits;

use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Cushon\HealthBundle\Message\Result\Traits\JsonSerializableTrait;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;

use function Safe\json_decode;
use function Safe\json_encode;

final class JsonSerializableTraitTest extends TestCase
{
    use ProphecyTrait;

    public function testItAddsJsonSerialize(): void
    {
        $dep1 = [
            'foo' => 'bar',
        ];

        $dep2 = [
            'baz' => 'boo',
        ];

        $status = 'all good';

        $dependency1 = $this->prophesize(DependencyStatus::class);
        $dependency1->jsonSerialize()->willReturn($dep1);
        $dependency2 = $this->prophesize(DependencyStatus::class);
        $dependency2->jsonSerialize()->willReturn($dep2);

        $class = new class (
            $status,
            $dependency1->reveal(),
            $dependency2->reveal()
        ) implements JsonSerializable {
            use JsonSerializableTrait;

            private string $status;

            /**
             * @var DependencyStatus[]
             */
            private array $dependencies;

            public function __construct(string $status, DependencyStatus ...$dependencies)
            {
                $this->status = $status;
                $this->dependencies = $dependencies;
            }

            /**
             * @return DependencyStatus[]
             */
            public function dependencies(): array
            {
                return $this->dependencies;
            }

            /**
             * @return string
             */
            public function getStatus(): string
            {
                return $this->status;
            }
        };

        $encoded = $this->encodeDecode($class);

        $this->assertObjectHasAttribute(HealthCheck::KEY_STATUS, $encoded);
        $this->assertSame($status, $encoded->{HealthCheck::KEY_STATUS});
        $this->assertObjectHasAttribute(HealthCheck::KEY_DEPENDENCIES, $encoded);
        $dep1Obj = $this->encodeDecode($dep1);
        $dep2Obj = $this->encodeDecode($dep2);
        $this->assertEquals([$dep1Obj, $dep2Obj], $encoded->{HealthCheck::KEY_DEPENDENCIES});
    }

    /**
     * @param mixed $data
     * @return stdClass
     * @throws \Safe\Exceptions\JsonException
     */
    private function encodeDecode(mixed $data): stdClass
    {
        return json_decode(json_encode($data));
    }
}
