<?php

namespace App;

use App\Kernel\Exception\EnvironmentVariableNotSet;
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
        return $this->getEnvVar('APP_CACHE_DIR');
    }

    /**
     * @inheritDoc
     */
    public function getLogDir(): string
    {
        return $this->getEnvVar('APP_LOG_DIR');
    }

    /**
     * @param string $varName
     * @return string
     */
    private function getEnvVar(string $varName): string
    {
        if (!$var = getenv($varName)) {
            throw EnvironmentVariableNotSet::fromVarName($varName);
        }

        return $var;
    }
}
