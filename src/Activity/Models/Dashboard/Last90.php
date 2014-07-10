<?php
namespace Activity\Models\Dashboard;

class Last90 extends \Activity\Models\Dashboard
{
    public function total()
    {
        return $this->fetchTotal(date('Y-m-d 00:00:00', strtotime('today -89 days')));
    }
    
    public function chartData()
    {
        $return = array();
        
        $results = array();
        $results[] = array(
            'M/D',
            'Total'
        );
        
        $start = date('Y-m-d', strtotime('today -89 days'));
        $n=0;
        while ($n<90) 
        {
            $start_date = (new \DateTime($start))->add( \DateInterval::createFromDateString( $n . ' days' ) );
            $start_datetime = $start_date->format('Y-m-d 00:00:00');
            $end_datetime = (new \DateTime($start))->add( \DateInterval::createFromDateString( ($n + 1) . ' days' ) )->format('Y-m-d 00:00:00');
            
            $result = $this->fetchTotal($start_datetime, $end_datetime);
            $results[] = array(
                $start_date->format('m/d'),
                $result
            );
            
            $n++;
        }
        
        $return['haxis.title'] = 'Day';
        $return['results'] = $results;
        
        return $return;
    }
}