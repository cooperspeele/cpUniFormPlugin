<?php
class ufWidgetFormSelectRadio extends sfWidgetFormSelectRadio {

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:         An array of possible choices (required)
   *  * label_separator: The separator to use between the input radio and the label
   *  * separator:       The separator to use between each input radio
   *  * class:           The class to use for the main <ul> tag
   *  * formatter:       A callable to call to format the radio choices
   *                     The formatter callable receives the widget and the array of inputs as arguments
   *  * template:        The template to use when grouping option in groups (%group% %options%)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *   
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);

    $this->addOption('alternate', false);
    $this->addOption('css_class', null);
    $this->addOption('raw', false);
  }

  protected function formatChoices($name, $value, $choices, $attributes) {
    $inputs = array();
    foreach ($choices as $key => $option) {
      $baseAttributes = array(
        'name'  => substr($name, 0, -2),
        'type'  => 'radio',
        'value' => self::escapeOnce($key),
        'id'    => $id = $this->generateId($name, self::escapeOnce($key)),
      );

      if (strval($key) == strval($value === false ? 0 : $value)) {
        $baseAttributes['checked'] = 'checked';
      }

      $input = $this->renderTag('input', array_merge($baseAttributes, $attributes));
      $label = $this->renderContentTag('span', $this->getOption('raw') ? $option : self::escapeOnce($option), array('class' => 'label'));
      $inputs[] = $this->renderContentTag('label', $input . $label, array('for' => $id));
    }

    return call_user_func($this->getOption('formatter'), $this, $inputs);
  }  
  
  public function formatter($widget, $inputs) {
    $rows = array();
    foreach ($inputs as $input) {
      $rows[] = $this->renderContentTag('li', $input);
    }

    $attributes = array();
    $class = array();
    if ($this->getOption('alternate')) { $class[] = 'alternate'; }
    if ($cls = $this->getOption('css_class')) { $class[] = $cls; }  
    if (!empty($class)) { $attributes['class'] = implode(' ', $class); }
    
    return $this->renderContentTag('ul', 
                                   implode($this->getOption('separator'), $rows), 
                                   $attributes);
  }

}