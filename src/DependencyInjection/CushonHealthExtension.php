<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\DependencyInjection;

use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Cushon\HealthBundle\CushonHealthBundle;
use Cushon\HealthBundle\Traits\BundleRootKeyTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CushonHealthExtension extends Extension
{
    use BundleRootKeyTrait;

    public const KEY_DEPENDENCY_CHECKS = 'dependency_check';

    /** @var FileLocator  */
    private FileLocator $fileLocator;

    /**
     * @param FileLocator $fileLocator
     * @param string $rootKey
     */
    public function __construct(
        FileLocator $fileLocator,
        string $rootKey = CushonHealthBundle::BUNDLE_ROOT_KEY
    ) {
        $this->fileLocator = $fileLocator;
        $this->rootKey = $rootKey;
    }

    /**
     * @param mixed[] $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Comes from the Symfony parent
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->createLoader($container)->load('services.yaml');
        $dependencyCheckTag = $this->generateKeyFromRoot(self::KEY_DEPENDENCY_CHECKS);

        $container->registerForAutoconfiguration(DependencyCheck::class)
            ->addTag($dependencyCheckTag);
    }

    /**
     * @param ContainerBuilder $container
     * @return FileLoader
     */
    private function createLoader(ContainerBuilder $container): FileLoader
    {
        return new YamlFileLoader($container, $this->fileLocator);
    }
}
