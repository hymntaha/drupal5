<?php

/**
 * @file
 * Contains \Drupal\advanced_search\Form\ResumeForm.
 */

namespace Drupal\advanced_search\Form;

use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

class AdvancedSearchForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advanced_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $default_values = $this->getQueryParams(\Drupal::request()->query->all());

    $form['#attached']['library'][] = 'advanced_search/advanced-search';

    $form['keys'] = array(
      '#type' => 'hidden',
      '#default_value' => isset($default_values['keys']) ? $default_values['keys'] : '',
    );

    $default_keyword = '';
    if(!empty($default_values['keyword'])){
      foreach(explode(',',$default_values['keyword']) as $tid){
        $default_keyword = Term::load($tid);
      }
    }

    $form['keyword_display'] = array(
      '#type' => 'entity_autocomplete',
      '#target_type' => 'taxonomy_term',
      '#selection_settings' => array(
        'target_bundles' => array('keyword'),
      ),
      '#title' => $this->t('Keyword:'),
      '#default_value' => $default_keyword,
    );

    $form['keyword'] = array(
      '#type' => 'hidden',
      '#default_value' => isset($default_values['keyword']) ? $default_values['keyword'] : '',
    );

    $form['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title Contains:'),
      '#default_value' => isset($default_values['title']) ? $default_values['title'] : '',
    );

    $form['description'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Description Contains:'),
      '#default_value' => isset($default_values['description']) ? $default_values['description'] : '',
    );

    $form['author'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Authored by:'),
      '#default_value' => isset($default_values['author']) ? $default_values['author'] : '',
    );

    $form['location'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Location:'),
      '#default_value' => isset($default_values['location']) ? $default_values['location'] : '',
    );

    $form['from'] = array(
      '#type' => 'select',
      '#title' => $this->t('From:'),
      '#options' => $this->getFromDateOptions(),
      '#default_value' => isset($default_values['from']) ? $default_values['from'] : '',
    );

    $form['to'] = array(
      '#type' => 'select',
      '#title' => $this->t('To:'),
      '#options' => $this->getToDateOptions(),
      '#default_value' => isset($default_values['to']) ? $default_values['to'] : '',
    );

    $form['collection'] = array(
      '#type' => 'select',
      '#title' => $this->t('In Collection:'),
      '#options' => $this->getCollectionOptions(),
      '#default_value' => isset($default_values['collection']) ? $default_values['collection'] : '',
    );

    $form['group'] = array(
      '#type' => 'select',
      '#title' => $this->t('In Group:'),
      '#options' => $this->getGroupOptions(),
      '#default_value' => isset($default_values['group']) ? $default_values['group'] : ''
    );

    $form['media'] = array(
      '#type' => 'select',
      '#title' => $this->t('Media'),
      '#options' => $this->getMediaOptions(),
      '#default_value' => isset($default_values['media']) ? $default_values['media'] : '',
    );

    $form['ipp'] = array(
      '#type' => 'hidden',
      '#default_value' => isset($default_values['ipp']) ? $default_values['ipp'] : '',
    );

    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      '#button_type' => 'primary',
    );

    if(\Drupal::routeMatch()->getRouteName() == 'advanced.search.search_result_controller_result_page'){
      $form['actions']['submit']['#value'] = $this->t('Refine Results');
    }

    $form['#cache']['contexts'][] = 'url.query_args';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirectUrl(Url::fromRoute('advanced.search.search_result_controller_result_page', [], [
      'query' => $this->getQueryParams($form_state->getValues()),
    ]));
  }

  private function getCollectionOptions() {
    $option[] = '';
    $terms = \Drupal::service('entity_type.manager')
      ->getStorage("taxonomy_term")
      ->loadTree('collections', 0, 3, FALSE);

    /** @var \Drupal\taxonomy\Entity\Term $term */
    foreach ($terms as $term) {
      $key = $term->tid;
      $name = $term->name;

      for ($i = 0; $i < $term->depth; $i++) {
        $name = '-' . $name;
      }

      $option[$key] = $name;
    }

    return $option;
  }

  private function getGroupOptions(){
    $option[] = '';
    $terms = \Drupal::service('entity_type.manager')
      ->getStorage("taxonomy_term")
      ->loadTree('groups');

    /** @var \Drupal\taxonomy\Entity\Term $term */
    foreach ($terms as $term) {
      $option[$term->tid] = $term->name;
    }

    return $option;
  }

  private function getFromDateOptions() {
    $options[] = '';
    for ($i = 1920; $i <= date('Y'); $i++) {
      $options[$i] = $i;
    }

    return $options;
  }

  private function getToDateOptions() {
    $options[] = '';
    for ($i = date('Y'); $i >= 1920; $i--) {
      $options[$i] = $i;
    }

    return $options;
  }

  private function getMediaOptions() {
    return [
      '' => $this->t('All'),
      'audio' => $this->t('Audio'),
      'text' => $this->t('Document'),
      'photo' => $this->t('Photo'),
      'video' => $this->t('Video'),
    ];
  }

  private function getQueryParams($values){
    $query = [];

    foreach ($values as $key => $value) {
      if (in_array($key, [
        'keyword',
        'title',
        'description',
        'author',
        'location',
        'from',
        'to',
        'collection',
        'group',
        'media',
        'ipp',
        'keys',
      ])) {
        if (!empty($value)) {
          if($key == 'keyword' && !is_numeric($value)){
            $value = EntityAutocomplete::extractEntityIdFromAutocompleteInput($value);
          }
          $query[$key] = $value;
        }
      }
    }

    return $query;
  }
}