<?php

namespace Drupal\identify_submission;

use Drupal\Core\Database\Connection;

/**
 * Class ViewSubmissionQuery.
 *
 * @package Drupal\identify_submission
 */
class ViewSubmissionQuery {


  protected $connection;
  private $node_id;

  public function __construct(Connection $connection) {
    $this->connection = $connection;
    $this->node_id = '';
  }

  /**
   * @return array
   */
  public function getNodeId() {
    return $this->node_id;
  }

  /**
   * @param array $node_id
   */
  public function setNodeId($node_id) {
    $this->node_id = $node_id;
  }

  public function viewSubmissionQuery() {

    $result = $this->connection
                   ->select('submissions','sub')
                   ->fields('sub')
                   ->condition('sub.node_id', $this->getNodeId())
                   ->execute();

    $results = $result->fetchAll(\PDO::FETCH_OBJ);

    return $results;
  }
}
