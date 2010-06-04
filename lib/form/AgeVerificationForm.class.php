<?php
/**
 * Form for entering an i18n date of birth. Requires 18 years default for countries not in app.yml
 */
class AgeVerificationForm extends BaseForm
{
    protected $country_codes;
    
    /**
     * 
     */
    public function setup()
    {
        // output  remember me checkbox if option set
        if($this->getOption('remember_me'))
        {
            $this->setWidget('remember_me', new sfWidgetFormInputCheckbox());
            $this->setValidator('remember_me', new sfValidatorBoolean(array('required' => false)));
        }
        
        $this->setWidget('country_code', new sfWidgetFormI18nChoiceCountry(array(
          'culture'   => $this->getOption('culture'),
          'countries' => $this->getCountryCodes()),
          array(
            'class'   => 'replace-me'
          )
        ));
        $this->setWidget('date_of_birth', new sfWidgetFormInputDate(array('format' => '%day%%month%%year%')));
        
        $this->setValidator('date_of_birth', new sfValidatorDate(
                array(
                    'with_time'    => false,
                    'required'     => true,
                    'date_format'  => '~(?P<day>\d{1,2})/(?P<month>\d{1,2})/(?P<year>\d{4})~',
                    'date_output'  => 'd-m-Y',
                ),
                array(
                    'required'   => 'Please enter your date of birth',
                    'invalid'    => 'Invalid date - Please use the format DD MM YYYY (e.g. 31 2 1998)',
                    'bad_format' => 'Please use the format DD MM YYYY (e.g. 31 2 1998)',
                )
        ));
        
        $this->setValidator('country_code', new sfValidatorI18nChoiceCountry(array('required' => true, 'countries' => $this->getCountryCodes())));
        $this->setDefault('country_code', isset($_SERVER['GEOIP_COUNTRY_CODE']) ? $_SERVER['GEOIP_COUNTRY_CODE'] : 'CH');
        
        // check user is old enough. Could've used the min_date on the sfValidatorDate validator,
        // but couldn't figure out how to create an accurate timestamp.
        $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkAge'))));
        
        $this->getWidgetSchema()->setLabels(array(
            'country_code'  => 'Country',
            'date_of_birth' => 'Date of Birth',
        ));
        
        $this->getWidgetSchema()->setNameFormat('age[%s]');
    }
    
    /**
     * Takes leap years into account, and works for people born before 1/1/1970.
     */
    public function checkAge($validator, $values)
    {
        $required_age = $this->getRequiredAgeForCountry($values['country_code']);

        if(!$required_age)
        {
            $error = new sfValidatorError($validator, 'country_denied');
            throw new sfValidatorErrorSchema($validator, array('country_code' => $error));
        }
        
        $date = $values['date_of_birth'];
        $date = is_string($date) ? strtotime($date) : $date;            
        
        $seconds_since     = time() - $date;
        $seconds_in_a_year = 31556926;

        $years_since = floor($seconds_since / $seconds_in_a_year);

        if($required_age > $years_since)
        {
            $error = new sfValidatorError($validator, 'age');
            throw new sfValidatorErrorSchema($validator, array('date_of_birth' => $error));
        }

        return $values;
    }  
    
    public function doBind(array $values)
    {
        $dob = $values['date_of_birth'];
        $values['date_of_birth'] = sprintf('%s/%s/%s', $dob['day'], $dob['month'], $dob['year']);
        parent::doBind($values);
    }

    /**
     * @return array
     */
    public function getCountryCodes()
    {
        if(!$this->country_codes)
        {
            $this->country_codes = array_keys($this->getCountriesConfig());
        }

        return $this->country_codes;
    }

    /**
     * Load country config from countries.yml
     *
     * @return array
     */
    protected function getCountriesConfig()
    {
        $country_config = include(sfContext::getInstance()->getConfigCache()->checkConfig('config/countries.yml'));
        return $country_config['countries'];
    }
    
    /**
     * Fetch required age (or false) for a specific country.
     *
     * @param string $iso_code
     * @return mixed integer or boolean false
     */
    protected function getRequiredAgeForCountry($iso_code)
    {
        $config = $this->getCountriesConfig();
        
        if(!isset($config[$iso_code]))
        {
            throw new InvalidArgumentException("$iso_code doesn't appear to be included in countries list");
        }
        
        return $config[$iso_code]['age'];
    }
}
?>