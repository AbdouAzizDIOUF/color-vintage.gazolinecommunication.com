<?php

namespace Drupal\adminimal_gazoline\Plugin\Block;

use Drupal\adminimal_gazoline\Form\AdminimalGazolineForm;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Class MenuAdmin
 *
 * @package Drupal\adminimal_gazoline\Plugin\Block
 *
 * @Block(
 *   id = "menu_admin",
 *   admin_label = "Menu admin",
 *   category = "Adminimal Gazoline"
 * )
 */
class MenuAdmin extends BlockBase
{

  public function build()
  {
    \Drupal::service('page_cache_kill_switch')->trigger();

    $html = '<ul id="MenuList" class="menu">';
    if(\Drupal::config('adminimal_gazoline.settings')->get('adminimal_gazoline.multisite_actif') && !empty(\Drupal::config('adminimal_gazoline.settings')->get('adminimal_gazoline.multisite_json'))) {
      $menus_admin = json_decode(\Drupal::config('adminimal_gazoline.settings')->get('adminimal_gazoline.multisite_json'));
      $menu = \Drupal::menuTree()->load($menus_admin->{$_SERVER['SERVER_NAME']}, new MenuTreeParameters());
    } else {
      $menu = \Drupal::menuTree()->load('menu-admin', new MenuTreeParameters());
    }
    $manipulators = [
        ['callable' => 'menu.default_tree_manipulators:checkAccess'],
        ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $menu = \Drupal::menuTree()->transform($menu, $manipulators);
    $this->generate_submenu_tree($render, $menu);
    $path = \Drupal::service('path.current')->getPath();

    if (!empty($render)) {
      foreach ($render as $item) {
        if ($item['url'] != '/') {
          $active = '';
          if ($item['url'] !== "") {
            $active = ( strpos($path, $item['url']) === 0) ? 'active' : '';
          }
          $childs = NULL;

          if (!empty($item['child'])) {
            $childs = '<div class="childs">';

            foreach ($item['child'] as $child) {
              if (empty($active)) {
                $active = (strpos($path, $child['url']) === 0) ? 'active' : '';
              }

              $child_active = (strpos($path, $child['url']) === 0) ? 'active' : '';

              $childs .= '<div class="menu-item-child ' . $child_active . '">';
              $childs .= '<a href="' . $child['url'] . '">';

              if (!is_null($child['fa'])) {
                $childs .= '<i class="fa ' . $child['fa'] . '"></i>&nbsp;';
              }

              $childs .= $child['name'];
              $childs .= '</a>';
              $childs .= '</div>';
            }

            $childs .= '</div>';
          }

          $html .= '<li class="menu-item ' . $active . '">';
          if ($item['url'] !== "") {
            $html .= '<a href="' . $item['url'] . '">';
          } else {
            $html .= '<a>';
          }

          if (!is_null($item['fa'])) {
            $html .= '<i class="fa ' . $item['fa'] . '"></i>&nbsp;';
          }

          $html .= $item['name'];
          $html .= '</a>';
          $html .= $childs;
          $html .= '</li>';
        }
      }
    }

    if (\Drupal::config('adminimal_gazoline.settings')->get('adminimal_gazoline.display')) {
      $user = \Drupal::currentUser();
      $width = (\Drupal::service('module_handler')->moduleExists('gazoline_manager') && $user->hasPermission('gazoline')) ? 33.33 : 50;
      $colors = [
          '#4b4950',
          '#3e3d43',
          '#323036',
      ];

      if (\Drupal::service('module_handler')->moduleExists('gazoline_manager') && $user->hasPermission('gazoline')) {
        $html .= '<li class="menu-item menu-item-special" style="width: ' . $width . '%; background-color: ' . $colors[0] . ';">';
        $html .= '<a href="/admin/config/system/gazoline-manager/informations" title="Configuration"><i class="fa fa-cog"></i></a>';
        $html .= '</li>';
      }

      $html .= '<li class="menu-item menu-item-special" style="width: ' . $width . '%; background-color: ' . $colors[1] . ';">';
      $html .= '<a href="/user/' . $user->id() . '/edit" title="Profil"><i class="fa fa-user"></i></a>';
      $html .= '</li>';
      $html .= '<li class="menu-item menu-item-special" style="width: ' . $width . '%; background-color: ' . $colors[2] . ';">';
      $html .= '<a href="/user/logout" title="Déconnexion"><i class="fa fa-sign-out-alt"></i></a>';
      $html .= '</li>';
    }

    $html .= '</ul>';

    $colors = [];
    $style = '';

    if ($color1 = \Drupal::config('adminimal_gazoline.settings')->get('adminimal_gazoline.color1')) {
      $colors[] = $color1;
    }

    if ($color2 = \Drupal::config('adminimal_gazoline.settings')->get('adminimal_gazoline.color2')) {
      $colors[] = $color2;
    }

    if (!empty($colors)) {
      if (isset($colors[1])) {
        $style = 'background: transparent linear-gradient(to right, ' . $colors[0] . ' 0%, ' . $colors[1] . ' 100%) repeat scroll 0 0;';
      } else {
        $style = 'background: transparent linear-gradient(to right, ' . $colors[0] . ' 0%, ' . $colors[0] . ' 100%) repeat scroll 0 0;';
      }
    }

    return [
        '#cache' => ['max-age' => 0],
        '#attached' => array(
            'library' => array(
                'adminimal_gazoline/global-styling',
            ),
        ),
        '#theme' => 'menu-admin',
        '#menu' => $html,
        '#colors' => $style,
    ];
  }

  private function generate_submenu_tree(&$output, &$input, $parent = FALSE)
  {
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
              'fa' => $fa,
          ];
        } else {
          $output['child'][$key] = [
              'name' => $name,
              'url' => $url_string,
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