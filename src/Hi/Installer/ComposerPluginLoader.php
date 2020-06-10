<?php
namespace Hi\Installer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class ComposerPluginLoader implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        $oInstallationManager = $composer->getInstallationManager();
        $oSiteInstaller = new Site\Installer($io, $composer);
        $oInstallationManager->addInstaller($oSiteInstaller);

        $oDomainInstaller = new Api\Installer($io, $composer);
        $oInstallationManager->addInstaller($oDomainInstaller);

        $oApiInstaller = new Domain\Installer($io, $composer);
        $oInstallationManager->addInstaller($oApiInstaller);

        $oApiInstaller = new System\Installer($io, $composer);
        $oInstallationManager->addInstaller($oApiInstaller);
    }
}
