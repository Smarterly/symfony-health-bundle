<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Traits;

trait BundleRootKeyTrait
{
    private string $rootKey;

    /**
     * @param string $key
     * @return string
     */
    private function generateKeyFromRoot(string $key): string
    {
        return sprintf('%s.%s', $this->rootKey, $key);
    }
}
