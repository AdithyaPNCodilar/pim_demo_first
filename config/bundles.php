<?php
use Pimcore\Bundle\SimpleBackendSearchBundle\PimcoreSimpleBackendSearchBundle;
return [
    Pimcore\Bundle\ApplicationLoggerBundle\PimcoreApplicationLoggerBundle::class => ['all' => true],
    Pimcore\Bundle\CustomReportsBundle\PimcoreCustomReportsBundle::class => ['all' => true],
    PimcoreSimpleBackendSearchBundle::class => ['all' => true],
];
