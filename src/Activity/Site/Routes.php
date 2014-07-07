<?php
namespace Activity\Site;

class Routes extends \Dsc\Routes\Group
{
    public function initialize()
    {
        $this->setDefaults( array(
            'namespace' => '\Activity\Site\Controllers',
            'url_prefix' => '/activity' 
        ) );
        
        $this->add( '/fp/@id [ajax]', 'GET|POST', array(
            'controller' => 'Fingerprint',
            'action' => 'index'
        ) );        
    }
}