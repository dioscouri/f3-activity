<?php 
namespace Activity\Admin\Models;

Class Activities Extends \Dsc\Models\Db\Mongo  {

    protected $collection = 'activities';
    protected $default_ordering_direction = '1';
    protected $default_ordering_field = 'type';

    public function __construct($config=array())
    {
        $config['filter_fields'] = array(
            'name', 'start_date', 'end_date'
        );
        $config['order_directions'] = array('1', '-1');
        
        parent::__construct($config);
    }

    public function prefab( $source=array(), $options=array() )
    {
        $prefab = new \Activity\Models\Prefabs\Activity($source, $options);
        
        return $prefab;
    }

    /**
    * An alias for the save command, used only for creating a new object
    *
    * @param array $values
    * @param array $options
    */
    public function create( $values, $options=array() )
    {
        $values = $this->prefab( $values, $options )->cast();

        return $this->save( $values, $options );
    }

    


}

?>