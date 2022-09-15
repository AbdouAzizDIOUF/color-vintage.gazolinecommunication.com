<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */

namespace Drupal\blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\user\Entity\User;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\taxonomy\Entity\Term;


/**
 * Provides a 'menu' Block.
 *
 * @Block(
 *   id = "kitsBlock",
 *   admin_label = @Translation("kits Block"),
 *   category = @Translation("kits Block")
 * )
 */
class kitsBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build()
    {

       
        $kits = \Drupal::service('kits_module.service')->getKits();

        $categories = \Drupal::service('kits_module.service')->getCategoriesKits();

        return [
            '#theme' => 'kitsBlock',
            '#kits' => $kits,
            '#categories' => $categories
        ];
    }
}
