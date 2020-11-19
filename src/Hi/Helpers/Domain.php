<?php

namespace Hi\Helpers;

use Core\DataType\Path;
use Core\Utils;
use DirectoryIterator;


final class Domain
{
    private $sPathname;
    private $sSystemId;
    private $oDirectoryStructure;
    function __construct(DirectoryIterator $oDirectory)
    {
        $this->oDirectoryStructure = new DirectoryStructure();
        $this->sPathname = $oDirectory->getPathname();
        $this->sSystemId = $oDirectory->getFilename();
    }
    function getConfigPath(bool $bAbsolute = false):Path
    {
        $oDirectoryStructure = new DirectoryStructure();
        return new Path(Utils::makePath($oDirectoryStructure->getSystemDir(true), 'config', $this->getSystemID(), 'config.php'));
    }
    function getPropelConfigPath(bool $bAbsolute = false):Path
    {
        $oDirectoryStructure = new DirectoryStructure();
        return new Path(Utils::makePath($oDirectoryStructure->getSystemDir(true), 'config', $this->getSystemID(), 'propel', 'config.php'));
    }
    function getPathname(bool $bAbsolute = false):string
    {
        if($bAbsolute)
        {
            $oDirectoryStructure = new DirectoryStructure();
            return Utils::makePath($oDirectoryStructure->getSystemDir(true), $this->sPathname);
        }
        return $this->sPathname;
    }
    function getSystemID():string
    {
        return $this->sSystemId;
    }
    function getDataDir():string
    {
        $oDirectoryStructure = new DirectoryStructure();
        return $oDirectoryStructure->getDataDir() . '/' . $this->getSystemID();
    }
    function makeDbUser():string
    {
        return str_replace('.', '_', $this->getSystemID());
    }
    function getSystemRoot():string
    {
        return $this->oDirectoryStructure->getSystemRoot();
    }
    function makeDbPass($length = 15)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        $characterListLength = mb_strlen($characters, '8bit') - 1;
        foreach(range(1, $length) as $i){
            $password .= $characters[random_int(0, $characterListLength)];
        }
        return $password;
    }
}


