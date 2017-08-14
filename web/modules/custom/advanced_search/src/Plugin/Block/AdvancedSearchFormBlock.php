<?php

namespace Drupal\advanced_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'AdvancedSearchFormBlock' block.
 *
 * @Block(
 *  id = "advanced_search_form_block",
 *  admin_label = @Translation("Advanced search form block"),
 * )
 */
class AdvancedSearchFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['advanced_search_form_block'] = \Drupal::formBuilder()->getForm('Drupal\advanced_search\Form\AdvancedSearchForm');

    return $build;
  }

}
