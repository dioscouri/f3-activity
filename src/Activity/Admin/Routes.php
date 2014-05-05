<?php
namespace Activity\Admin;

/**
 * Group class is used to keep track of a group of routes with similar aspects (the same controller, the same f3-app and etc)
 */
class Routes extends \Dsc\Routes\Group
{

    /**
     * Initializes all routes for this group
     * NOTE: This method should be overriden by every group
     */
    public function initialize()
    {
        $this->setDefaults( array(
            'namespace' => '\Activity\Admin\Controllers',
            'url_prefix' => '/admin' 
        ) );
        
        // settings routes
        $this->addSettingsRoutes( '/activity' );
        
        $this->addCrudGroup( 'Activities', 'Activity' );
        
    }
}