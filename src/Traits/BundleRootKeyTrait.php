<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Traits;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
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
