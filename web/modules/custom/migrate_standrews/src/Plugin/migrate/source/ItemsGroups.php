<?php

namespace Drupal\migrate_standrews\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * @MigrateSource(
 *   id = "items_groups"
 * )
 */
class ItemsGroups extends SqlBase {
  public function query() {
    return $this->select('groups', 'g')
      ->fields('g', ['group_id', 'name'])
      ->orderBy('group_id');
  }

  public function fields() {
    return [
      'txt' => 'Group Name',
    ];
  }

  public function getIds() {
    return [
      'group_id' => ['type' => 'integer'],
    ];
  }

}