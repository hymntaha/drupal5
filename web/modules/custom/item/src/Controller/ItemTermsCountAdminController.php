<?php

namespace Drupal\item\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\VocabularyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ItemTermsCountAdminController  extends ControllerBase {

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * @param VocabularyInterface $taxonomy_vocabulary
   * @return string
   */
  public function getTitle($taxonomy_vocabulary){
    return ucwords($taxonomy_vocabulary->id()). ' Count';
  }

  /**
   * @param VocabularyInterface $taxonomy_vocabulary
   * @return array
   */
  public function count($taxonomy_vocabulary) {
    $headers = [
      [
        'data' => $this->t('Name'),
        'field' => 't.name',
        'sort' => 'asc',
      ],
      [
        'data' => $this->t('Count'),
        'field' => 'cnt',
      ],
    ];

    $build['table'] = [
      '#type' => 'table',
      '#header' => $headers,
    ];

    $terms = $this->getTerms($taxonomy_vocabulary, $headers);

    foreach($terms as $term){
      $count = $term->cnt;
      $term = Term::load($term->tid);
      $build['table']['term:'.$term->id()] = [
        'name' => [
          '#type' => 'link',
          '#title' => $term->getName(),
          '#url' => $term->urlInfo(),
        ],
        'count' => [
          '#type' => 'link',
          '#title' => $count,
          '#url' => $term->urlInfo(),
        ],
      ];
    }

    $build['pager'] = ['#type' => 'pager'];

    return $build;
  }

  /**
   * @param VocabularyInterface $taxonomy_vocabulary
   * @param $headers
   * @return
   */
  private function getTerms($taxonomy_vocabulary, $headers){
    /** @var \Drupal\Core\Database\Query\SelectInterface $query */
    $query = $this->database->select('taxonomy_term_field_data', 't')
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->extend('Drupal\Core\Database\Query\TableSortExtender');

    $query->leftJoin('taxonomy_index', 'ti', 't.tid = ti.tid');
    $query->groupBy('t.tid')->groupBy('t.name');
    $query->addExpression('COUNT(ti.nid)', 'cnt');

    return $query
      ->fields('t', ['tid', 'name'])
      ->condition('t.vid', $taxonomy_vocabulary->id())
      ->condition('t.default_langcode', 1)
      ->orderByHeader($headers)
      ->limit(100)
      ->execute();
  }

}
