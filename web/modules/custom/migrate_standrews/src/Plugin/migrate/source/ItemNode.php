<?php

namespace Drupal\migrate_standrews\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "item_node"
 * )
 */
class ItemNode extends SqlBase {
  public function query() {
    return $this->select('items', 'i')
      ->fields('i', [
        'item_id',
        'title',
        'author',
        'item_date_d',
        'item_date_m',
        'item_date_y',
        'decade',
        'location',
        'physical_desc',
        'notes',
        'media_type',
        'value',
        'language',
        'donor',
        'restricted_use',
        'featured_txt',
        'youtube',
        'filename',
        'added_by',
      ])
      ->orderBy('item_id', 'ASC');
  }

  public function fields() {
    return [
      'title' => 'Name',
      'author' => 'Author',
      'item_date_d' => 'Date (Day)',
      'item_date_m' => 'Date (Month)',
      'item_date_y' => 'Date (Year)',
      'decade' => 'Decade',
      'location' => 'Location',
      'physical_desc' => 'Physical Description',
      'notes' => 'Notes',
      'media_type' => 'Media Type',
      'value' => 'Value',
      'language' => 'Language',
      'donor' => 'Donor',
      'restricted_use' => 'Restricted Use',
      'featured_txt' => 'Featured Text',
      'youtube' => 'Youtube Link',
      'collection_ids' => 'Collection Term IDs',
      'group_ids' => 'Group Term IDs',
      'keyword_ids' => 'Keyword Term IDs',
      'related_item_ids' => 'Related Item IDs',
      'new_to_archive' => 'New',
      'featured' => 'Featured',
      'identified' => 'Identified',
      'fid' => 'File ID',
      'uid' => 'User ID',
    ];
  }

  public function getIds() {
    return [
      'item_id' => ['type' => 'integer'],
    ];
  }

  public function prepareRow(Row $row) {
    $row->setSourceProperty('collection_ids', $this->getCollectionIDs($row));
    $row->setSourceProperty('group_ids', $this->getGroupIDs($row));
    $row->setSourceProperty('keyword_ids', $this->getKeywordIDs($row));
    $row->setSourceProperty('related_item_ids', $this->getRelatedItemIDs($row));
    $row->setSourceProperty('new_to_archive', $this->isNew($row));
    $row->setSourceProperty('featured', $this->isFeatured($row));
    $row->setSourceProperty('identified', $this->isIdentified($row));

    $row->setSourceProperty('fid', NULL);
    $row->setSourceProperty('image_fid', NULL);

    if(!empty($row->getSourceProperty('filename'))){
      $pathinfo = pathinfo($row->getSourceProperty('filename'));
      switch(strtolower($pathinfo['extension'])){
        case 'jpg':
        case 'png':
          $row->setSourceProperty('image_fid', $row->getSourceProperty('item_id'));
          break;
        default:
          $row->setSourceProperty('fid', $row->getSourceProperty('item_id'));
          break;
      }
    }

    $date_fields = [
      'item_date_m',
      'item_date_d',
      'item_date_y',
    ];

    foreach($date_fields as $date_field){
      if($row->getSourceProperty($date_field) == 0){
        $row->setSourceProperty($date_field, NULL);
      }
    }

    $uid = 1;

    $user_map = [
      3 => 42,
      4 => 40,
    ];

    if(isset($user_map[$row->getSourceProperty('added_by')])){
      $uid = $user_map[$row->getSourceProperty('added_by')];
    }

    $row->setSourceProperty('uid', $uid);

    return parent::prepareRow($row);
  }

  private function getCollectionIDs(Row $row){
    $query = $this->select('item_collection', 'ic');
    $query->innerJoin('items', 'i', 'ic.item_id = i.item_id');
    $query->innerJoin('collections', 'c', 'ic.collection_id = c.collection_id');
    return $query->fields('ic', ['collection_id'])
      ->condition('ic.item_id', $row->getSourceProperty('item_id'))
      ->orderBy('ic.collection_id', 'ASC')
      ->execute()
      ->fetchCol();
  }

  private function getGroupIDs(Row $row){
    $query = $this->select('item_group', 'ig');
    $query->innerJoin('items', 'i', 'ig.item_id = i.item_id');
    $query->innerJoin('groups', 'g', 'ig.group_id = g.group_id');
    return $query->fields('ig', ['group_id'])
      ->condition('ig.item_id', $row->getSourceProperty('item_id'))
      ->orderBy('ig.group_id', 'ASC')
      ->execute()
      ->fetchCol();
  }

  private function getKeywordIDs(Row $row){
    $query = $this->select('item_keyword', 'ik');
    $query->innerJoin('items', 'i', 'ik.item_id = i.item_id');
    $query->innerJoin('keywords', 'k', 'ik.keyword_id = k.keyword_id');
    return $query->fields('ik', ['keyword_id'])
      ->condition('ik.item_id', $row->getSourceProperty('item_id'))
      ->orderBy('ik.keyword_id', 'ASC')
      ->execute()
      ->fetchCol();
  }

  private function getRelatedItemIDs(Row $row){
    $query = $this->select('item_relations', 'ir');
    $query->innerJoin('items', 'i', 'ir.item_id_2 = i.item_id');
    return $query->fields('ir', ['item_id_2'])
      ->condition('ir.item_id_1', $row->getSourceProperty('item_id'))
      ->orderBy('ir.item_id_2', 'ASC')
      ->execute()
      ->fetchCol();
  }

  private function isNew(Row $row){
    $query = $this->select('new_to_archives', 'n');
    $query->innerJoin('items', 'i', 'n.item_id = i.item_id');
    return $query->fields('n')
      ->condition('n.item_id', $row->getSourceProperty('item_id'))
      ->countQuery()
      ->execute()
      ->fetchField();
  }

  private function isFeatured(Row $row){
    $query = $this->select('featured', 'f');
    $query->innerJoin('items', 'i', 'f.item_id = i.item_id');
    return $query->fields('f')
      ->condition('f.item_id', $row->getSourceProperty('item_id'))
      ->countQuery()
      ->execute()
      ->fetchField();
  }

  private function isIdentified(Row $row){
    $query = $this->select('identify', 'idn');
    $query->innerJoin('items', 'i', 'idn.item_id = i.item_id');
    $count = $query->fields('idn', ['item_id'])
      ->condition('idn.item_id', $row->getSourceProperty('item_id'))
      ->countQuery()
      ->execute()
      ->fetchField();

    if($count > 0){
      return 0;
    }

    return 1;
  }
}