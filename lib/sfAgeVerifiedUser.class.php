<?php
/**
 * Requires sf(Doctrine)GuardPlugin to be installed and activated,
 * as a number of methods on sfGuardSecurityUser are used by the sfAgeVerificationPlugin.
 */
class sfAgeVerifiedUser extends sfGuardSecurityUser
{
    /**
     * Set a user as verified (for when they've passed the age test).
     */
    public function verify()
    {
        $this->setAttribute('age_verified', true, 'sf_age_verification');
    }
    
    /**
     * Has a user been verified as old enough? 
     * 
     * @return boolean
     */
    public function isVerified()
    {
        return $this->hasAttribute('age_verified', 'sf_age_verification');
    }
    
    /**
     * Is this the user's first visit? 
     *
     * @param boolean $boolean
     * @return void
     */
    public function isFirstRequest($boolean = null)
    {
        if(is_null($boolean))
        {
            return $this->getAttribute('first_request', true, 'sf_age_verification');
        }
        
        $this->setAttribute('first_request', $boolean, 'sf_age_verification');
    }    
}
?>