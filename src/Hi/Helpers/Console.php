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

    function log($sMessage)
    {
        $this->io->write(" -  Novum installer <info>{$sMessage}</info>");
    }
}
