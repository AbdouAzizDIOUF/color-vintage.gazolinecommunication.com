<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */

namespace Drupal\blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'calendar' Block.
 *
 * @Block(
 *   id = "calendarBlock",
 *   admin_label = @Translation("calendar Block"),
 *   category = @Translation("calendar Block")
 * )
 */
class calendarBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    return [
      '#theme' => 'calendar',
    ];
  }

}
