<?php
namespace Hi;

use Exception;
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
use Composer\Plugin\Capable;

class Plugin implements PluginInterface, EventSubscriberInterface, Capable
{
    public function deactivate(Composer $composer, IOInterface $io)
    {

    }
    public function uninstall(Composer $composer, IOInterface $io)
    {

    }
    public function activate(Composer $composer, IOInterface $io)
    {
        try
        {
            $oConsole = new Console($io);
            $oConsole->log("Activating Novum installer", "Novum component loaders", ConsoleColor::blue);
            $oInstallationManager = $composer->getInstallationManager();

            $oConsole->log("Site installer", "Novum component loaders", ConsoleColor::blue);
            $oSiteInstaller = new SiteInstaller($io, $composer);
            $oInstallationManager->addInstaller($oSiteInstaller);

            $oConsole->log("Api installer", "Novum component loaders", ConsoleColor::blue);
            $oApiInstaller = new ApiInstaller($io, $composer);
            $oInstallationManager->addInstaller($oApiInstaller);

            $oConsole->log("Domain installer", "Novum component loaders", ConsoleColor::blue);
            $oDomainInstaller = new DomainInstaller($io, $composer);
            $oInstallationManager->addInstaller($oDomainInstaller);

            $oConsole->log("Core installer", "Novum component loaders", ConsoleColor::blue);
            $oCoreInstaller = new CoreInstaller($io, $composer);
            $oInstallationManager->addInstaller($oCoreInstaller);
        }
        catch(Exception $e)
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
        // $event->getIO();
        // $event->getComposer()

        $this->postInstall($event);
    }
    public function getCapabilities()
    {
        return array(
            'Composer\Plugin\Capability\CommandProvider' => CommandProvider::class,
        );
    }
    public static function getSubscribedEvents()
    {
        return [
            'post-install-cmd' => 'postInstall',
            'post-update-cmd'  => 'postUpdate',
        ];
    }
}
