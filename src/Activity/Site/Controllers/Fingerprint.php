<?php 
namespace Activity\Site\Controllers;

class Fingerprint extends \Dsc\Controller 
{
    public function index()
    {
        $fingerprint = $this->inputfilter->clean($this->app->get('PARAMS.id'), 'alnum');
        
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
                
                // TODO Update all actions with this fingerprint an associate them with this user
                /*
                \Dsc\Queue::task('\Affiliates\Models\Referrals::checkFingerprints', array('id'=>$identity->id), array(
                    'title' => 'Checking browser fingerprints in referrals from affiliate: ' . $identity->fullName()
                ));
                */
            }
        }
        
        // the user not yet identified 
        else 
        {
            
        }
        
    }
}