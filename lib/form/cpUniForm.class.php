<?php
require_once sfConfig::get('sf_plugins_dir') . '/cpFormulatePlugin/lib/form/cpForm.class.php';

class cpUniForm extends cpForm {

  const FORMATTER_NAME = 'uniForm';
  
  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null) {
    parent::__construct($defaults, $options, $CSRFSecret);
  }
  
  public function getWidgetFormSchemaClass() {
    return 'cpUniFormWidgetFormSchema';
  } 
  
  protected function postSetup() {
    parent::postSetup();
    $catalogue = $this->getTranslationCatalogue();
    $this->getWidgetSchema()->setFormFormatterName(self::FORMATTER_NAME);
    if ($catalogue) { $this->setTranslationCatalogue($catalogue); }
  }
  
  protected function updateWidgets() {
  	
  	foreach ($this->getWidgetSchema()->getFields() as $widget) {
  		$class = get_class($widget);
  		$css = '';
  		
  		if ($widget instanceof sfWidgetFormChoice) {
  		  if ($widget->getOption('expanded') == false) {
          $css = 'selectInput';
  		  }
  		  else if ($widget->getOption('multiple') == false) {
  		    $css = 'radioInput';
  		  }
  		  else { $css = 'checkboxInput'; }
  		}
  		
  		switch ($class) {
  			case 'sfWidgetFormTextarea' :
  			case 'sfWidgetFormInputPassword' :
  			case 'sfWidgetFormInputText' :
  		  case 'sfWidgetFormInput' :
  				$css = 'textInput';
  				break;
  		}
  		
  		if ($css) { 
  		  $widget->setAttribute('class', $css. ' ' . $widget->getAttribute('class')); 
  		}
  	}
  }
}