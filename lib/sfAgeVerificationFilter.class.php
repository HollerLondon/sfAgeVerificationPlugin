<?php
class sfAgeVerificationFilter extends sfFilter
{
    /**
     * 
     */
    public function execute($filterChain)
    {
        if($this->isFirstCall())
        {
            if(!$this->isRobot())
            {
                // if user has not verified their age, send them to 
                // age verification before allowing them past.
                if(!$this->getContext()->getUser()->isVerified() && !$this->ignoreCurrentRoute())
                {
                    // set the referer once and once only (so invalid form page refreshes
                    // do not interfere).
                    $this->getContext()->getUser()->setReferer($this->getContext()->getRouting()->getCurrentInternalUri(true));
                    $this->getContext()->getController()->redirect('@sf_age_verify');
                }
            }
        }
        
        $filterChain->execute();
    }
    
    /**
     * For some routes (namely set language) we don't want this filter to execute.
     * This method determines by route whether we should perform the verification check.
     */
    public function ignoreCurrentRoute()
    {
        $current_route = $this->getContext()->getRouting()->getCurrentInternalUri(true);
        $ignore_routes = sfConfig::get('app_sf_age_verification_ignore', array('sf_age_verify', 'sf_age_denied'));
        
        foreach($ignore_routes as $route_name)
        {
            if(strstr($current_route, '@'.$route_name))
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check whether request is from a known search engine robot.
     * 
     * @return boolean
     */
    protected function isRobot()
    {
        // fetch cached version of the agents.yml file
        $all_agents = include($this->getContext()->getConfigCache()->checkConfig('plugins/sfAgeVerificationPlugin/config/agents.yml'));
        
        // check current user-agent against list of known robot agents
        return in_array($this->getContext()->getRequest()->getHttpHeader('User-Agent'), $all_agents['strings']);   
    }
}
?>