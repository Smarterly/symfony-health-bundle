<?php

declare(strict_types=1);

namespace Tests\Unit\Formatter\Http;

use App\Result\HealthStatus\HealthStatus;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Encoder\Json\SafeJson;
use Cushon\HealthBundle\Formatter\Http\SimpleJson;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Cushon\HealthBundle\Message\Result\HealthCheck\Unhealthy;
use Ergebnis\Json\Printer\Printer;
use PHPUnit\Framework\TestCase;

use function Safe\json_decode;

final class JsonTest extends TestCase
{
    public function testItReturnsAValidJsonResponse(): void
    {
        $depenencyName = 'some:dependency';
        $dependencyStatus = new SimpleStatus($depenencyName, false);
        $unhealthy = new Unhealthy($dependencyStatus);

        $jsonFormatter = new SimpleJson(new SafeJson(new Printer()));

        $response = $jsonFormatter->format($unhealthy);
        $body = json_decode($response->getContent());

        $this->assertSame(HealthCheck::STATUS_UNHEALTHY, $body->status);
        $this->assertNotEmpty($body->dependencies);
        $this->assertSame($depenencyName, $body->dependencies[0]->name);
        $this->assertFalse($body->dependencies[0]->healthy);
    }
}
