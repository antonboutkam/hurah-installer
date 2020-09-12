<?php

namespace Hi\Installer;

use Composer\Installer\InstallerInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Hi\Helpers\Console;
use function PHPUnit\Framework\returnArgument;

abstract class AbstractInstaller extends LibraryInstaller implements InstallerInterface
{

    function getRelativeInstallPath(PackageInterface $package, int $iDirsUp = 0):string
    {

        $oConsole = new Console($this->io);
        $sPrepend = './';
        if($iDirsUp)
        {
            $sPrepend = str_repeat('../', $iDirsUp);
        }

        $sAbsoluteInstallPath = parent::getInstallPath($package);
        $sPackageDir = basename($sAbsoluteInstallPath); //bv api-belastingdiest
        $sPackageVendorDir = basename(dirname($sAbsoluteInstallPath)); // bv novum


        $oConsole->log("Package vendor dir $sPackageVendorDir");
        $oConsole->log("Package dir $sPackageDir");
        $oConsole->log("Dirs up $iDirsUp");
        $oConsole->log("Prepend $sPrepend");
        $oConsole->log("Absolute install path  $sAbsoluteInstallPath");

        $sRelativeInstallPath = "{$sPrepend}vendor/$sPackageVendorDir/$sPackageDir";
        $oConsole->log("Relative install path  $sRelativeInstallPath");

        return $sRelativeInstallPath;
    }

    function getInstallPath(PackageInterface $package):string
    {
        return parent::getInstallPath($package);
    }
}
