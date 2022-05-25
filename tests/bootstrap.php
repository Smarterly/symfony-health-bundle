<?php

use Symfony\Component\Dotenv\Dotenv;
use Tests\Utils\Constants;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(Constants::appDir() . '/config/bootstrap.php')) {
    require Constants::appDir() . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(Constants::rootDir() . '/.env');
}
