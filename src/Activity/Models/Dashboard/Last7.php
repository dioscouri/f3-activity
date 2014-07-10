<?php
namespace Activity\Models\Dashboard;

class Last7 extends \Activity\Models\Dashboard
{
    public function total()
    {
        return $this->fetchTotal(date('Y-m-d 00:00:00', strtotime('today -6 days')));
    }

    public function chartData()
    {
        $return = array();
        
        $results = array();
        $results[] = array(
            'M/D',
            'Total'
        );
        
        $start = date('Y-m-d', strtotime('today -6 days'));
        for ($n = 0; $n < 7; $n++)
        {
            $start_date = (new \DateTime($start))->add( \DateInterval::createFromDateString( $n . ' days' ) );
            $start_datetime = $start_date->format('Y-m-d 00:00:00');
            $end_datetime = (new \DateTime($start))->add( \DateInterval::createFromDateString( ($n + 1) . ' days' ) )->format('Y-m-d 00:00:00');
            
            $result = $this->fetchTotal($start_datetime, $end_datetime);
            $results[] = array(
                $start_date->format('m/d'),
                $result
            );
        }

        $return['haxis.title'] = 'Day';
        $return['results'] = $results;
                
        return $return;
    }
}