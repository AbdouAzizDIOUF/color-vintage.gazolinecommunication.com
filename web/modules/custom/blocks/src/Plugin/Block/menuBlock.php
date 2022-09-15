<?php
/**
 * @file
 * Contains \Drupal\blocks\Plugin\Block\.
 */

namespace Drupal\blocks\Plugin\Block;

use Drupal;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\user\Entity\User;

/**
 * Provides a 'menu' Block.
 *
 * @Block(
 *   id = "menuBlock",
 *   admin_label = @Translation("menu Block"),
 *   category = @Translation("menu Block")
 * )
 */
class menuBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];

    $menu_menu = [];
    $menu = Drupal::menuTree()->load('main', new MenuTreeParameters());
    $menu = Drupal::menuTree()->transform($menu, $manipulators);
    $this->generate_submenu_tree($menu_menu, $menu);


    $user = User::load(Drupal::currentUser()->id());
    $username = $user->name->value ?? '';

      $panier = Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'panier', 'field_client' => Drupal::currentUser()->id()]);
      if ($panier) {
          $panier = reset($panier);
          $nbKitsPanier = count($panier->field_mes_kits->getValue()) ?? 0;
      }

    return [
      '#theme' => 'menuBlock',
      '#menu' => $menu_menu,
      '#username' => $username,
      '#nbkitsPanier' => $nbKitsPanier ?? 0,
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
                $fa = (isset($options['fa_icon'])) ? $options['fa_icon'] : NULL;

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
