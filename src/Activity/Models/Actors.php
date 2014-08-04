<?php
namespace Activity\Models;

class Actors extends \Dsc\Mongo\Collection
{
    public $__expire = 2592000; // 30 days = 30 * (24*60*60)
    
    public $name; // text, preferably email address
    public $created; // time()
    public $visited; // time()
    public $last_visit; // YYYY-MM-DD
    public $last_activity; // time() when the actor was last active on the site
    public $logged_in = false;
    public $user_id;
    public $session_id;
    public $fingerprints = array();
    public $ips = array();
    public $agents = array();
    public $is_bot; // null|bool
    public $is_bot_last_checked; // time()
    public $is_excluded; // null|bool
    public $is_excluded_last_checked; // time()
    public $action_count; // int
    public $action_count_last_checked; // time()
    
    protected $__collection_name = 'activities.actors';
    
    protected $__config = array(
        'default_sort' => array(
            'visited' => -1
        )
    );    
    
    protected function fetchConditions()
    {
        parent::fetchConditions();
    
        $filter_keyword = $this->getState('filter.keyword');
        if ($filter_keyword && is_string($filter_keyword))
        {
            $key = new \MongoRegex('/' . $filter_keyword . '/i');
    
            $where = array();
    
            $where[] = array(
                'name' => $key
            );
            
            $where[] = array(
                'session_id' => $key
            );
            
            $where[] = array(
                'fingerprints' => $key
            );
            
            $where[] = array(
                'ips' => $key
            );
            
            $where[] = array(
                'agents' => $key
            );            
    
            $this->setCondition('$or', $where);
        }
    
        $filter_user = $this->getState('filter.user');
        if (strlen($filter_user))
        {
            $regex = '/^[0-9a-z]{24}$/';
            if (preg_match($regex, (string) $filter_user))
            {
                $this->setCondition('user_id', new \MongoId( (string) $filter_user ));
            }            
        }
        
        $filter_excluded = $this->getState('filter.excluded');
        if (is_bool($filter_excluded) && !empty($filter_excluded))
        {
            $this->setCondition('is_excluded', true);
        }
        else if (is_bool($filter_excluded) && empty($filter_excluded))
        {
            $this->setCondition('is_excluded', array( '$in' => array( null, false ) ) );
        }
        
        $filter_bot = $this->getState('filter.bot');
        if (is_bool($filter_bot) && !empty($filter_bot))
        {
            $this->setCondition('is_bot', true);
        }
        else if (is_bool($filter_bot) && empty($filter_bot))
        {
            $this->setCondition('is_bot', array( '$in' => array( null, false ) ) );
        }        
        
        $filter_active_after = $this->getState('filter.active_after');
        if (strlen($filter_active_after))
        {
            $this->setCondition( '$and', array( 'last_activity' => array ( '$gt' => $filter_active_after ) ), 'append' );
        }
        
        $filter_active_before = $this->getState('filter.active_before');
        if (strlen($filter_active_before))
        {
            $this->setCondition( '$and', array( 'last_activity' => array ( '$lt' => $filter_active_before ) ), 'append' );
        }
        
        $filter_is_user = $this->getState('filter.is_user');
        if (is_bool($filter_is_user) && !empty($filter_is_user))
        {
            $this->setCondition('user_id', array('$exists' => true, '$nin' => array( '', null ) ));
        }
        else if (is_bool($filter_is_user) && empty($filter_is_user))
        {
            $this->setCondition('user_id', array('$in' => array( '', null ) ));
        }
    
        return $this;
    }    
    
    public static function fetch($email=null)
    {
        $app = \Base::instance();
        
        if (!empty($email)) 
        {
            return static::fetchForEmail($email);
        }
        
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
        $agent = $app->get('AGENT');
        $session_id = \Dsc\System::instance()->get('session')->id();
        $fingerprint = \Dsc\System::instance()->get('session')->get('activity.fingerprint');
        $fingerprints = array_filter( array_unique( array_merge( $actor->fingerprints, array( $fingerprint ) ) ) );
        $ips = array_filter( array_unique( array_merge( $actor->ips, array( $ip ) ) ) );
        $agents = array_filter( array_unique( array_merge( $actor->agents, array( $agent ) ) ) );
        if (empty($actor->id) || $actor->fingerprints != $fingerprints || $actor->ips != $ips || $actor->agents != $agents)
        {
            $actor->agents = $agents;
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
            $app->set('COOKIE.session_actor_ids', json_encode($session_actor_ids), $actor->__expire );
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
                        
            $app->set('COOKIE.session_actor_ids', null, $actor->__expire);
        }
        
        // No matter what, update the cookie with the current actor_id
        // TODO Remove this?  Is this necessary?        
        $app->set('COOKIE.actor_id', (string) $actor->id, $actor->__expire);        
        
        return $actor;
    }    
    
    public static function fetchForSession()
    {
        $actor = new static;
    
        $session_id = \Dsc\System::instance()->get('session')->id();
        $fingerprint = \Dsc\System::instance()->get('session')->get('activity.fingerprint');
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // check if there is a cookie with an actor_id
        // if so, and if that actor_id is valid, use that $actor
        $app = \Base::instance();
        //$cookie_actor_id = $app->get('COOKIE.actor_id');
        $cookie_actor_id = null;
        $cookie_actor_id_ok = false;
        
        $regex = '/^[0-9a-z]{24}$/';
        if (preg_match($regex, (string) $cookie_actor_id))
        {
            $cookie_actor_id_ok = true;
        }
        
        if ($cookie_actor_id && $cookie_actor_id_ok)
        {
            $cookie_actor = new static;
            $cookie_actor->load(array('_id' => new \MongoId( (string) $cookie_actor_id )));
            if (!empty($cookie_actor->id))
            {
                $actor = $cookie_actor;
                $actor->session_id = $session_id;
            }
        }        
        
        if (empty($actor->id))
        {
            // if no cookie, check the session's browser fingerprint
            // is it unique enough to be dependable? is there a match for it in the actor's DB where user_id is null?
            // if so, use that $actor
            /*
            For now, let's put fingerprinting on pause
            if (!empty($fingerprint))
            {
                if (static::collection()->count(array(
                    'fingerprints' => $fingerprint,
                    'ips' => $ip
                )))
                {
                    // Use it
                    $actor->load(array(
                        'fingerprints' => $fingerprint,
                        'ips' => $ip
                    ));
                }
            }
            */
        }
        
        if (empty($actor->id))
        {
            $actor->load(array('session_id' => $session_id));
        }        

        $actor->session_id = $session_id;
        $actor->fingerprints = array_filter( array_unique( array_merge( $actor->fingerprints, array( $fingerprint ) ) ) );
    
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
                
                $actor->fingerprints = array_filter( array_unique( array_merge( $actor->fingerprints, $session_actor->fingerprints ) ) );
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
    
    public static function fetchForEmail($email)
    {
        $actor = new static;
    
        $user = \Users\Models\Users::emailExists( $email );
    
        if (!empty($user->id))
        {
            $actor->load(array('user_id' => $user->id));
            $actor->user_id = $user->id;
            $actor->name = $user->email;
        }
        
        else 
        {
            $actor->load(array('name' => $email));
            $actor->name = $email;
        }
        
        if (empty($actor->id))
        {
            $actor->is_excluded = false;
            $actor->is_excluded_last_checked = date('Y-m-d', strtotime('today'));
            $actor->is_bot = false;
            $actor->is_bot_last_checked = date('Y-m-d', strtotime('today'));
            $actor->save();
        }
        
        if ($actor->is_bot_last_checked < date('Y-m-d', strtotime('today'))
            || $actor->is_excluded_last_checked < date('Y-m-d', strtotime('today'))
            )
        {
            $actor->is_excluded_last_checked = date('Y-m-d', strtotime('today'));
            $actor->is_bot_last_checked = date('Y-m-d', strtotime('today'));
            $actor->store();
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
        
        if (empty($this->is_bot_last_checked) || $this->is_bot_last_checked < date('Y-m-d', strtotime('today')))
        {
            $this->is_bot = (new \Activity\Lib\Excluded)->setActor($this)->isBot();
            $this->is_bot_last_checked = time();
        }
        
        return parent::beforeSave();
    }
    
    /**
     *
     */
    protected function afterSave()
    {
        $this->actionCount();
        
        return parent::afterSave();
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

    /**
     * Is this actor's actions excluded from tracking?
     * 
     * @return boolean
     */
    public function isExcluded()
    {
        if (is_null($this->is_excluded)) 
        {
            $this->is_excluded = \Activity\Lib\Excluded::actor($this);
            if (!empty($this->id)) 
            {
                $this->store();
            }
        }
        
        elseif (empty($this->is_excluded_last_checked) || $this->is_excluded_last_checked < date('Y-m-d', strtotime('today')))
        {
            $this->is_excluded = \Activity\Lib\Excluded::actor($this);
            if (!empty($this->id))
            {
                $this->is_excluded_last_checked = time();
                $this->store();
            }
        }        
        
        return (bool) $this->is_excluded;
    }
    
    /**
     * Is this a bot?
     */
    public function isBot()
    {
        if (is_null($this->is_bot))
        {
            $this->is_bot = (new \Activity\Lib\Excluded)->setActor($this)->isBot();
            if (!empty($this->id))
            {
                $this->store();
            }
        }

        elseif (empty($this->is_bot_last_checked) || $this->is_bot_last_checked < date('Y-m-d', strtotime('today')))
        {
            $this->is_bot = (new \Activity\Lib\Excluded)->setActor($this)->isBot();
            if (!empty($this->id))
            {
                $this->is_bot_last_checked = time();
                $this->store();
            }            
        }
        
        return (bool) $this->is_bot;
    }
    
    /**
     * 
     */
    public function actionCount()
    {
        if (empty($this->action_count) || empty($this->action_count_last_checked) || $this->action_count_last_checked < (time() - 15*60) )
        {
            $this->action_count = \Activity\Models\Actions::collection()->count(array('actor_id' => $this->id));
            if (!empty($this->id))
            {
                $this->action_count_last_checked = time();
                $this->store();
            }
        }
        
        return $this->action_count;
    }

    /**
     * Marks this actor as active 
     * @return \Activity\Models\Actors
     */
    public function markActive($logged_in=null)
    {
        $this->last_activity = time();
        if (is_bool($logged_in)) 
        {
            if (!empty($logged_in)) 
            {
                $this->logged_in = true;
            }
            else 
            {
                $this->logged_in = false;
            }
        }
        $this->store();
        
        return $this;
    }
    
    /**
     * 
     * @param string $after
     * @param string $before
     * @return unknown
     */
    public static function fetchActiveUsers( $after=null, $before=null )
    {
        if (is_null($after)) 
        {
            $last_active = 5; // TODO fetch from a setting  // 5 minutes ago
            $after = time() - ($last_active * 60);
        }
        
        $items = (new static)->setState('filter.active_after', $after)->setState('filter.is_user', true)->getitems();
        
        return $items;
    }
    
    /**
     * Gets the associated user object
     *
     * @return unknown
     */
    public function user()
    {
        $user = (new \Users\Models\Users)->load(array('_id'=>$this->user_id));
    
        return $user;
    }    
}