<?php
namespace Test\Classes\Api\Accounting\MoneyMonk;

use Core\Notification;
use Core\NotificationAction;
use Exception\LogicException;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    function setUp()
    {
        $aConfigFiles = [];

        $aConfigFiles[] =  "../../../config/cockpit/propel/config.php";
        $aConfigFiles[] =  "../../../config/cockpit/config.php";
        foreach($aConfigFiles as $sConfigFile)
        {
            if(file_exists($sConfigFile))
            {
                require_once $sConfigFile;
            }
            else
            {
                throw new LogicException("File $sConfigFile does not exist");
            }
        }
        parent::setUp();
    }

    /**
     * @throws \Exception
     */
    public function testCreateNotification()
    {
        $aActions = [
            new NotificationAction('Testing', '/bla/blabla', 'danger')
        ];
        Notification::register('warning', 'Reeleezee', 'Could not push invoices', $aActions);

    }
}