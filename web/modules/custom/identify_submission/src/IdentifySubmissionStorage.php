<?php

namespace Drupal\identify_submission;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Database\Connection;
use Drupal\node\Entity\Node;

/**
 * Class IdentifySubmissionStorage.
 *
 * @package Drupal\identify_submission
 */
class IdentifySubmissionStorage {
  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;
  protected $connection;
  private $entity_id;

  public function __construct(QueryFactory $entityQuery, Connection $connection) {
    $this->entityQuery = $entityQuery;
    $this->connection = $connection;
    $this->entity_id = '';
  }

  /**
   * @return mixed
   */
  public function getEntityId() {
    return $this->entity_id;
  }

  /**
   * @param mixed $entity_id
   */
  public function setEntityId($entity_id) {
    $this->entity_id = $entity_id;
  }

  public function identifiedItemsList($header = []){
    $query = $this->connection->select('node', 'n');

    if(!empty($header)){
      $query->extend('Drupal\Core\Database\Query\TableSortExtender')
        ->orderByHeader($header);
    }

    $query->leftJoin('submissions', 'sub', 'sub.node_id = n.nid');
    $query->innerJoin('node__field_identified', 'i', 'i.entity_id = n.nid');
    $query->innerJoin('node_field_data', 'nfd', 'nfd.nid = n.nid');

    $query->condition('i.field_identified_value', 0);

    $query->groupBy('nfd.nid');
    $query->groupBy('nfd.title');
    $query->addExpression('COUNT(sub.node_id)', 'num_submissions');

    $query->fields('nfd', ['nid','title']);

    return $query->execute();
  }

}
