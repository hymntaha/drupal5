<?php

namespace Drupal\homepage\Controller;

use Drupal\block\Entity\Block;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HomepageController.
 *
 * @package Drupal\homepage\Controller
 */
class HomepageController extends ControllerBase {
  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;

  /**
   * HomepageController constructor.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Drupal\Core\Entity\Query\QueryFactory $entityQuery
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, QueryFactory $entityQuery) {
    $this->entityTypeManager = $entityTypeManager;
    $this->entityQuery = $entityQuery;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity.query')
    );

  }

  public function home() {
    return [
      '#theme' => 'homepage',
      '#welcome' => $this->entityTypeManager->getViewBuilder('block')->view(Block::load('homewelcome')),
      '#timeline' => \Drupal::formBuilder()->getForm('Drupal\homepage\Form\HomepageTimelineForm'),
      '#collections' => [
        $this->entityTypeManager->getViewBuilder('block')->view(Block::load('homepagecollectionblock')),
        $this->entityTypeManager->getViewBuilder('block')->view(Block::load('homecollections')),
      ],
      '#identify' => $this->getRandomIdentifyItems(),
      '#featured' => $this->entityTypeManager->getViewBuilder('block')->view(Block::load('views_block__featured_item_block_1')),
      '#new' => $this->entityTypeManager->getViewBuilder('block')->view(Block::load('views_block__new_to_the_archive_block_1')),
    ];

  }

  private function getRandomIdentifyItems(){
    $build = [
      '#cache' => [
          'max-age' => 0,
        ],
      'items' => [],
    ];

    $query = $this->entityQuery->get('node')
      ->condition('type', 'item')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('field_media_type.value', 'photo')
      ->condition('field_identified.value', 0);

    $results = $query->execute();

    if(!empty($results)){
      $random_results = array_rand($results, 10);

      foreach ($random_results as $delta) {
        $node = Node::load($results[$delta]);
        $build['items'][] = node_view($node, 'carousel');
      }
    }

    return $build;
  }

}
