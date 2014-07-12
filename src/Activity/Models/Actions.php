<?php
namespace Activity\Models;

class Actions extends \Dsc\Mongo\Collection
{
    public $actor_id; // MongoId
    public $actor_name; // text
    public $action; // text
    public $created; // time()
    
    protected $__collection_name = 'activities.actions';
    
    protected $__config = array(
        'default_sort' => array(
            'created' => -1
        )
    );    

    protected function fetchConditions()
    {
        parent::fetchConditions();
        
        $filter_keyword = $this->getState('filter.keyword');
        if ($filter_keyword && is_string($filter_keyword))
        {
            $key = new \MongoRegex('/' . $filter_keyword . '/i');
            
            $where = array();
            
            $where[] = array(
                'actor_name' => $key
            );
            
            $where[] = array(
                'action' => $key
            );
            
            $this->setCondition('$or', $where);
        }
        
        $filter_action = $this->getState('filter.action');
        if (strlen($filter_action))
        {
            $this->setCondition('action', $filter_action);
        }
        
        return $this;
    }
    
    public static function track( $action, $properties=array() )
    {
        if (\Audit::instance()->isbot()) {
            return false;            
        }
        
        // TODO Allow admin to enable/disable tracking in the admin
        
        $model = new static();
        $model->properties = $properties;
        $model->created = time();
        $model->instance = \Base::instance()->get('APP_NAME');
        $model->action = $action;
        
        $actor = static::fetchActor();
        
        $model->actor_id = $actor->id;
        $model->actor_name = $actor->name();
        $model->store();
       	
        \Dsc\System::instance()->trigger('afterCreateActivityModelsActions', array('model' => $model));
        
        return true;
    }
    
    public static function fetchActor()
    {
        return \Activity\Models\Actors::fetch();
    }
    
    public function displayValue($value)
    {
        if (is_string($value)) 
        {
            return $value;
        }
        
        $string = '';
        if (is_array($value)) 
        {
            $string .= '<ul>';
            foreach ($value as $k=>$v) 
            {
                if (is_array($v)) {
                    $string .= '<li><b>'. $k .':</b> ' . \Joomla\Utilities\ArrayHelper::toString($v) . '</li>';
                } else {
                    $string .= '<li><b>'. $k .':</b> ' . $v . '</li>';
                }                
            }
            $string .= '</ul>';
        }
        
        return $string;
    }
    
    /**
     * Gets the associated actor object
     *
     * @return unknown
     */
    public function actor()
    {
        $actor = (new \Activity\Models\Actors)->load(array('_id'=>$this->actor_id));
    
        return $actor;
    }    
}

?>