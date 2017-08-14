<?php

namespace Drupal\identify_submission\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\identify_submission\ViewSubmissionQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use \Drupal\Core\Link;
/**
 * Class ViewSubmissionController.
 *
 * @package Drupal\identify_submission\Controller
 */
class ViewSubmissionController extends ControllerBase {

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
   * Viewsubmissionpage.
   *
   * @return string
   *   Return Hello string.
   */
  public function viewSubmissionPage($node_id) {

    $nid = $node_id;
    $this->viewSubmission->setNodeId($nid);
    $rows = [];
    $query_rows = $this->viewSubmission->viewSubmissionQuery();

    $header = array(
      'name' => $this->t('Name'),
      'email' => $this->t('Email'),
      'date' => $this->t('Date'),
      'info' => $this->t('Info'),
    );

    foreach ( $query_rows as $key => $query_row ) {
      $name = $query_row->name;
      $email = $query_row->email;
      $timestamp = $query_row->date;
      $date =  date("F j, Y, g:i a", $timestamp);
      $info = $query_row->info;

      $url =  Url::fromUri('internal:/admin/content/identify/submission/'.$node_id);
      $link = Link::fromTextAndUrl($info, $url);
      $rows[] = [
        'data'=> [
          $name,
          $email,
          $date,
          $link,
        ],
      ];
    }

    $table = array(
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    );

    return $table;
  }

}
