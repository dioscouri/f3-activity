<?php
namespace Activity\Models;

class Settings extends \Dsc\Mongo\Collections\Settings
{
    public $general = array();
    public $tracking = array(
        'enabled' => 1
    );
    
    public $excluded = array(
        'ips' => array()
    );    
    
    public $pusher = array();
    
    protected $__type = 'activity.settings';
    
    protected function beforeSave()
    {
        if (!empty($this->{'excluded.ips'}) && !is_array($this->{'excluded.ips'})) 
        {
            $excluded_ips = array_filter( array_unique( array_values( \Base::instance()->split( $this->{'excluded.ips'} ) ) ) );
            $this->{'excluded.ips'} = $excluded_ips; 
        }
        
        return parent::beforeSave();
    }
}