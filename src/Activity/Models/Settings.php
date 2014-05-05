<?php
namespace Activity\Models;

class Settings extends \Dsc\Mongo\Collections\Settings
{
    public $general = array();
    public $pusher = array();
    
    protected $__type = 'activity.settings';
}