<?php

namespace TrackBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;
use TrackBundle\Tool\Installer;

class TrackBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/track/js/pimcore/startup.js',
            '/bundles/track/js/pimcore/newmenu.js'
        ];
    }

    public function getInstaller():Installer
    {
        return $this->container->get(Installer::class);
    }

}
