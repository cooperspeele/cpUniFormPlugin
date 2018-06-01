<?php
class cpWidgetFormChoice extends sfWidgetFormChoice {
  
  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);
    $this->addOption('dictionary');
  }
  
}