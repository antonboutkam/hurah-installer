<?php
namespace Hi;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Hi\Installer\Site\Installer as SiteInstaller;
use Hi\Installer\Api\Installer as ApiInstaller;
use Hi\Installer\Domain\Installer as DomainInstaller;
use Hi\Installer\Core\Installer as CoreInstaller;

class Plugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        echo "Activating plugins" . PHP_EOL;
        $oInstallationManager = $composer->getInstallationManager();
        $oSiteInstaller = new SiteInstaller($io, $composer);
        $oInstallationManager->addInstaller($oSiteInstaller);

        $oDomainInstaller = new ApiInstaller($io, $composer);
        $oInstallationManager->addInstaller($oDomainInstaller);

        $oApiInstaller = new DomainInstaller($io, $composer);
        $oInstallationManager->addInstaller($oApiInstaller);

        $oApiInstaller = new CoreInstaller($io, $composer);
        $oInstallationManager->addInstaller($oApiInstaller);
    }
}