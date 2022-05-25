<?php

declare(strict_types=1);

namespace Tests\Unit\Encoder\Json;

use Cushon\HealthBundle\Encoder\Json\SafeJson;
use Ergebnis\Json\Printer\Printer;
use PHPUnit\Framework\TestCase;
use Tests\Utils\Constants;

use function Safe\file_get_contents;
use function Safe\json_decode;

final class SafeJsonTest extends TestCase
{
    private const ENCODING_FIXTURES_DIR = '/encoding';

    private static function getJsonFixture(): string
    {
        $path = Constants::fixturesDir() . self::ENCODING_FIXTURES_DIR . '/object.json';
        return trim(file_get_contents($path));
    }

    public function testItEncodesAnArray(): void
    {
        $encoded = self::getJsonFixture();
        $data = json_decode($encoded);

        $encoder = new SafeJson(new Printer());

        $this->assertSame($encoded, $encoder->encode($data));
    }
}
