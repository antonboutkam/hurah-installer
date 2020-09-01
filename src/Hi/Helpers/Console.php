<?php

namespace Hi\Helpers;

use Composer\IO\IOInterface;

class Console
{

    private $io;
    function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    function log($sMessage, $sTopic = 'Novum installer')
    {
        $this->io->write(" -  $sTopic <info>{$sMessage}</info>");
    }
}
