<?php 
namespace Activity\Site\Controllers;

class Fingerprint extends \Dsc\Controller 
{
    public function index()
    {
        $fingerprint = $this->inputfilter->clean($this->app->get('PARAMS.id'), 'alnum');
        \Dsc\System::instance()->get('session')->set('activity.fingerprint', $fingerprint);
                
        $identity = $this->getIdentity();
        if (!empty($identity->id)) 
        {
            $identity = $identity->reload();
            $fingerprints = (array) $identity->{'activities.fingerprints'};
            
            // is this a new fingerprint?
            if (!in_array($fingerprint, $fingerprints)) 
            {
                $fingerprints[] = $fingerprint;
                $identity->{'activities.fingerprints'} = $fingerprints;
                $identity->save();

                // ensure that the fingerprint is added to the actor, which is 
                // something that is handled by fetch()
                $actor = \Activity\Models\Actors::fetch();
            }
        }
        
        // the user not yet identified
        // so store the fingerprint in the session actor
        else 
        {
            $actor = \Activity\Models\Actors::fetch();
            $actor->fingerprints = array_unique( array_merge( $actor->fingerprints, array( $fingerprint ) ) );
            $actor->save();
        }
        
    }
}