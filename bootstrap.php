<?php
class ActivityBootstrap extends \Dsc\Bootstrap
{
    protected $dir = __DIR__;

    protected $namespace = 'Activity';

    protected function runAdmin()
    {
        \Dsc\System::instance()->getDispatcher()->addListener(\Activity\Listener::instance());
        
        parent::runAdmin();
    }
}
$app = new ActivityBootstrap();