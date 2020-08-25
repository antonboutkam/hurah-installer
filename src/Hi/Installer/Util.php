<?php
namespace Hi\Installer;

final class Util
{

    public static function removeSymlink($path)
    {
        if (PHP_SHLIB_SUFFIX === 'dll')
        {
            // windows
            return rmdir($path);
        }
        return unlink($path);
    }
}
