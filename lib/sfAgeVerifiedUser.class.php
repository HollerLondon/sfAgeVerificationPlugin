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
    public function verify($remember = false, $country_code = null)
    {
        // basic remember me functionality, no key set in database to check
        // just a simple cookie that we can check for.
        if($remember)
        {
            $expiration_age  = sfConfig::get('app_sf_age_verification_remember_period', 15 * 24 * 3600);
            $remember_cookie = sfConfig::get('app_sf_age_verification_remember_cookie_name', 'sfAgeVerifyRemember');
            sfContext::getInstance()->getResponse()->setCookie($remember_cookie, $country_code, time() + $expiration_age);
        }
        
        // store a country code
        if(!is_null($country_code))
        {
            $this->setAttribute('country_code', $country_code);
        }
        
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