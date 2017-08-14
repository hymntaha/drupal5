<?php

namespace Drupal\migrate_standrews\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateException;
use Drupal\Component\Utility\Unicode;

/**
 * @MigrateProcessPlugin(
 *   id = "strtotime"
 * )
 */
class Strtotime extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return strtotime($value);
  }

}
