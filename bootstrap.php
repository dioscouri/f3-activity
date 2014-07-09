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
    
    protected function postSite()
    {
        parent::postSite();
        
        $actor = \Activity\Models\Actors::fetch();
        
        // Track the site visit if it hasn't been done today for this actor
        if (empty($actor->last_visit) || $actor->last_visit < date('Y-m-d', strtotime('today'))) 
        {
            \Activity\Models\Actions::track('Visited Site');
            $actor->set('last_visit', date('Y-m-d', strtotime('today') ) )->set('visited', time())->save();
        }
    }    
}
$app = new ActivityBootstrap();