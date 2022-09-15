<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */

namespace Drupal\blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;

/**
 * Provides a 'footer' Block.
 *
 * @Block(
 *   id = "footerBlock",
 *   admin_label = @Translation("Footer Block"),
 *   category = @Translation("Footer Block")
 * )
 */
class footerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {


    $infos = [];

    $infos['phone1'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.phone1');
    $infos['address1'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.address1');
    $infos['address2'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.address2');
    $infos['address3'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.address3');
    $infos['cp'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.cp');
    $infos['city'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.city');
    $infos['socials']['facebook-square'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.facebook');
    $infos['socials']['twitter'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.twitter');
    $infos['socials']['google-plu'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.googleplus');
    $infos['socials']['linkedin'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.linkedin');
    $infos['socials']['youtube-y'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.youtube');
    $infos['socials']['viadeo-v'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.viadeo');
    $infos['socials']['instagram'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.instagram');
    $infos['socials']['pinterest-p'] = \Drupal::config('gazoline_manager.settings')
      ->get('gazoline_manager.pinterest');

    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];

    $menu_footer = [];
    $menu = \Drupal::menuTree()
      ->load('footer', new \Drupal\Core\Menu\MenuTreeParameters());
    $menu = \Drupal::menuTree()->transform($menu, $manipulators);
    foreach ($menu as $item) {
      if ($item->link->isEnabled()) {
        $title = $item->link->getTitle();
        $url = str_replace('/fr/', '/', $item->link->getUrlObject()
          ->toString());
        $has_children = $item->hasChildren;
        $menu_footer[] = ['title' => $title, 'url' => $url];
      }
    }


      $menu_sub_footer = [];
      $smenu = \Drupal::menuTree()
          ->load('sub-footer', new \Drupal\Core\Menu\MenuTreeParameters());
      $smenu = \Drupal::menuTree()->transform($smenu, $manipulators);
      foreach ($smenu as $item) {
          if ($item->link->isEnabled()) {
              $title = $item->link->getTitle();
              $url = str_replace('/fr/', '/', $item->link->getUrlObject()
                  ->toString());
              $has_children = $item->hasChildren;
              $menu_sub_footer[] = ['title' => $title, 'url' => $url];
          }
      }
      
    return [
      '#theme' => 'footerBlock',
      '#infos' => $infos,
      '#menu' => $menu_footer,
      '#submenu' => $menu_sub_footer
    ];
  }



    private function generate_submenu_tree(&$output, &$input, $parent = FALSE) {
        $input = array_values($input);
        foreach ($input as $key => $item) {
            if ($item->link->isEnabled()) {
                $name = $item->link->getTitle();
                $url = $item->link->getUrlObject();
                $url_string = $url->toString();
                $options = $item->link->getOptions();
                $fa = $options['fa_icon'] ?? NULL;

                if ($parent === FALSE) {
                    $output[$key] = [
                        'name' => $name,
                        'url' => $url_string,
                        'obf' => base64_encode($url_string),
                        'fa' => $fa,
                    ];
                } else {
                    $output['child'][$key] = [
                        'name' => $name,
                        'url' => $url_string,
                        'obf' => base64_encode($url_string),
                        'fa' => $fa,
                    ];
                }

                if ($item->hasChildren) {
                    if ($item->depth == 1) {
                        self::generate_submenu_tree($output[$key], $item->subtree, $key);
                    } else {
                        self::generate_submenu_tree($output['child'][$key], $item->subtree, $key);
                    }
                }
            }
        }
    }
}
