<?php

use Pimcore\Bundle\BundleGeneratorBundle\PimcoreBundleGeneratorBundle;
use Pimcore\Bundle\SimpleBackendSearchBundle\PimcoreSimpleBackendSearchBundle;

return [
    PimcoreBundleGeneratorBundle::class => ['all' => true],
    Pimcore\Bundle\ApplicationLoggerBundle\PimcoreApplicationLoggerBundle::class => ['all' => true],
    Pimcore\Bundle\CustomReportsBundle\PimcoreCustomReportsBundle::class => ['all' => true],
    PimcoreSimpleBackendSearchBundle::class => ['all' => true],
    NewBundle\NewBundle::class => ['all' => true],
    CustomBundle\CustomBundle::class => ['all' => true],
    TrackingBundle\TrackingBundle::class => ['all' => true],
    TrackBundle\TrackBundle::class => ['all' => true],


];
