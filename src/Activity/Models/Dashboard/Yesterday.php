<?php
namespace Activity\Models\Dashboard;

class Yesterday extends \Activity\Models\Dashboard
{
    public function totalSales()
    {
        return $this->fetchTotalSales(date('Y-m-d 00:00:00', strtotime('yesterday')), date('Y-m-d 00:00:00', strtotime('today')));
    }
    
    public function topSellers()
    {
        return $this->fetchtopSellers(date('Y-m-d 00:00:00', strtotime('yesterday')), date('Y-m-d 00:00:00', strtotime('today')));
    }

    public function salesData()
    {
        $return = array();
        
        $results = array();
        $results[] = array(
            'Hour',
            'Total'
        );
        
        for ($n=0; $n<24; $n++) 
        {
            $result = $this->fetchTotalSales(date('Y-m-d '.$n.':00:00', strtotime('yesterday')), date('Y-m-d '.$n.':59:59', strtotime('yesterday')));
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