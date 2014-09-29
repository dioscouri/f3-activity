<?php
namespace Activity\Admin\Controllers;

class Export extends \Admin\Controllers\BaseAuth
{
    public function beforeRoute()
    {
        $this->app->set('meta.title', 'Export | Activity');
    }

    public function index()
    {
        $this->app->set('meta.title', 'Export | Activity');
        
        echo $this->theme->render('Activity/Admin/Views::export/index.php');
    }
    
    public function all()
    {
        $time = time();
        $filename = \Base::instance()->get('PATH_ROOT') . 'tmp/' . $time . '.csv';
        
        $writer = (new \Ddeboer\DataImport\Writer\CsvWriter(","))->setStream(fopen($filename, 'w'));
        
        // Write column headers:
        $writer->writeItem(array(
            'created',
            'actor_name',
            'action',            
            'properties',            
        ));
        
        // write items
        $cursor = (new \Activity\Models\Actions)->collection()->find(array(), array(
            '_id' => 0,
            'created' => 1,
            'actor_name' => 1,
            'action' => 1,
            'properties' => 1,
        ))->sort(array(
            'created' => -1
        ));
        
        foreach ($cursor as $doc)
        {
            $writer->writeItem(array(
                date('Y-m-d H:i:s', $doc['created']),
                $doc['actor_name'],
                $doc['action'],
                \Activity\Models\Actions::displayValue($doc['properties'], 'raw'),
            ));
        }
        
        \Web::instance()->send($filename, null, 0, true);
    }
    
    public function identified()
    {
        $time = time();
        $filename = \Base::instance()->get('PATH_ROOT') . 'tmp/' . $time . '.csv';
    
        $writer = (new \Ddeboer\DataImport\Writer\CsvWriter(","))->setStream(fopen($filename, 'w'));
    
        // Write column headers:
        $writer->writeItem(array(
            'created',
            'actor_name',
            'action',
            'properties',
        ));
    
        $key = new \MongoRegex('/@/i');
        
        // write items
        $cursor = (new \Activity\Models\Actions)->collection()->find(array(
            'actor_name' => $key
        ), array(
            '_id' => 0,
            'created' => 1,
            'actor_name' => 1,
            'action' => 1,
            'properties' => 1,
        ))->sort(array(
            'created' => -1
        ));
    
        foreach ($cursor as $doc)
        {
            $writer->writeItem(array(
                date('Y-m-d H:i:s', $doc['created']),
                $doc['actor_name'],
                $doc['action'],
                \Activity\Models\Actions::displayValue($doc['properties'], 'raw'),
            ));
        }
    
        \Web::instance()->send($filename, null, 0, true);
    }
}