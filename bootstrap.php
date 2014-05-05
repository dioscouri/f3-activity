<?php 
class ActivityBootstrap extends \Dsc\Bootstrap{
    protected $dir = __DIR__;
    protected $namespace = 'Activity';

    protected function runAdmin(){

        \Dsc\System::instance()->getDispatcher()->addListener(\Activity\Listener::instance());
        parent::runAdmin();
    }
    //not to be run on the site
    protected function preSite(){}
    protected function runSite(){}
    protected function postSite(){}
}
$app = new ActivityBootstrap();