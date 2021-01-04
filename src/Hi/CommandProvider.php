<?php
namespace Hi;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Hi\Installer\Domain\Command;


class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return array(new Command());
    }
}

