<?php

namespace Drupal\migrate_standrews\Plugin\migrate\source;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "item_files"
 * )
 */
class ItemFiles extends SqlBase {
  public function query() {
    return $this->select('items', 'i')
      ->fields('i', ['item_id','filename'])
      ->condition('i.filename', '', '<>')
      ->orderBy('item_id');
  }

  public function fields() {
    return [
      'filename_processed' => 'Filename',
      'status' => 'Status',
      'uri' => 'Drupal URI',
    ];
  }

  public function getIds() {
    return [
      'item_id' => ['type' => 'integer'],
    ];
  }

  public function prepareRow(Row $row) {
    $row->setSourceProperty('status', 1);
    $row->setSourceProperty('uri', str_replace('files/', 'public://', $row->getSourceProperty('filename')));

    $pathinfo = pathinfo($row->getSourceProperty('filename'));
    $row->setSourceProperty('filename_processed', $pathinfo['basename']);

    return parent::prepareRow($row);
  }

}