<?php
class BasesfAgeVerificationActions extends sfActions
{
    /**
     * Show age verification form, process and verify the user
     * if submitted and valid.
     */
    public function executeVerify(sfWebRequest $request)
    {
        $form = new AgeVerificationForm(null, array(
            'culture' => $this->getUser()->getCulture(),
        ));
        
        if($request->isMethod('post'))
        {
            // we'll need this data if the form isn't valid
            $data = $request->getParameter($form->getName());
            $form->bind($data);
            
            if($form->isValid())
            {
                $this->getUser()->verify();
                $this->getUser()->setAttribute('country_code', $data['country_code']);
                $this->redirect($this->getUser()->getReferer('@homepage'));
            }
            else
            {
                foreach($form->getErrorSchema()->getErrors() as $e)
                {
                    if(in_array($e->getCode(), array('age', 'country_denied')))
                    {
                        $this->redirect('@sf_age_denied?country_code=' . $data['country_code']);
                    }
                }
            }
        }
        
        $this->setVar('form', $form);
        return sfView::SUCCESS;
    }    
    
    /**
     * 
     */
    public function executeUnverify(sfWebRequest $request)
    {
        $this->getUser()->getAttributeHolder()->remove('age_verified', null, 'sf_age_verification');
        $this->redirect($request->getReferer());
    }
}
?>