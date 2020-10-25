<?php
namespace Hi\Installer\Domain;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Hi\Helpers\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * The domain installer is called automatically during the installation of a domain. This script can be invoked when for
 * some reason the domain installation actions need to be executd manually.
 *
 * Class CommandProvider
 * @package My\Composer
 */
class Command extends BaseCommand
{
    protected function configure()
    {
        $this->setName('novum:domain-installer');
        $this->setDescription('Can be invoked when for some reason the domain installation actions need to be executd manually.');
        $this->addArgument('system_id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sSystemId = $input->getArgument('system_id');

        $sNamespace = Util::namespaceFromSystemId($sSystemId);


        Util::createSymlinkMapping(new Console($this->getIO()), $sSystemId, $sNamespace);

        echo "System id: $sSystemId, namespace: $sNamespace" . PHP_EOL;
        print_r(glob('./*'));

        $output->writeln('Executing');
    }
}
