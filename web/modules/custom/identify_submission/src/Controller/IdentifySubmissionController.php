<?php

namespace Drupal\identify_submission\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Class IdentifySubmissionController.
 *
 * @package Drupal\identify_submission\Controller
 */
class IdentifySubmissionController extends ControllerBase {

  public function nodeFields($node_id) {
    $node = Node::load($node_id);

    $build = [
      '#theme' => 'identify_submission',
      '#item' =>  node_view($node, 'identify'),
      '#form' => $this->getSubmissionForm(),
    ];

    return $build;
  }

  private function getSubmissionForm() {
    return \Drupal::formBuilder()->getForm('Drupal\identify_submission\Form\IdentifySubmissionForm');
  }

}
