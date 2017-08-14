<?php

namespace Drupal\identify_submission\Form;
use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
/**
 * Class IdentifySubmissionForm.
 *
 * @package Drupal\identify_submission\Form
 */
class IdentifySubmissionForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'identify_submission_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Your Name:'),
      '#required' => TRUE,
    );

    $form['email'] = array(
      '#type' => 'email',
      '#title' => $this->t('Your Email:'),
      '#required' => TRUE,
    );

    $form['info'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Information about photograph:'),
      '#maxlength' => 1000,
      '#required' => TRUE,
    );

    $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
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
    $form_state->setRedirectUrl(Url::fromRoute('identify_submission.submitted_identify_controller_displayThankYou'));

    $current_path = \Drupal::service('path.current')->getPath();
    $node_id = explode("/", $current_path);

    $conn = Database::getConnection();
    $conn->insert('submissions')->fields(
      array(
        'name' => $form_state->getValue('name'),
        'email' => $form_state->getValue('email'),
        'date' => time(),
        'info' => $form_state->getValue('info'),
        'node_id' => $node_id[2]
      )
    )->execute();
  }
}
