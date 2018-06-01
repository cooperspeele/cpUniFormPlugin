<?php
class sfWidgetFormSchemaFormatterUniForm extends cpWidgetFormSchemaFormatter {

  protected 
    $format_fields               = "%fields%",
    $format_fields_inlineLabels  = "<fieldset class=\"inlineLabels\">\n  <ol>\n%fields%\n  </ol>\n</fieldset>",
    $rowFormat                   = "<div id='__%name%' class='ctrlHolder %class%'>\n%label%\n  %field%%help%</div>\n",
    $rowInErrorFormat            = "<div id='__%name%' class='ctrlHolder %class% error'>\n  %label%\n  %field%\n%errors%\n%help%</div>\n",
    $helpFormat                  = '<p class="formHint">%help%</p>',
    $errorRowFormat              = "<div id=\"errorMsg\" class=\"alert-box error\"><p>%warning%</p>%errors%</div>\n",
    $errorListFormatInARow       = "  <ol class='errors'>\n%errors%\n  </ol>\n",
    $errorRowFormatInARow        = "    <li>%error%</li>\n",
    $namedErrorRowFormatInARow   = "    <li>%name%: %error%</li>\n",
    $decoratorFormat             = "%content%",
    $error_warning               = "Sorry, this form needs corrections.";

  protected function getFieldsetToken(sfForm $form, $name = null) {
    $cls = null;
    if ($form->getOption('inlineLabels')) { $cls = 'inlineLabels'; }
    if ($form->getOption('fieldset_class')) { 
      $cls = $cls ? $cls . ' ' : '';
      $cls .= $form->getOption('fieldset_class');
    }
    $token = '<fieldset' . ($name ? ' id="__' . $name . '"' : '') . ($cls ? ' class="' . $cls . '">' : '>');
    if ($legend = $form->getOption('legend')) {
      $token .= '<legend>' . $legend . ' </legend>';
    }
    
    return $token;
  }
  
  public function processWidget(sfWidgetForm $widget) {
    if ($widget instanceof sfWidgetFormChoice) {
      if ($widget->getOption('expanded') && !$widget->getOption('multiple')) {
        if (!$widget->getOption('renderer_class')) {
          $widget->setOption('renderer_class', 'ufWidgetFormSelectRadio');
        }
      }
    }

    else if ($widget instanceof sfWidgetFormInputFileEditable) {
      $template = '<div class="fileUpload"><div class="file">%file%</div>' .
                  '<div class="input">%input%';
      if ($widget->getOption('with_delete')) {
        $template .= '<label>%delete%' . 
                     '<span class="label">' . $this->translate($widget->getOption('delete_label')) . '</span>' .
                     '</label>';
      }
      $template .= '</div></div>';
      $widget->setOption('template', $template);
    }
    
    return $widget;
  }
  
  public function generateAttributes($widget, $name) {
    $schema = $this->getWidgetSchema();
    
    $clone = clone $widget;
    $clone->setIdFormat($schema->getOption('id_format'));
    
    $id = $clone->generateId($schema->generateName($name));
    $attributes = array('id' => $id);
    
    if ($widget->getAttribute('holder_class')) {
      $attributes['class'] = $widget->getAttribute('holder_class');
      $widget->setAttribute('holder_class', null);
    }
    
    return $attributes;
  }
  
  public function renderRow($label, $field, $errors = array(), $help = '', $hiddenFields = null, $attributes = array()) {
    unset($attributes['holder_class']);
    if (empty($errors)) {
      return strtr($this->getRowFormat(), array(
        '%name%'          => $attributes['id'],
        '%class%'         => @$attributes['class'],
        '%label%'         => $label,
        '%field%'         => $field,
        '%help%'          => $this->formatHelp($help),
        '%hidden_fields%' => is_null($hiddenFields) ? '%hidden_fields%' : $hiddenFields,
      ));
    }
    else {
      return strtr($this->getRowInErrorFormat(), array(
        '%name%'          => $attributes['id'],
        '%class%'         => @$attributes['class'],
        '%label%'         => $label,
        '%errors%'        => $this->formatErrorsForRow($errors),
        '%field%'         => $field,
        '%help%'          => $this->formatHelp($help),
        '%hidden_fields%' => is_null($hiddenFields) ? '%hidden_fields%' : $hiddenFields,
      ));
    }
  }
  
  public function generateLabel($name, $attributes = array()) {
    $schema = $this->getWidgetSchema();
    $validator = $schema->getForm()->getValidator($name);
    
    $labelName = $this->generateLabelName($name);

    if (false === $labelName) { return ''; }

    if ($validator->getOption('required')) { 
      $labelName =  '<em>*</em>' . $labelName; 
    }
     
    if (!isset($attributes['for'])) {
      $attributes['for'] = $this->widgetSchema->generateId($this->widgetSchema->generateName($name));
    }

    return $this->widgetSchema->renderContentTag('label', $labelName, $attributes);
  }
  
  public function getRowInErrorFormat() {
    return $this->rowInErrorFormat;
  }
  
  public function setRowInErrorFormat($format) {
    $this->rowInErrorFormat = $format;
  }
  
  public function formatErrorRow($errors) {
    if (null === $errors || !$errors) {
      return '';
    }

    return strtr($this->getErrorRowFormat(), 
                 array('%warning%' => $this->translate($this->getErrorWarning()),
                       '%errors%' => $this->formatErrorsForRow($this->getWidgetSchema()->getGlobalErrors($errors))));
  }
  
  public function getErrorWarning() {
    return $this->error_warning;
  }
  
  public function setErrorWarning($warning) {
    $this->error_warning = $warning;
  }
  
}
