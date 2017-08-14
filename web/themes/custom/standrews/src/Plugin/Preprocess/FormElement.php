<?php

namespace Drupal\standrews\Plugin\Preprocess;
use Drupal\bootstrap\Plugin\Preprocess\PreprocessBase;
use Drupal\bootstrap\Plugin\Preprocess\PreprocessInterface;
use Drupal\bootstrap\Utility\Element;
use Drupal\bootstrap\Utility\Variables;

/**
 * Pre-processes variables for the "form_element" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("form_element")
 */
class FormElement extends PreprocessBase implements PreprocessInterface {
  protected function preprocessElement(Element $element, Variables $variables) {
    parent::preprocessElement($element, $variables);

    $variables['is_form_group'] = $element->getProperty('form_group', !$variables['is_checkbox'] && !$variables['is_radio'] && !$element->isType(['hidden']));
  }

}