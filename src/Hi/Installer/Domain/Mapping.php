<?php
namespace Hi\Installer\Domain;


use Hi\Helpers\DirectoryStructure;

class Mapping
{
    const DIRECTORY = 'directory';
    const FILE = 'file';

    private string $sRelativeSource;
    private string $sRelativeDest;
    private string $sType;
    private string $sSystemId;
    private bool $bCreateSourceIfNotExists;
    private DirectoryStructure $oDirectoryStructure;

    function __constructor(string $sSystemId, string $sRelativeSource, string $sRelativeDest, string $sType, bool $bCreateSourceIfNotExists = true)
    {
        $this->sRelativeSource = $sRelativeSource;
        $this->sRelativeDest = $sRelativeDest;
        $this->sType = $sType;
        $this->bCreateSourceIfNotExists = $bCreateSourceIfNotExists;
        $this->oDirectoryStructure = new DirectoryStructure();
    }

    function sourceMissing():bool
    {
        return file_exists($this->getSourceDir());
    }
    function createSource()
    {
        $sParent = dirname($this->getSourceDir());

        if(!is_dir($sParent))
        {
            mkdir($sParent, 0777, true);
        }

        if($this->getType() === self::FILE)
        {
            touch($this->getSourceDir());
        }
        else
        {
            mkdir($this->getSourceDir());
        }
    }
    function destinationDirExists():bool
    {
        return file_exists($this->getSourceDir());
    }

    function getSourcePath(bool $bAbsolute = true):string
    {
        if($bAbsolute)
        {
            return $this->oDirectoryStructure->getSystemRoot() . DIRECTORY_SEPARATOR . $this->sRelativeSource;
        }
        return  $this->sRelativeSource;
    }
    function getDestPath(bool $bAbsolute = true):string
    {
        if($bAbsolute)
        {
            return $this->oDirectoryStructure->getSystemRoot() . DIRECTORY_SEPARATOR . $this->sRelativeDest;
        }
        return  $this->sRelativeDest;
    }
    function getType():string
    {
        return $this->sType;
    }
    function createIfNotExists():bool
    {
        return $this->bCreateSourceIfNotExists;
    }
}