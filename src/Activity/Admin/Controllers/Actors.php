<?php 
namespace Activity\Admin\Controllers;

class Actors extends \Admin\Controllers\BaseAuth 
{
    use \Dsc\Traits\Controllers\AdminList;
    
    protected $list_route = '/admin/activities/actors';
    
    protected function getModel()
    {
        $model = new \Activity\Models\Actors;
        return $model;
    }
    
    public function index()
    {
        $model = $this->getModel();
        $state = $model->emptyState()->populateState()->getState();
        $this->app->set('state', $state );
        
        $paginated = $model->paginate();
        $this->app->set('paginated', $paginated );
    
        $this->app->set('meta.title', 'Actors | Activity');
    
        echo $this->theme->render('Activity/Admin/Views::actors/list.php');
    }
}