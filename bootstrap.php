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
    
    protected function preSite()
    {
        parent::preSite();
    
        if (class_exists('\Minify\Factory'))
        {
            \Minify\Factory::registerPath($this->dir . "/src/");
    
            $files = array(
                'Activity/Assets/js/fingerprint.js',
            );
    
            foreach ($files as $file)
            {
                \Minify\Factory::js($file);
            }
        }
    }
}
$app = new ActivityBootstrap();