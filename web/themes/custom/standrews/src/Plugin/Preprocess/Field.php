<?php

namespace Drupal\standrews\Plugin\Preprocess;

use Drupal\bootstrap\Plugin\Preprocess\PreprocessBase;
use Drupal\bootstrap\Plugin\Preprocess\PreprocessInterface;
use Drupal\bootstrap\Utility\Variables;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

/**
 * Pre-processes variables for the "field" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("field")
 */
class Field extends PreprocessBase implements PreprocessInterface {
  protected function preprocessVariables(Variables $variables) {
    parent::preprocessVariables($variables);

    switch ($variables['field_name']){
      case 'field_media_type':
        $this->preprocessFieldMediaTypeVariables($variables);
        break;
      case 'field_notes':
        $this->preprocessFieldNotesVariables($variables);
        break;
      case 'field_youtube_link':
        $this->preprocessFieldYoutubeLink($variables);
        break;
      case 'field_groups':
      case 'field_collection':
      case 'field_keywords':
        $this->preprocessFieldTaxonomyLink($variables);
        break;
    }
  }

  private function preprocessFieldMediaTypeVariables(Variables $variables){
    $icon = '';
    switch(strtolower($variables['element']['#object']->field_media_type->value)){
      case 'text':
        $icon = '<i class="fa fa-file-pdf-o" aria-hidden="true"></i> ';
        break;
      case 'photo':
        $icon = '<i class="fa fa-file-image-o" aria-hidden="true"></i> ';
        break;
      case 'video':
        $icon = '<i class="fa fa-file-video-o" aria-hidden="true"></i> ';
        break;
      case 'audio':
        $icon = '<i class="fa fa-file-audio-o" aria-hidden="true"></i> ';
        break;
    }
    $variables['items'][0]['content']['#markup'] = $icon.$variables['items'][0]['content']['#markup'];
  }

  private function preprocessFieldNotesVariables(Variables $variables){
    if($variables['element']['#view_mode'] == 'search_result' && isset($variables['items'][0]['content']['#context']['value'])){
      $summary = text_summary($variables['items'][0]['content']['#context']['value'], NULL ,250);
      if(strlen($variables['items'][0]['content']['#context']['value']) > strlen($summary)){
        $variables['items'][0]['content']['#context']['value'] = $summary.'..';
      }
    }
  }

  private function preprocessFieldYoutubeLink(Variables $variables){
    $youtube_id = explode('v=', $variables['element']['#object']->field_youtube_link->value);
    $variables['items'][0] = ['#markup' => $youtube_id[1]];
  }

  private function preprocessFieldTaxonomyLink(Variables $variables){

    switch($variables['field_name']){
      case 'field_groups':
        $query_key = 'group';
        break;
      case 'field_collection':
        $query_key = 'collection';
        break;
      default:
        $query_key = 'keyword';
        break;
    }

    foreach($variables['items'] as $delta => $item){
      $variables['items'][$delta]['content']['#url'] = Url::fromRoute('advanced.search.search_result_controller_result_page', [], [
        'query' => [
          $query_key => $variables['element']['#object']->{$variables['field_name']}[$delta]->target_id
        ],
      ]);
    }
  }
}