<?php
namespace Activity\Models;

class Activity extends \Dsc\Mongo\Collection
{
    /**
     * Default Document Structure
     * 
     * @var unknown
     */
    public $user = array();
    public $type;
    public $action;
    public $object;
    public $timestamp;
    
    protected $__collection_name = 'activity';

    protected function fetchConditions()
    {
        parent::fetchConditions();
        
        $filter_keyword = $this->getState('filter.keyword');
        if ($filter_keyword && is_string($filter_keyword))
        {
            $key = new \MongoRegex('/' . $filter_keyword . '/i');
            
            $where = array();
            $where[] = array(
                'type' => $key
            );
            $where[] = array(
                'action' => $key
            );
           
            
            $this->setCondition('$or', $where);
        }
        
        $filter_type = $this->getState('filter.type');
        if (strlen($filter_type))
        {
            $this->setCondition('type', $filter_type);
        }
         
        $filter_action = $this->getState('filter.action');
        if (strlen($filter_action))
        {
            $this->setCondition('action', $filter_action);
        }
        
        return $this;
    }


    public function track($type, $action, $object) {
        $this->user['id'] = \Dsc\System::instance()->get( 'session' )->get( 'auth-identity')->get('_id');
        $this->user['name'] = \Dsc\System::instance()->get( 'session' )->get( 'auth-identity')->fullName();
        $this->type = $type;
        $this->action = $action;
        $this->object = $object;
        $this->save();
    }
    
}


?>