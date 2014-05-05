<?php 
namespace Activity\Admin\Controllers;

class Activity extends \Admin\Controllers\BaseAuth 
{
    use \Dsc\Traits\Controllers\CrudItemCollection;
    
    protected $list_route = '/admin/activities';
    protected $create_item_route = '/admin/activity/create';
    protected $get_item_route = '/admin/activity/read/{id}';
    protected $edit_item_route = '/admin/activity/edit/{id}';
    
    protected function getModel() 
    {
        $model = new \Activity\Models\Activity;
        return $model; 
    }
    
    protected function getItem()
    {
        $f3 = \Base::instance();
        $id = $this->inputfilter->clean( $f3->get('PARAMS.id'), 'alnum' );
        $model = $this->getModel()
        ->setState('filter.id', $id);
        
        try {
            $item = $model->getItem();
        } catch ( \Exception $e ) {
            \Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error');
            $f3->reroute( $this->list_route );
            return;
        }
    
        return $item;
    }
    
    protected function displayCreate()
    {
        $f3 = \Base::instance();
        $f3->set('pagetitle', 'Create Activity');

        $view = \Dsc\System::instance()->get('theme');
        $view->event = $view->trigger( 'onDisplayAdminActivityEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
        
        echo $view->render('Activity/Admin/Views::activity/create.php');
    }
    
    protected function displayEdit()
    {
        $f3 = \Base::instance();
        $f3->set('pagetitle', 'Edit Activity');
        
  
        $view = \Dsc\System::instance()->get('theme');
        $view->event = $view->trigger( 'onDisplayAdminActivityEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
                
        echo $view->render('Users/Admin/Views::users/edit.php');
    }
    
    /**
     * This controller doesn't allow reading, only editing, so redirect to the edit method
     */
    protected function doRead(array $data, $key=null)
    {
        $f3 = \Base::instance();
        $id = $this->getItem()->get( $this->getItemKey() );
        $route = str_replace('{id}', $id, $this->edit_item_route );
        $f3->reroute( $route );
    }
    
    protected function displayRead() {}
}
