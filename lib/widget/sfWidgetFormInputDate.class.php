<?php
/**
 * A date widget that uses text fields for each of the d-m-Y elements.
 */
class sfWidgetFormInputDate extends sfWidgetFormDate
{
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    // convert value to an array
    $default = array('year' => '', 'month' => '', 'day' => '');

    if (is_array($value))
    {
      $value = array_merge($default, $value);
    }
    else
    {
      $value = (string) $value == (string) (integer) $value ? (integer) $value : strtotime($value);

      if (false === $value)
      {
        $value = $default;
      }
      else
      {
        $value = array('year' => date('Y', $value), 'month' => date('n', $value), 'day' => date('j', $value));
      }
    }

    $date            = array();
    $emptyValues     = $this->getOption('empty_values');

    $date['%day%']   = $this->renderDayWidget($name.'[day]', $value['day'], array(), array_merge($this->attributes, $attributes));
    $date['%month%'] = $this->renderMonthWidget($name.'[month]', $value['month'], array(), array_merge($this->attributes, $attributes));
    $date['%year%']  = $this->renderYearWidget($name.'[year]', $value['year'], array(), array_merge($this->attributes, $attributes));


    return strtr($this->getOption('format'), $date);
  }

  /**
   * @param string $name
   * @param string $value
   * @param array $options
   * @param array $attributes
   * @return string rendered widget
   */
  protected function renderDayWidget($name, $value, $options, $attributes)
  {
    $widget = new sfWidgetFormInputText($options, array_merge(array('size' => 2, 'maxlength' => 2), $attributes));
    
    return $widget->render($name, $value);
  }

  /**
   * @param string $name
   * @param string $value
   * @param array $options
   * @param array $attributes
   * @return string rendered widget
   */
  protected function renderMonthWidget($name, $value, $options, $attributes)
  {
    $widget = new sfWidgetFormInputText($options, array_merge(array('size' => 2, 'maxlength' => 2), $attributes));
    
    return $widget->render($name, $value);
  }

  /**
   * @param string $name
   * @param string $value
   * @param array $options
   * @param array $attributes
   * @return string rendered widget
   */
  protected function renderYearWidget($name, $value, $options, $attributes)
  {
    $widget = new sfWidgetFormInputText($options, array_merge(array('size' => 4, 'maxlength' => 4), $attributes));
    
    return $widget->render($name, $value);
  }
}
?>