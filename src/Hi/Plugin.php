<?php
namespace Hi;

use Hi\Helpers\Console;
use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Hi\Helpers\ConsoleColor;
use Hi\Installer\Site\Installer as SiteInstaller;
use Hi\Installer\Api\Installer as ApiInstaller;
use Hi\Installer\Domain\Installer as DomainInstaller;
use Hi\Installer\Core\Installer as CoreInstaller;
use Hi\Installer\Env\Installer as EnvInstaller;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        try
        {
            $oConsole = new Console($io);
            $oConsole->log("Activating Novum installer", "Novum component loaders", ConsoleColor::blue);
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
        catch(\Exception $e)
        {
            echo $e->getMessage() . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
        }

    }

    function postInstall(Event $event)
    {
        $oDbInstaller = new EnvInstaller();
        $oDbInstaller->install($event);
    }
    function postUpdate(Event $event)
    {
        $this->postInstall($event);
    }
    public static function getSubscribedEvents()
    {
        return [
            'post-install-cmd' => 'postInstall',
            'post-update-cmd'  => 'postUpdate',
        ];
    }
}
