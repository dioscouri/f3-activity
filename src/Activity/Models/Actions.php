<?php
namespace Activity\Models;

class Actions extends \Dsc\Mongo\Collection
{
    public $actor_id; // MongoId
    public $actor_name; // text
    public $action; // text
    public $created; // time()
    
    protected $__collection_name = 'activities.actions';

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
        $model = new static();
        $model->properties = $properties;
        $model->created = time();
        $model->instance = \Base::instance()->get('APP_NAME');
        $model->action = $action;
        $model->actor_id = static::fetchActorId();
        $model->actor_name = static::fetchActorName();
        $model->store();
       	
        \Dsc\System::instance()->trigger('afterCreateActivityModelsActions', array('model' => $model));
    }
    
    public static function fetchActorId()
    {
        return md5(rand(5,1000));
    }
    
    public static function fetchActorName()
    {
    	return 'John Smith';
    }
    
  
    
    
}

?>