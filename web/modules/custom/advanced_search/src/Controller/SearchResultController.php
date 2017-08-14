<?php

namespace Drupal\advanced_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\advanced_search\AdvancedSearchQuery;
use Drupal\Core\Url;

/**
 * Class SearchResultController.
 *
 * @package Drupal\advanced_search\Controller
 */
class SearchResultController extends ControllerBase {

  /**
   * AdvancedSearchQuery definition.
   *
   * @var AdvancedSearchQuery
   */
  protected $advancedSearchQuery;

  /**
   * {@inheritdoc}
   */
  public function __construct(AdvancedSearchQuery $advanced_search_search_result) {
    $this->advancedSearchQuery = $advanced_search_search_result;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('advanced.search.search.query')
    );
  }

  public function result_page() {
    $this->getParameters();

    $query_params = \Drupal::request()->query->all();
    $media_types = AdvancedSearchQuery::getMediaTypes();
    $ipp = 4;

    if(isset($query_params['ipp']) && in_array($ipp, [4,8,16,32])){
      $ipp = $query_params['ipp'];
    }
    else if(!empty($this->advancedSearchQuery->getMedia())){
      $ipp = 32;
    }

    $build['#cache']['contexts'][] = 'url.query_args';

    $build['search_header'] = [
      '#theme' => 'advanced_search_header',
      '#ipp' => $ipp,
      '#search_params' => $this->getSearchParams($query_params),
    ];

    $this->advancedSearchQuery->setLimit($ipp);

    if (empty($this->advancedSearchQuery->getMedia())) {
      foreach ($media_types as $media_type => $media_string) {
        $build['results'][] = $this->getResults($media_type, $media_string, $query_params);
      }
    }
    else {
      $this->advancedSearchQuery->setPaginate(TRUE);
      $build['results'][] = $this->getResults($this->advancedSearchQuery->getMedia(), $media_types[$this->advancedSearchQuery->getMedia()], $query_params);
      $build['pager'] = ['#type' => 'pager'];
    }

    $is_empty = TRUE;
    foreach($build['results'] as $results){
      if(!empty($results)){
        $is_empty = FALSE;
      }
    }

    if($is_empty){
      $build['results'] = ['#markup' => '<div class="no-results">'.$this->t('Sorry, no results found for your query.').'</div>'];
    }

    return $build;
  }

  private function getParameters() {
    $default_params = [
      'keyword' => '',
      'title' => '',
      'description' => '',
      'author' => '',
      'location' => '',
      'from' => '',
      'to' => '',
      'collection' => '',
      'group' => '',
      'media' => '',
      'keys' => '',
    ];

    $params = array_merge($default_params, \Drupal::request()->query->all());

    $this->advancedSearchQuery->setKeywords($params['keyword']);
    $this->advancedSearchQuery->setTitle($params['title']);
    $this->advancedSearchQuery->setDescription($params['description']);
    $this->advancedSearchQuery->setAuthor($params['author']);
    $this->advancedSearchQuery->setLocation($params['location']);
    $this->advancedSearchQuery->setFrom($params['from']);
    $this->advancedSearchQuery->setTo($params['to']);
    $this->advancedSearchQuery->setCollection($params['collection']);
    $this->advancedSearchQuery->setGroup($params['group']);
    $this->advancedSearchQuery->setMedia($params['media']);
    $this->advancedSearchQuery->setTextSearch($params['keys']);
  }

  private function getResults($media_type, $media_string, $query_params) {
    $this->advancedSearchQuery->setCount(FALSE);
    $this->advancedSearchQuery->setMedia($media_type);
    $show_all_results_link = FALSE;

    if(!isset($query_params['media']) || $query_params['media'] == 'all'){
      $show_all_results_link = TRUE;
    }

    $results = $this->advancedSearchQuery->getResults();

    if(empty($results)){
      return [];
    }

    $query_params['media'] = $media_type;

    $url = Url::fromRoute('advanced.search.search_result_controller_result_page', [], ['query' => $query_params]);

    $this->advancedSearchQuery->setCount(TRUE);

    $search_result[$media_type]['title'] = $media_string;
    $search_result[$media_type]['count'] = $this->advancedSearchQuery->getResults();
    $search_result[$media_type]['route'] = $url->toString();

    /** @var \Drupal\node\Entity\Node $node */
    foreach (Node::loadMultiple($results) as $key => $node) {
      $search_result[$media_type]['results'][] = node_view($node, 'search_result');
    }

    return [
      '#theme' => 'advanced_search',
      '#search_result' => $search_result,
      '#show_all_results_link' => $show_all_results_link,
    ];
  }

  private function getSearchParams($query_params){
    $search_params = [];

    foreach($query_params as $key => $value){
      if(!in_array($key, ['keyword', 'title', 'description', 'author', 'location', 'from', 'to', 'collection', 'group', 'media' , 'keys'])){
        continue;
      }
      if($key != 'ipp'){
        switch($key){
          case 'media':
            $key = 'Media Type';
            $value = AdvancedSearchQuery::getMediaTypes()[$value];
            break;
          case 'collection':
          case 'group':
          case 'keyword':
            $key = ucwords($key);
            $name = [];
            foreach(explode(',', $value) as $tid){
              $name[] = Term::load($tid)->getName();
            }
            $value = implode(', ', $name);
            break;
          case 'keys':
            $key = 'Search';
            break;
          default:
            $key = ucwords($key);
            break;
        }
        $search_params[$key] = $value;
      }
    }

    return $search_params;
  }

}
