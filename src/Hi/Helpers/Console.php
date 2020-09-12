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

    function log($sMessage, $sTopic = 'Novum installer', $color = 'default')
    {
        if($color === 'default')
        {
            $this->io->write(" -  $sTopic <info>{$sMessage}</info>");
        }
        else
        {
            $this->io->write(" -  $sTopic <fg=$color>{$sMessage}</>");
        }

    }
}
