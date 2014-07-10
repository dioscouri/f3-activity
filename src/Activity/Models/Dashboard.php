<?php
namespace Activity\Models;

class Dashboard extends \Dsc\Models
{
    /**
     * Actually returns total visitors.  
     * TODO Rename function
     * 
     * @param string $start
     * @param string $end
     * @return multitype:number
     */
    public function fetchTotal($start=null, $end=null)
    {
        $conditions = array(
            'action' => 'Visited Site'
        );
        
        if (!empty($start)) {
        	$conditions['created'] = array('$gte' => strtotime($start));
        }
        
        if (!empty($end)) {
            if (empty($conditions['created'])) {
                $conditions['created'] = array('$lt' => strtotime($end));
            } else {
                $conditions['created']['$lt'] = strtotime($end);
            }
            
        }
    
        $return = \Activity\Models\Actions::collection()->count($conditions);

        return $return;
    } 
}