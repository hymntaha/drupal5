<?php

namespace Drupal\standrews\Plugin\Preprocess;
use Drupal\bootstrap\Plugin\Preprocess\PreprocessBase;
use Drupal\bootstrap\Plugin\Preprocess\PreprocessInterface;
use Drupal\bootstrap\Utility\Variables;

/**
 * Pre-processes variables for the "node" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("node")
 */
class Node extends PreprocessBase implements PreprocessInterface{
  protected function preprocessVariables(Variables $variables) {
    parent::preprocessVariables($variables);

    /** @var \Drupal\node\Entity\Node $node */
    $node = $variables['elements']['#node'];

    if($variables['elements']['#view_mode'] == 'carousel'){
      $variables['url'] = '/identify/'.$node->id();
    }
  }
}