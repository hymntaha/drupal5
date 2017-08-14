<?php

namespace Drupal\identify_submission\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\identify_submission\IdentifySubmissionStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use \Drupal\Core\Link;

/**
 * Class IdentifyAdminController.
 *
 * @package Drupal\identify_submission\Controller
 */
class IdentifyAdminController extends ControllerBase{

  protected $identifyStorage;

  public function __construct(IdentifySubmissionStorage $identify_submission) {
    $this->identifyStorage = $identify_submission;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('identify.submission')
    );
  }

  /**
   * adminPage.
   *
  */
  public function adminPage() {
    $header = array(
      'item_id' => $this->t('Item Id'),
      'item_title' => ['data' => $this->t('Item Title'), 'field' => 'title'],
      'identifications_submitted' => ['data' => $this->t('Identifications submitted'), 'field' => 'num_submissions', 'sort' => 'desc'],
      'actions' => $this->t('Actions'),
    );

    $identified_items = $this->identifyStorage->identifiedItemsList($header);
    $rows = [];

    foreach ($identified_items as $item) {
      $rows[] = [
          Link::createFromRoute($item->nid, 'entity.node.edit_form', ['node' => $item->nid]),
          Link::createFromRoute($item->title, 'entity.node.canonical', ['node' => $item->nid]),
          $item->num_submissions,
          Link::fromTextAndUrl(t('View Submissions'), Url::fromUri('internal:/admin/content/identify/item/'.$item->nid)),
        ];
    }

    $options = [];
    $form['table'] = array(
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#options'=>$options
    );

    return $form;
  }

}
