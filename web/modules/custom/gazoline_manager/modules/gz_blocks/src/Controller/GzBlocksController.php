<?php

namespace Drupal\gz_blocks\Controller;

use Drupal\Core\Controller\ControllerBase;

class GzBlocksController extends ControllerBase
{

  /**
   * Retourne un paramètre de configuration
   * spécifique ou toute la configuration.
   *
   * @param null|string $field
   *
   * @return string|\stdClass
   */
  public static function get_config($field = NULL)
  {
    $config = NULL;

    if (!is_null($field)) {
      $config = \Drupal::config('gz_blocks.settings')
        ->get('gz_blocks.' . $field);
    } else {
      $config = [];

    }

    return (object)$config;
  }
}