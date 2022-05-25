<?php

namespace App;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Tests\Utils\Constants;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @inheritDoc
     */
    public function getProjectDir(): string
    {
        return Constants::appDir();
    }

    /**
     * @inheritDoc
     */
    public function getCacheDir(): string
    {
        if (!$cacheDir = getenv('APP_CACHE_DIR')) {
            throw new RuntimeException('Environment variable APP_CACHE_DIR not set');
        }

        return $cacheDir;
    }

    /**
     * @inheritDoc
     */
    public function getLogDir(): string
    {
        if (!$logDir = getenv('APP_LOG_DIR')) {
            throw new RuntimeException('Environment variable APP_LOG_DIR not set');
        }

        return $logDir;
    }
}
