<?php 
namespace Activity\Admin\Controllers;

class Activities extends \Admin\Controllers\BaseAuth  
{	
	
     public function index()
    {
        \Base::instance()->set('pagetitle', 'Activities');
        \Base::instance()->set('subtitle', '');
         $model = new \Activity\Models\Activity;
        \Base::instance()->set('state', $model->populateState()->getState() );
        \Base::instance()->set('paginated', $model->paginate() );

        echo \Dsc\System::instance()->get('theme')->renderTheme('Activity/Admin/Views::activities/list.php');     
    	
    }
	

}
?> 
