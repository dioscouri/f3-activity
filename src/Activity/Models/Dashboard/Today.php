<?php
namespace Activity\Models\Dashboard;

class Today extends \Activity\Models\Dashboard
{
    public function total()
    {
        return $this->fetchTotal(date('Y-m-d'));
    }
    
    public function chartData()
    {
        $return = array();
        
        $results = array();
        $results[] = array(
            'Hour',
            'Total'
        );
        
        for ($n=0; $n<24; $n++) 
        {
            $result = $this->fetchTotal(date('Y-m-d '.$n.':00:00', strtotime('today')), date('Y-m-d '.$n.':59:59', strtotime('today')));
            $results[] =  array(
                date('g a', strtotime( '2014-01-01 '.$n.':00:00' ) ),
                $result
            );
        }
        
        $return['haxis.title'] = 'Hour';
        $return['results'] = $results;
        
        return $return;
    }    
}