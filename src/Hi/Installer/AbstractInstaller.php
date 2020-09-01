<?php

namespace Hi\Installer;

use Composer\Installer\InstallerInterface;
use Composer\Installer\LibraryInstaller;

abstract class AbstractInstaller extends LibraryInstaller implements InstallerInterface
{


    function getInstallPath(\Composer\Package\PackageInterface $package, int $iDirsUp = 0):string
    {
        $sPrepend = str_repeat('../', $iDirsUp) .
        $sAbsoluteInstallPath = parent::getInstallPath($package);
        $sRelativeInstallPath = preg_replace('/.+\/vendor/', 'vendor', $sAbsoluteInstallPath);

        return $sPrepend . $sRelativeInstallPath;
    }
}
