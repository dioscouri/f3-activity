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
            $mapper->icon = 'fa fa-activiy';
            $mapper->children = array(
                    json_decode(json_encode(array( 'title'=>'Activity', 'route'=>'/admin/activity', 'icon'=>'fa fa-list' )))
                        
            );
            $mapper->save();
            
            \Dsc\System::instance()->addMessage('Activity added its admin menu items.');
        }
    }
    
}