<?php

namespace Drupal\advanced_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'AdvancedSearchTextSearchBlock' block.
 *
 * @Block(
 *  id = "advanced_search_text_search_block",
 *  admin_label = @Translation("Advanced search text search block"),
 * )
 */
class AdvancedSearchTextSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['advanced_search_text_search_block'] = \Drupal::formBuilder()->getForm('Drupal\advanced_search\Form\AdvancedSearchTextSearchForm');

    return $build;
  }

}
