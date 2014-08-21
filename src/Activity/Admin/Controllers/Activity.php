<?php 
namespace Activity\Admin\Controllers;

class Activity extends \Admin\Controllers\BaseAuth 
{
    public function track()
    {
        $action = $this->input->get('action', null, 'string');
        $properties = $this->input->get('properties', null, 'string');
        if (!empty($properties) && is_string($properties)) {
            $properties = json_decode($properties);
        }
        
        if (!empty($action)) 
        {
            \Activity\Models\Actions::track($action, $properties);
        }
    }
}