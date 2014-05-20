<?php 
namespace Activity;

class Listener extends \Prefab 
{
    public function onSystemRebuildMenu( $event )
    {
        if ($model = $event->getArgument('model'))
        {  
            $root = $event->getArgument( 'root' );
            $modules = clone $model;
        
            $modules->insert(
                    array(
                            'type'  => 'admin.nav',
                            'priority' => 200,
                            'title' => 'Activity',
                            'icon'  => 'fa fa-building',
                            'is_root' => false,
                            'tree'  => $root,
                            'base' => '/admin/activity',
                    )
            );
            
            $children = array(
                    array( 'title'=>'List', 'route'=>'/admin/activity', 'icon'=>'fa fa-list' ),
                    array( 'title'=>'Add New', 'route'=>'/admin/activity/create', 'icon'=>'fa fa-plus' ),
            );
            $modules->addChildrenItems( $children, $root );
            
            \Dsc\System::instance()->addMessage('Activity added its admin menu items.');
        }
    }

     public function afterSaveUsersModelsUsers( $event )
    {

        $doc =  $event->getArgument('model');

        (new \Activity\Models\Activity)->track('save', 'test',  $doc->cast());
       
    }



    
}