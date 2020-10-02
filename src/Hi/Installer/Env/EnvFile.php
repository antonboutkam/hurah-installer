<?php
namespace Hi\Installer\Env;

class EnvFile
{
    private $sSystemId, $sSystemRoot, $sDataDir, $sDbUser, $sDbHost, $sDbPass;

    function __toString()
    {
        return  <<<EOT
SYSTEM_ID=$this->sSystemId
SYSTEM_ROOT=$this->sSystemRoot
DATA_DIR=$this->sDataDir
DB_USER=$this->sDbUser
DB_HOST=$this->sDbHost
DB_PASS=$this->sDbPass
EOT;
    }

    function __construct($sSystemId, $sSystemRoot, $sDataDir, $sDbHost, $sDbUser, $sDbPass)
    {
        $this->sSystemId = $sSystemId;
        $this->sSystemRoot = $sSystemRoot;
        $this->sDataDir = $sDataDir;
        $this->sDbUser = $sDbUser;
        $this->sDbHost = $sDbHost;
        $this->sDbPass = $sDbPass;
    }
}
