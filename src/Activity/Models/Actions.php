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
            
            // get an array of actor_ids based on this keyword search, then add them as an OR
            $conditions = (new \Activity\Models\Actors)->setState('filter.keyword', $filter_keyword)->conditions();
            if ($actor_ids = \Activity\Models\Actors::collection()->distinct("_id", $conditions)) 
            {
                $where[] = array(
                    'actor_id' => array( '$in' => $actor_ids)
                );
            }
            
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
        // TODO Allow admin to enable/disable tracking in the admin
        
        $actor = static::fetchActor();
        if ($actor->isExcluded()) 
        {
            return false;
        }
        
        $model = new static();
        $model->properties = $properties;
        $model->created = time();
        $model->instance = \Base::instance()->get('APP_NAME');
        $model->action = $action;
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