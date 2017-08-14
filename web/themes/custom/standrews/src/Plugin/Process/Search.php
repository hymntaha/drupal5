<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Process\Search.
 */

namespace Drupal\standrews\Plugin\Process;

use Drupal\bootstrap\Plugin\Process\ProcessBase;
use Drupal\bootstrap\Plugin\Process\ProcessInterface;
use Drupal\bootstrap\Utility\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * Processes the "search" element.
 *
 * @ingroup plugins_process
 *
 * @BootstrapProcess("search")
 */
class Search extends ProcessBase implements ProcessInterface {

  /**
   * {@inheritdoc}
   */
  public static function processElement(Element $element, FormStateInterface $form_state, array &$complete_form) {

  }

}
