<?php
class ActivityBootstrap extends \Dsc\Bootstrap
{
    protected $dir = __DIR__;

    protected $namespace = 'Activity';
    
    /**
     * Runs before all global_apps
     * 
     * @param unknown $app
     */
    protected function preBase($app)
    {
        if (class_exists('\Modules\Factory'))
        {
            \Modules\Models\Conditions::register('\Activity\ModuleConditions\Visits', array(
                'title'=>'Visits',
                'icon'=>'fa fa-bolt',
                'type'=>'activity',
                'slug'=>'activity-visits',
            ));
        }
    }

    protected function runAdmin()
    {
        \Dsc\System::instance()->getDispatcher()->addListener(\Activity\Listener::instance());
        
        if (class_exists('\Minify\Factory'))
        {
            \Minify\Factory::registerPath($this->dir . "/src/");
        
            $files = array(
                'Activity/Assets/js/track.js',
            );
        
            foreach ($files as $file)
            {
                \Minify\Factory::js($file);
            }
        }
                
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
                'Activity/Assets/js/track.js',
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
        
        if (!\Audit::instance()->isbot()) 
        {
            $actor = \Activity\Models\Actors::fetch();
            
            $app = \Base::instance();
                       
            // Track the site visit if it hasn't been done today for this actor
            if (empty($actor->last_visit) || $actor->last_visit < date('Y-m-d', strtotime('today')))
            {
                \Activity\Models\Actions::track('Visited Site');
                $actor->set('last_visit', date('Y-m-d', strtotime('today') ) )->set('visited', time())->save();
            }

            if ($this->input->get('ping', null, 'int') != 1) 
            {
                $actor->markActive( !empty( $this->auth->getIdentity()->id ) );
            }
        }
    }    
}
$app = new ActivityBootstrap();