<?php

namespace Hi\Helpers;

use DirectoryIterator;


final class Domain
{
    private $oIterator;
    private $oDirectoryStructure;
    function __construct(DirectoryIterator $oDirectory)
    {
        $this->oDirectoryStructure = new DirectoryStructure();
        $this->oIterator = $oDirectory;
    }

    function getDirectory():DirectoryIterator
    {
        return $this->oIterator;
    }
    function getSystemID():string
    {
        return $this->oIterator->getFilename();
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


