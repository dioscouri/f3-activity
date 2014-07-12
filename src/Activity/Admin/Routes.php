<?php
namespace Activity\Admin;

class Routes extends \Dsc\Routes\Group
{

    public function initialize()
    {
        $this->setDefaults( array(
            'namespace' => '\Activity\Admin\Controllers',
            'url_prefix' => '/admin/activities' 
        ) );
        
        $this->addSettingsRoutes();
        
        $this->addCrudGroup( 'Actions', 'Action' );
        $this->addCrudGroup( 'Actors', 'Actor' );
    }
}