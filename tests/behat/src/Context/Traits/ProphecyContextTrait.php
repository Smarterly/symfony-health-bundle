<?php

declare(strict_types=1);

namespace Tests\Behat\Context\Traits;

use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;

trait ProphecyContextTrait
{
    private static ?Prophet $prophet = null;

    /**
     * @BeforeSuite
     */
    public static function initializeProphet(): Prophet
    {
        if (!self::$prophet) {
            self::$prophet = new Prophet();
        }

        return self::$prophet;
    }

    /**
     * @param string $classOrInterface
     * @phpstan-ignore-next-line Ignore as ObjectProphecy requires a template
     * @return ObjectProphecy
     */
    private function prophesize(string $classOrInterface): ObjectProphecy
    {
        return self::initializeProphet()->prophesize($classOrInterface);
    }
}
