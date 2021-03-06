<?php
namespace Hi\Installer;

final class Util
{
    public static function makePath(...$params):string
    {
        return join(DIRECTORY_SEPARATOR, $params);
    }

    public static function removeSymlink($path)
    {
        if(!realpath($path))
        {
            return false;
        }
        if (PHP_SHLIB_SUFFIX === 'dll')
        {
            // windows
            return rmdir($path);
        }
        return unlink($path);
    }
}
