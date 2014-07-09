<?php
namespace Activity\Models;

class Actors extends \Dsc\Mongo\Collection
{
    public $name; // text, preferably email address
    public $created; // time()
    public $visited; // time()
    public $last_visit; // YYYY-MM-DD
    public $user_id;
    public $session_id;
    public $fingerprints = array();    
    public $ips = array();
    
    protected $__collection_name = 'activities.actors';
    
    public static function fetch()
    {
        $app = \Base::instance();
        
        $user = \Dsc\System::instance()->get('auth')->getIdentity();
        if (!empty($user->id))
        {
            $actor = static::fetchForUser();
        }
        
        else
        {
            $actor = static::fetchForSession();
        }
        
        // has anything changed in the $actor?
        // or is it a new $actor
        $ip = $_SERVER['REMOTE_ADDR'];
        $session_id = \Dsc\System::instance()->get('session')->id();
        $fingerprint = \Dsc\System::instance()->get('session')->get('activity.fingerprint');
        $fingerprints = array_unique( array_merge( $actor->fingerprints, array( $fingerprint ) ) );
        $ips = array_unique( array_merge( $actor->ips, array( $ip ) ) );
        if (empty($actor->id) || $actor->fingerprints != $fingerprints || $actor->ips != $ips)
        {
            $actor->ips = $ips;
            $actor->session_id = $session_id;
            $actor->fingerprints = $fingerprints;
            $actor->save();
        }
        
        // If this is a session actor, then push it into an array of actor_ids
        // so that once the user is identified, we can merge the activity
        if (empty($actor->user_id)) 
        {
            $session_actor_ids = (array) json_decode( $app->get('COOKIE.session_actor_ids') );
            $session_actor_ids = array_unique( array_merge( $session_actor_ids, array( (string) $actor->id ) ) );
            $app->set('COOKIE.session_actor_ids', json_encode($session_actor_ids) );
        }
        
        elseif (!empty($actor->user_id) && $app->get('COOKIE.session_actor_ids'))
        {
            // update all actions with session_actor_ids to use this $actor->id
            $session_actor_ids = (array) json_decode( $app->get('COOKIE.session_actor_ids') );
            if (!empty($session_actor_ids)) 
            {
                $mongo_ids = array();
                foreach ($session_actor_ids as $session_actor_id) 
                {
                    $regex = '/^[0-9a-z]{24}$/';
                    if (preg_match($regex, (string) $session_actor_id))
                    {
                        $mongo_ids[] = new \MongoId($session_actor_id);
                    }
                }
                
                if (!empty($mongo_ids)) 
                {
                    \Activity\Models\Actions::collection()->update(
                        array( 'actor_id' => array( '$in' => $mongo_ids ) ),
                        array( '$set' => array( 'actor_id' => $actor->id, 'actor_name' => $actor->name ) ),
                        array( 'multiple' => true)
                    );
                }
            }
                        
            $app->set('COOKIE.session_actor_ids', null);
        }
        
        // No matter what, update the cookie with the current actor_id
        // TODO Remove this?  Is this necessary?        
        $app->set('COOKIE.actor_id', (string) $actor->id);        
        
        return $actor;
    }    
    
    public static function fetchForSession()
    {
        $actor = new static;
    
        $session_id = \Dsc\System::instance()->get('session')->id();
        $fingerprint = \Dsc\System::instance()->get('session')->get('activity.fingerprint');
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $actor->load(array('session_id' => $session_id));
        
        // If the actor->id is empty, then this session didn't have an actor_id
        if (empty($actor->id)) 
        {
            // check if there is a cookie with an actor_id
            // if so, and if that actor_id is valid, use that $actor
            $app = \Base::instance();
            
            if ($cookie_actor_id = $app->get('COOKIE.actor_id')) 
            {
                $cookie_actor = new static;
                $cookie_actor->load(array('_id' => new \MongoId( (string) $cookie_actor_id )));
                if (!empty($cookie_actor->id))
                {
                    $actor = $cookie_actor;
                    $actor->session_id = $session_id;
                }                
            }            
        }

        if (empty($actor->id))
        {
            // if no cookie, check the session's browser fingerprint
            // is it unique enough to be dependable? is there a match for it in the actor's DB where user_id is null?
            // if so, use that $actor
            if (!empty($fingerprint))
            {
                if (static::collection()->count(array(
                    'fingerprints' => $fingerprint,
                    'user_id' => null,
                    'ips' => $ip
                )))
                {
                    // Use it
                    $actor->load(array(
                        'fingerprints' => $fingerprint,
                        'user_id' => null,
                        'ips' => $ip
                    ));
                }
            }
        }

        $actor->session_id = $session_id;
        $actor->fingerprints = array_unique( array_merge( $actor->fingerprints, array( $fingerprint ) ) );
    
        return $actor;
    }

    public static function fetchForUser()
    {
        $actor = new static;
    
        $user = \Dsc\System::instance()->get('auth')->getIdentity();
    
        if (!empty($user->id))
        {
            $actor->load(array('user_id' => $user->id));
            $actor->user_id = $user->id;
            $actor->name = $user->email;
            
            $session_actor = static::fetchForSession();
    
            // if there was a already a user actor and there is a session actor, 
            // update all actions associated with session actor to assign them to user actor,
            // and merge the session actor's fingerprints with the user actor's fingerprints
            if (!empty($session_actor->id) && !empty($actor->id) && $session_actor->id != $actor->id)
            {
                // Do the update
                \Activity\Models\Actions::collection()->update(
                    array('actor_id'=>$session_actor->id),
                    array('$set' => array( 'actor_id'=>$actor->id, 'actor_name'=>$actor->name ) ),
                    array('multiple'=>true)
                );
                
                $actor->fingerprints = array_unique( array_merge( $actor->fingerprints, $session_actor->fingerprints ) );
                $actor->save();
                $session_actor->remove();                
            }
    
            // if there was no user actor but there IS a session actor, 
            // just add the user_id to the session actor and save it
            if (!empty($session_actor->id) && empty($actor->id))
            {
                $actor = $session_actor;
                $actor->user_id = $user->id;
                $actor->name = $user->email;
                $actor->save();
            }
        }
    
        return $actor;
    }
    
    /**
     * 
     */
    protected function beforeSave()
    {
        if (empty($this->created)) 
        {
            $this->created = time();
        }
        
        if (empty($this->visited) && !empty($this->last_visit))
        {
            $this->visited = strtotime($this->last_visit);
        }
        elseif (empty($this->visited))
        {
            $this->visited = time();
        }
        
        return parent::beforeSave();
    }
    
    /**
     * 
     * @return string
     */
    public function name()
    {
        if (!empty($this->name)) 
        {
            return $this->name;
        }
        
        return (string) $this->id;
    }

}