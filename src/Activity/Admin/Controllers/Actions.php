<?php 
namespace Activity\Admin\Controllers;

class Actions extends \Admin\Controllers\BaseAuth 
{
    use \Dsc\Traits\Controllers\AdminList;
    
    protected $list_route = '/admin/activities/actions';
    
    protected function getModel()
    {
        $model = new \Activity\Models\Actions;
        return $model;
    }
    
    public function index()
    {
        $model = $this->getModel();
        $state = $model->emptyState()->populateState()->getState();
        $this->app->set('state', $state );
        
        $paginated = $model->paginate();
        $this->app->set('paginated', $paginated );
    
        $this->app->set('meta.title', 'Actions | Activity');
    
        echo $this->theme->render('Activity/Admin/Views::actions/list.php');
    }
}