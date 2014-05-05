<?php 
namespace Activity;

class Listener extends \Prefab 
{
    public function onSystemRebuildMenu( $event )
    {
        if ($mapper = $event->getArgument('mapper')) 
        {
            $mapper->reset();
            $mapper->title = 'Activity';
            $mapper->route = '';
            $mapper->icon = 'fa fa-tasks';
            $mapper->children = array(
                    json_decode(json_encode(array( 'title'=>'Activities', 'route'=>'/admin/activities', 'icon'=>'fa fa-list' )))
                   ,json_decode(json_encode(array( 'title'=>'Settings', 'route'=>'/admin/activities/settings', 'icon'=>'fa fa-cogs' )))
      
            );
            $mapper->save();
            
            \Dsc\System::instance()->addMessage('Activity added its admin menu items.');
        }
    }

     public function afterSaveUsersModelsUsers( $event )
    {

        $doc =  $event->getArgument('model');

        (new \Activity\Models\Activity)->track('save', 'test',  $doc->cast());
       
    }



    
}