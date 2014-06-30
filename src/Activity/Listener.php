<?php
namespace Activity;

class Listener extends \Dsc\Singleton
{

    public function onSystemRebuildMenu($event)
    {
        if ($model = $event->getArgument('model'))
        {
            $root = $event->getArgument('root');
            $activity = clone $model;
            
            $activity->insert(array(
                'type' => 'admin.nav',
                'priority' => 150,
                'title' => 'Activity',
                'icon' => 'fa fa-bolt',
                'is_root' => false,
                'tree' => $root,
                'base' => '/admin/activities'
            ));
            
            $children = array(
                array(
                    'title' => 'Actions',
                    'route' => '/admin/activities/actions',
                    'icon' => 'fa fa-bar-chart-o'
                ),
                array(
                    'title' => 'Settings',
                    'route' => './admin/activities/settings',
                    'icon' => 'fa fa-cogs'
                )                
            );
            
            $activity->addChildren($children, $root);
            
            \Dsc\System::instance()->addMessage('Activity added its admin menu items.');
        }
    }
    
    public function afterCreateActivityModelsActions($event) {
    	$model = $event->getArgument('model');
    	$this->pusher->trigger('dashboard','event', array('actor_id' => $model->actor_id, 'action'=> $model->action)); 
    }

    
}