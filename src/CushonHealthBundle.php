<?php

declare(strict_types=1);

namespace Cushon\HealthBundle;

use Cushon\HealthBundle\DependencyInjection\Compiler\DependencyCheckPass;
use Cushon\HealthBundle\DependencyInjection\CushonHealthExtension;
use Cushon\HealthBundle\Traits\BundleRootKeyTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CushonHealthBundle extends Bundle
{
    use BundleRootKeyTrait;

    public const BUNDLE_PATH = __DIR__;
    public const CONFIG_PATH = self::BUNDLE_PATH . '/Resources/config';

    public const BUNDLE_ROOT_KEY = 'cushon_health';

    public const BUNDLE_PATH_KEY = 'bundle_path';

    /**
     * @param string $rootKey
     */
    public function __construct(string $rootKey = self::BUNDLE_ROOT_KEY)
    {
        $this->rootKey = $rootKey;
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container): void
    {
        $container->setParameter($this->generateKeyFromRoot(self::BUNDLE_PATH_KEY), self::BUNDLE_PATH);
    }

    /**
     * @inheritDoc
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new CushonHealthExtension(
            new FileLocator(self::CONFIG_PATH),
            $this->rootKey
        );
    }
}
