<?php

namespace Drupal\identify_submission\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class SubmittedIdentifyController.
 *
 * @package Drupal\identify_submission\Controller
 */
class SubmittedIdentifyController extends ControllerBase {

  /**
   * Displaythankyou.
   *
   * @return string
   *   Return Hello string.
   */
  public function displayThankYou() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Thank you for submitting your information.')
    ];
  }

}
