<?php 
namespace Activity;

class Pusherlistener extends \Prefab 
{   
	var $pusher;
	var $f3;
	var $event;

	 function __construct() {
	 	
	 	$this->f3 = $f3 = \Base::instance();
	 	$this->event = \Base::instance()->get('SESSION.event');
	 	$this->pusher = new \Pusher($this->f3->get('pusher_key'), $this->f3->get('pusher_secret'), $this->f3->get('pusher_app_id'));
	 //	parent::__construct();
	 }

     
    public function onAfterSaveSITEModelsActivity( $event )
    {   
        $mapper = $event->getArgument('mapper');
        
        $pusherEvent = 'createActivity';
      //  $pusherEvent .= ucfirst($mapper->type);
       // $pusherEvent .= ucfirst($mapper->action);
       
        $helper = new \Dash\Helpers\Activity($mapper);
        $data = $helper->getData();
        $this->pusher->trigger($this->f3->get('event.db'), $pusherEvent, $data);
        

        return $event;
    } 


    public function onAfterSaveSITEModelsAttendees( $event )
    {   
       /*  $mapper = $event->getArgument('mapper');
        
        $WBmodel = new \Dash\Models\Event\Wristbands;
        $used = (int) $WBmodel->getTotalCount();

        $ordered = (int) $this->event->{'wristbands.ordered'};
        $available = $ordered - $used;
       
        $this->pusher->trigger($this->f3->get('event.db'), 'updateWbAvailable', array( 'message' => $available));
        */
        return $event;
    }

      public function onAfterSaveSITEModelsEvents( $event )
    {   
        /* $mapper = $event->getArgument('mapper');*/
        
        return $event;
    }
       public function onAfterSaveSITEModelsRoles( $event )
    {   
          /* $mapper = $event->getArgument('mapper');*/
        
        return $event;
    }

  
     public function onAfterSaveSITEModelsTickets( $event )
    {   
          /* $mapper = $event->getArgument('mapper');*/
        

        return $event;
    }

 
}