<?php

namespace Drupal\migrate_standrews\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "items_collections"
 * )
 */
class ItemsCollections extends SqlBase {
  public function query() {
    return $this->select('collections', 'c')
      ->fields('c', ['collection_id', 'name', 'parent'])
      ->condition('c.name', 'root', '<>')
      ->orderBy('parent', 'ASC')->orderBy('collection_id', 'ASC');
  }

  public function fields() {
    return [
      'name' => 'Collection Name',
      'parent_formatted' => 'Collection Parent',
    ];
  }

  public function getIds() {
    return [
      'collection_id' => ['type' => 'integer'],
    ];
  }

  public function prepareRow(Row $row) {
    if($row->getSourceProperty('parent') == 1){
      $row->setSourceProperty('parent_formatted', 0);
    }
    else{
      $row->setSourceProperty('parent_formatted', $row->getSourceProperty('parent'));
    }

    return parent::prepareRow($row);
  }

}