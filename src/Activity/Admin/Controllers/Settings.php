<?php 
namespace Activity\Admin\Controllers;

class Settings extends \Admin\Controllers\BaseAuth 
{
	use \Dsc\Traits\Controllers\Settings;
	
	protected $layout_link = 'Activity/Admin/Views::settings/default.php';
	protected $settings_route = '/admin/activities/settings';
    
    protected function getModel()
    {
        $model = new \Activity\Models\Settings;
        return $model;
    }
}