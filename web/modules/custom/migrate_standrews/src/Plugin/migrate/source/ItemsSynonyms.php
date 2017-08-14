<?php

namespace Drupal\migrate_standrews\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * @MigrateSource(
 *   id = "items_synonyms"
 * )
 */
class ItemsSynonyms extends SqlBase {
  public function query() {
    return $this->select('keyword_synonym', 's')
      ->fields('s', ['id', 'txt'])
      ->orderBy('id');
  }

  public function fields() {
    return [
      'txt' => 'Synonym Name',
    ];
  }

  public function getIds() {
    return [
      'id' => ['type' => 'integer'],
    ];
  }

}