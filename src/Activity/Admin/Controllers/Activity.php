<?php 
namespace Activity\Admin\Controllers;

class Activity extends BaseAuth 
{	
    use \Dsc\Traits\Controllers\CrudItem;

    protected $list_route = '/activity';
    protected $create_item_route = '/activity/create';
    protected $create_item_confirm_route = '/activity/confirm/{id}';
    protected $create_item_customer_route = '/activity/customer/{id}';
    protected $get_item_route = '/activity/view/{id}';    
    protected $edit_item_route = '/activity/edit/{id}';
    
  	protected function getModel() 
    {
        $model = new \Activity\Models\Activities;
        return $model; 
    }
    
    protected function getItem() 
    {
        $f3 = \Base::instance();
        $id = $this->inputfilter->clean( $f3->get('PARAMS.id'), 'alnum' );

        $model = $this->getModel()->setState('filter.id', $id);

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
        $f3->set('pagetitle', 'Activities');
        
        $f3->set('tagid',$f3->get('PARAMS.tagid'));
        $selected = array();
        $flash = \Dsc\Flash::instance();

        $view = new \Dsc\Template;
        echo $view->render('Activity/Views::activities/create.php');
    }

       
    protected function displayEdit()
    {
        $f3 = \Base::instance();
        $f3->set('pagetitle', 'Activity');

     
        $view = new \Dsc\Template;
        echo $view->render('Activity/Views::activities/edit.php');
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
?> 
