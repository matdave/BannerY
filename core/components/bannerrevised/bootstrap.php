<?php

/**
 * @var \MODX\Revolution\modX $modx
 * @var array $namespace
 */

// Include autoloader generated by your composer
require_once $namespace['path'] . 'vendor/autoload.php';

// Register base class in the service container
$modx->services->add(
    'bannerrevised',
    function ($c) use ($modx) {
        return new \BannerRevised\v3\BannerRevised($modx);
    }
);

// Load packages model, uncomment if you have DB tables
$modx->addPackage('BannerRevised\Model', $namespace['path'] . 'src/', null, 'BannerRevised\\');

// More about this file: https://docs.modx.com/3.x/en/extending-modx/namespaces#bootstrapping-services
