<?php

namespace Drupal\advanced_search;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\node\NodeInterface;
use Drupal\search_api\Entity\Index;

class AdvancedSearchQuery {
  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  private $text_search;
  private $keywords;
  private $title;
  private $description;
  private $author;
  private $location;
  private $from;
  private $to;
  private $collection;
  private $media;
  private $limit;
  private $paginate;
  private $count;

  public function __construct(QueryFactory $entityQuery, Connection $connection) {
    $this->entityQuery = $entityQuery;
    $this->connection = $connection;
    $this->keywords    = [];
    $this->title       = '';
    $this->description = '';
    $this->author      = '';
    $this->location    = '';
    $this->from        = '';
    $this->to          = '';
    $this->collection  = [];
    $this->group       = [];
    $this->media       = '';
    $this->limit       = 4;
    $this->paginate    = FALSE;
    $this->count       = FALSE;
  }

  /**
   * @return mixed
   */
  public function getTextSearch() {
    return $this->text_search;
  }

  /**
   * @param mixed $text_search
   */
  public function setTextSearch($text_search) {
    $this->text_search = $text_search;
  }

  /**
   * @param mixed $keywords
   */
  public function setKeywords($keywords) {
    if(!empty($keywords)){
      $keywords = explode(',', $keywords);
    }

    $this->keywords = $keywords;
  }

  /**
   * @param mixed $title
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * @param mixed $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * @param mixed $author
   */
  public function setAuthor($author) {
    $this->author = $author;
  }

  /**
   * @param mixed $location
   */
  public function setLocation($location) {
    $this->location = $location;
  }

  /**
   * @param mixed $from
   */
  public function setFrom($from) {
    $this->from = $from;
  }

  /**
   * @param mixed $to
   */
  public function setTo($to) {
    $this->to = $to;
  }

  /**
   * @param mixed $collection
   */
  public function setCollection($collection) {
    if (empty($collection) || $collection == '0') {
      $this->collection = [];
    }
    else{
      $collection_ids = [$collection];
      $collection_terms = \Drupal::service('entity_type.manager')->getStorage("taxonomy_term")->loadChildren($collection);

      /** @var \Drupal\taxonomy\Entity\Term $term */
      foreach ($collection_terms as $term) {
        $collection_ids [] = $term->id();
      }

      $this->collection = $collection_ids;
    }
  }

  /**
   * @return array
   */
  public function getGroup() {
    return $this->group;
  }

  /**
   * @param array $group
   */
  public function setGroup($group) {
    if (empty($group) || $group == '0') {
      $this->group = [];
    }
    else{
      $this->group = [$group];
    }
  }

  /**
   * @param mixed $media
   */
  public function setMedia($media) {
    if($media == 'all'){
      $media = '';
    }

    $this->media = $media;
  }

  /**
   * @return mixed
   */
  public function getKeywords() {
    return $this->keywords;
  }

  /**
   * @return mixed
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @return mixed
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @return mixed
   */
  public function getAuthor() {
    return $this->author;
  }

  /**
   * @return mixed
   */
  public function getLocation() {
    return $this->location;
  }

  /**
   * @return mixed
   */
  public function getFrom() {
    return $this->from;
  }

  /**
   * @return mixed
   */
  public function getTo() {
    return $this->to;
  }

  /**
   * @return mixed
   */
  public function getCollection() {
    return $this->collection;
  }

  /**
   * @return mixed
   */
  public function getMedia() {
    return $this->media;
  }

  /**
   * @return int
   */
  public function getLimit() {
    return $this->limit;
  }

  /**
   * @param int $limit
   */
  public function setLimit($limit) {
    $this->limit = $limit;
  }

  /**
   * @return bool
   */
  public function isPaginate() {
    return $this->paginate;
  }

  /**
   * @param bool $paginate
   */
  public function setPaginate($paginate) {
    $this->paginate = $paginate;
  }

  /**
   * @return bool
   */
  public function isCount() {
    return $this->count;
  }

  /**
   * @param bool $count
   */
  public function setCount($count) {
    $this->count = $count;
  }

  /**
   * @return array|int
   */
  public function getResults() {

    $query = $this->entityQuery->get('node');
    $query->condition('type', 'item')
          ->condition('status', NodeInterface::PUBLISHED);

    if(!empty($this->getTextSearch())){
      $text_search_results = [];

      $textSearchQuery = Index::load('default_index')->query();
      $textSearchQuery->keys($this->getTextSearch());

      $results = $textSearchQuery->execute();

      if($results->getResultCount() == 0){
        return [];
      }

      foreach($results->getResultItems() as $result){
        $text_search_results[] = $result->getOriginalObject()->get('nid')->getString();
      }

      $query->condition('nid', $text_search_results, 'IN');
    }

    if (!empty($this->getKeywords())) {
      $query->condition('field_keywords.target_id', $this->getKeywords(), 'IN');
    }

    if (!empty($this->getTitle())) {
      $query->condition('title', '%'.$this->getTitle().'%' ,'LIKE');
    }

    if (!empty($this->getDescription())) {
      $query->condition('field_physical_description.value', '%'.$this->getDescription().'%' ,'LIKE');
    }

    if (!empty($this->getAuthor())) {
      $query->condition('field_author_photographer.value', '%'.$this->getAuthor().'%' ,'LIKE');
    }

    if (!empty($this->getLocation())) {
      $query->condition('field_location.value', '%'.$this->getLocation().'%' ,'LIKE');
    }

    if (!empty($this->getFrom())){
      $query->condition('field_date_year.value',  $this->getFrom(), '>=');
    }

    if (!empty($this->getTo())){
      $query->condition('field_date_year.value',  $this->getTo(), '<=');
    }

    if (!empty($this->getCollection())){
      $query->condition('field_collection.target_id', $this->getCollection(),'IN');
    }

    if (!empty($this->getGroup())){
      $query->condition('field_groups.target_id', $this->getGroup(),'IN');
    }

    if (!empty($this->getMedia())) {
      $query->condition('field_media_type.value', $this->getMedia());
    }

    $query->sort('field_date_year.value', 'DESC');

    if($this->isCount()){
      $query->count();
    }
    else{
      if($this->isPaginate() && $this->getLimit()){
        $query->pager($this->getLimit());
      }
      else{
        if($this->getLimit()){
          $query->range(0, $this->getLimit());
        }
      }
    }

    return $query->execute();
  }

  public static function getMediaTypes(){
    return [
      'photo' => 'Photos',
      'audio' => 'Audio Recordings',
      'text' => 'Documents',
      'video' => 'Videos',
    ];
  }
}