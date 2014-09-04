<?php 
namespace Activity\ModuleConditions\Visits;

class Condition extends \Modules\Abstracts\Condition
{
    public function bootstrap()
    {
        $this->theme->registerViewPath( __dir__ . '/Views/', 'Activity/ModuleConditions/Visits/Views' );
    
        return parent::bootstrap();
    }
        
    /**
     * Returns the condition's html
     * for the admin-side module-editing form
     */
    public function html() 
    {
        return $this->theme->renderView('Activity/ModuleConditions/Visits/Views::index.php');
    }
    
    /**
     * Determines whether or not this condition passes
     *
     * @param string $route
     * @param unknown $options
    */
    public function passes(\Modules\Models\Modules $module, $route=null, $options=array())
    {
        // if this ruleset is ignored, return null
        if (!in_array($module->{'assignment.activity-visits.method'}, array(
            'include',
        )))
        {
            return null;
        }
        
        $actor = \Activity\Models\Actors::fetch();
        
        if (empty($actor->id)) {
            return null;
        }
        
        $visits = \Activity\Models\Actions::collection()->count(array('actor_id' => $actor->id, 'action' => 'Visited Site' ));
        
        $return = null;
        switch ($module->{'assignment.activity-visits.has_visited'})
        {
            case "1":

                if ($visits > 1) {
                    $return = true;
                } else {
                    $return = false;
                }
                
                break;
            case "0":
                
                if ($visits > 1) {
                    $return = false;
                } else {
                    $return = true;
                }
                
                break;
        }
        
        return $return;
    }    
}