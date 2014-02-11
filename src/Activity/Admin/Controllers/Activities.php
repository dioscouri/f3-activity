<?php 
namespace Activity\Admin\Controllers;

class Activities extends \Admin\Controllers\BaseAuth  
{	
	
     public function display()
    {
        \Base::instance()->set('pagetitle', 'Activities');
        \Base::instance()->set('subtitle', '');
                
        $view = new \Dsc\Template;
        echo $view->render('activities/home.php');
    }
	

}
?> 
