<?php

namespace Drupal\homepage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class HomepageTimelineForm.
 *
 * @package Drupal\homepage\Form
 */
class HomepageTimelineForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'homepage_timeline_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['date_from'] = [
      '#type' => 'number',
      '#title' => $this->t('Date From'),
      '#default_value' => 1920,
    ];

    $form['date_to'] = [
      '#type' => 'number',
      '#title' => $this->t('Date To'),
      '#default_value' => 1925,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('See Results'),
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
    $query = [];

    if($from = $form_state->getValue('date_from')){
      $query['from'] = $from;
    }

    if($to = $form_state->getValue('date_to')){
      $query['to'] = $to;
    }

    $form_state->setRedirectUrl(Url::fromRoute('advanced.search.search_result_controller_result_page', [], ['query' => $query]));
  }

}
