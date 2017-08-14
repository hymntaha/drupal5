<?php

namespace Drupal\identify_submission\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\identify_submission\ViewSubmissionQuery;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SubmissionInfoController.
 *
 * @package Drupal\identify_submission\Controller
 */
class SubmissionInfoController extends ControllerBase {

  protected $viewSubmission;

  public function __construct(ViewSubmissionQuery $view_submission) {
    $this->viewSubmission = $view_submission;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('view.submission')
    );
  }

  /**
   * Infosubmission.
   *
   * @param $nid
   * @return array
   *
   */
  public function infoSubmission($node_id) {
    $this->viewSubmission->setNodeId($node_id);

    $query_rows = $this->viewSubmission->viewSubmissionQuery();
    $node = Node::load($node_id);

    $submission_fields = [
      'node_id' => $node_id,
      'title' => $node->getTitle(),
      'image' => '',
    ];

    if(isset($node->field_image[0])){
      $submission_fields['image'] = $node->field_image[0]->entity->url();
    }

    foreach ($query_rows as $query_row) {
      $submission_fields['name'] = $query_row->name;
      $submission_fields['email'] = $query_row->email;
      $submission_fields['date'] =  date("F j, Y, g:i a", $query_row->date);
      $submission_fields['info'] = $query_row->info;
    }

    $build = [
      '#theme' => 'info_view',
      '#fields' =>  $submission_fields,
    ];

    return $build;
  }

}
