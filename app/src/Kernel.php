<?php

namespace App;

use App\Kernel\Exception\EnvironmentVariableNotSet;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Dotenv\Dotenv;
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
        $this->bootEnv();
        if (!$var = $_ENV[$varName]) {
            throw EnvironmentVariableNotSet::fromVarName($varName);
        }

        return $var;
    }

    private function bootEnv(): void
    {
        (new Dotenv())->bootEnv(Constants::rootDir() . '/.env');
    }
}
