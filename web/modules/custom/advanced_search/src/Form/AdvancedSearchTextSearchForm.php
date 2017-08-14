<?php

namespace Drupal\advanced_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class AdvancedSearchTextSearchForm.
 *
 * @package Drupal\advanced_search\Form
 */
class AdvancedSearchTextSearchForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advanced_search_text_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $default_values = $this->getQueryParams(\Drupal::request()->query->all());

    $form['keys'] = [
      '#type' => 'search',
      '#title' => $this->t('Search for'),
      '#title_display' => 'invisible',
      '#placeholder' => $this->t('New search for...'),
      '#default_value' => isset($default_values['keys']) ? $default_values['keys'] : '',
      '#required' => TRUE,
    ];

    $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Search the Archive'),
    ];

    return $form;
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirectUrl(Url::fromRoute('advanced.search.search_result_controller_result_page', [], [
      'query' => $this->getQueryParams($form_state->getValues()),
    ]));
  }

  private function getQueryParams($values){
    $query = [];

    foreach ($values as $key => $value) {
      if (in_array($key, [
        'keys',
      ])) {
        if (!empty($value)) {
          $query[$key] = $value;
        }
      }
    }

    return $query;
  }

}
