<?php

namespace Hi\Installer;

use Composer\Installer\InstallerInterface;
use Composer\Installer\LibraryInstaller;

abstract class AbstractInstaller extends LibraryInstaller implements InstallerInterface
{
    protected function removeSymlink($path)
    {
        if (PHP_SHLIB_SUFFIX === 'dll')
        {
            // windows
            return rmdir($path);
        }
        return unlink($path);
    }
}
