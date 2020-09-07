<?php

namespace Hi\Installer;

use Composer\Installer\InstallerInterface;
use Composer\Installer\LibraryInstaller;
use function PHPUnit\Framework\returnArgument;

abstract class AbstractInstaller extends LibraryInstaller implements InstallerInterface
{

    function getRelativeInstallPath(\Composer\Package\PackageInterface $package, int $iDirsUp = 0):string
    {
        $sPrepend = str_repeat('../', $iDirsUp) .
        $sAbsoluteInstallPath = parent::getInstallPath($package);
        $sPackageDir = basename($sAbsoluteInstallPath); //bv api-belastingdiest
        $sPackageVendorDir = basename(dirname($sAbsoluteInstallPath)); // bv novum

        $sRelativeInstallPath = "{$sPrepend}vendor/$sPackageVendorDir/$sPackageDir";

        return $sRelativeInstallPath;
    }

    function getInstallPath(\Composer\Package\PackageInterface $package):string
    {
        return parent::getInstallPath($package);
    }
}
