<?php
namespace Activity\Models\Dashboard;

class Last7 extends \Activity\Models\Dashboard
{

    public function totalSales()
    {
        return $this->fetchTotalSales(date('Y-m-d 00:00:00', strtotime('today -7 days')));
    }

    public function topSellers()
    {
        return $this->fetchtopSellers(date('Y-m-d 00:00:00', strtotime('today -7 days')));
    }

    public function salesData()
    {
        $return = array();
        
        $results = array();
        $results[] = array(
            'M/D',
            'Total'
        );
        
        $start = date('Y-m-d', strtotime('today -7 days'));
        for ($n = 0; $n < 7; $n++)
        {
            $result = $this->fetchTotalSales(date('Y-m-d 00:00:00', strtotime($start . ' +' . $n . ' days')), date('Y-m-d 00:00:00', strtotime($start . ' +' . $n + 1 . ' days')));
            $results[] = array(
                date('m/d', strtotime($start . ' +' . $n . ' days')),
                $result
            );
        }

        $return['haxis.title'] = 'Day';
        $return['results'] = $results;
                
        return $return;
    }
}