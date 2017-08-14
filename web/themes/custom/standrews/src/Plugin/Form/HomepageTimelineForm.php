<?php

namespace Drupal\standrews\Plugin\Form;

use Drupal\bootstrap\Plugin\Form\FormBase;
use Drupal\bootstrap\Utility\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * @ingroup plugins_form
 *
 * @BootstrapForm("homepage_timeline_form")
 */
class HomepageTimelineForm extends FormBase {

  public function alterForm(array &$form, FormStateInterface $form_state, $form_id = NULL) {
    parent::alterForm($form, $form_state, $form_id);

    $form['slider']['#markup'] = '<div id="timeline-slider" class="hidden-xs"></div>';
  }

  /**
   * {@inheritdoc}
   */
  public function alterFormElement(Element $form, FormStateInterface $form_state, $form_id = NULL) {
    $form->actions->addClass('text-right');
    $form->actions->submit->setProperty('value', $this->t('See Results <i class="fa fa-chevron-right" aria-hidden="true"></i>'));
    $form->actions->submit->addClass('btn-primary');
  }

}