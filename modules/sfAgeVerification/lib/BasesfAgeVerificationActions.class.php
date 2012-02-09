<?php
class BasesfAgeVerificationActions extends sfActions
{
  /**
   * Show age verification form, process and verify the user
   * if submitted and valid.
   */
  public function executeVerify(sfWebRequest $request)
  {
    if($this->getUser()->isVerified())
    {
      $this->redirect('@homepage');
    }

    $form = new AgeVerificationForm(null, array(
      'culture'     => $this->getUser()->getCulture(),
      'remember_me' => sfConfig::get('app_sf_age_verification_remember_me', true)
    ));

    if ($request->isMethod('post'))
    {
      // we'll need this data if the form isn't valid
      $data = $request->getParameter($form->getName());
      $form->bind($data);

      if ($form->isValid())
      {
        $this->getUser()->verify($form->getValue('remember_me'), $data['country_code']);
        $this->redirect('@homepage');
      }
      else
      {
        foreach ($form->getErrorSchema()->getErrors() as $e)
        {
          if (in_array($e->getCode(), array('age', 'country_denied')))
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
  public function executeDenied(sfWebRequest $request)
  {
    $country_code = $request->getParameter('country_code');
    $country_config = include(sfContext::getInstance()->getConfigCache()->checkConfig('config/countries.yml'));
    
    $this->setVar('required_age', $country_config['countries'][$country_code]['age']);
    
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