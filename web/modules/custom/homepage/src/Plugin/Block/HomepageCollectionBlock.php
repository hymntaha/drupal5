<?php

namespace Drupal\homepage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'HomepageCollectionBlock' block.
 *
 * @Block(
 *  id = "homepage_collection_block",
 *  admin_label = @Translation("Homepage collection block"),
 * )
 */
class HomepageCollectionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'homepage_collection',
      '#collection' => $this->buildCollectionTree(),
    );
  }

  private function buildCollectionTree($parent = 0) {
    $collections = [];

    /** @var Term $term */
    foreach(\Drupal::service('entity_type.manager')->getStorage("taxonomy_term")->loadTree('collections',$parent, 1) as $term){
      $collections[$term->tid] = [
        'name' => $term->name,
        'children' => $parent == 2 ? [] : $this->buildCollectionTree($term->tid),
      ];
    }

    return $collections;
  }
}
