<?php

namespace Drupal\migrate_standrews\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "items_keywords"
 * )
 */
class ItemsKeywords extends SqlBase {
  public function query() {
    return $this->select('keywords', 'k')
      ->fields('k', ['keyword_id', 'txt'])
      ->orderBy('keyword_id');
  }

  public function fields() {
    return [
      'txt' => 'Keyword Name',
      'synonym_ids' => 'Synonym IDs',
    ];
  }

  public function getIds() {
    return [
      'keyword_id' => ['type' => 'integer'],
    ];
  }

  public function prepareRow(Row $row) {
    $row->setSourceProperty('synonym_ids', $this->getSynonymIDs($row));

    return parent::prepareRow($row);
  }

  private function getSynonymIDs(Row $row){
    $query = $this->select('keyword_synonym', 'ks');
    return $query->fields('ks', ['id'])
      ->condition('ks.keyword_id', $row->getSourceProperty('keyword_id'))
      ->orderBy('ks.id', 'ASC')
      ->execute()
      ->fetchCol();
  }

}