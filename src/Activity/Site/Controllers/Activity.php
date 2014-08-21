<?php 
namespace Activity\Site\Controllers;

class Activity extends \Dsc\Controller 
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