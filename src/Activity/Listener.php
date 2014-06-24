<?php
namespace Activity;

class Listener extends \Prefab
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
                    'title' => 'List',
                    'route' => '/admin/activities',
                    'icon' => 'fa fa-bar-chart-o'
                ),
            );
            
            $activity->addChildren($children, $root);
            
            \Dsc\System::instance()->addMessage('Activity added its admin menu items.');
        }
    }

    public function afterSaveUsersModelsUsers($event)
    {
        $doc = $event->getArgument('model');
        
        (new \Activity\Models\Activity())->track('save', 'test', $doc->cast());
    }
}