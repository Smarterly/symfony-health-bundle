<?php

declare(strict_types=1);

use Cushon\HealthBundle\CushonHealthBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle;

return [
    FrameworkBundle::class => ['all' => true],
    FriendsOfBehatSymfonyExtensionBundle::class => [
        'test' => true,
        'dev' => true,
    ],
    CushonHealthBundle::class => ['all' => true],
];
