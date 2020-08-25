<?php
namespace Hi\Installer\Api;


final class Installer extends \Hi\Installer\Site\Installer
{
    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'novum-api' === $packageType || 'hurah-api' === $packageType ;
    }
}
