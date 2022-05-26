<?php

declare(strict_types=1);

namespace Tests\Utils;

final class Constants
{
    /**
     * @return string
     */
    public static function rootDir(): string
    {
        return dirname(__DIR__, 3);
    }

    /**
     * @return string
     */
    public static function testsDir(): string
    {
        return self::rootDir() . '/tests';
    }

    /**
     * @return string
     */
    public static function buildDir(): string
    {
        return self::rootDir() . '/build';
    }

    /**
     * @return string
     */
    public static function fixturesDir(): string
    {
        return self::testsDir() . '/fixtures';
    }

    /**
     * @return string
     */
    public static function vendorDir(): string
    {
        return self::rootDir() . '/vendor';
    }

    /**
     * @return string
     */
    public static function appDir(): string
    {
        return self::rootDir() . '/app';
    }
}
