<?php

namespace Drupal\gazoline_manager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class GazolineManagerController extends ControllerBase
{

  /**
   * Retourne la latitude et la
   * longitude d'une adresse.
   *
   * @param array $address
   *
   * @return object
   */
  public static function get_lat_lng_from_address($address)
  {
    $address = str_replace(' ', '+', implode(',', $address));
    $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $address . '&sensor=false');

    $output = json_decode($geocode);

    $return = (object)[
      'lat' => 0,
      'lng' => 0,
    ];

    if ($output->status == 'OK') {
      $return->lat = $output->results[0]->geometry->location->lat;
      $return->lng = $output->results[0]->geometry->location->lng;
    } else {
      //drupal_set_message('error', $output->error_message);
    }

    return $return;
  }

  /**
   * Retourne un paramètre de configuration
   * spécifique ou toute la configuration.
   *
   * @param null|string $field
   *
   * @return string|array
   */
  public static function get_config($field = NULL)
  {
    $config = NULL;

    if (!is_null($field)) {
      $config = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.' . $field);
    } else {
      $config = [];

      // Téléphones.
      $config['phones']['phone1'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.phone1');
      $config['phones']['phone2'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.phone2');
      $config['phones']['fax'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.fax');

      // Emails.
      $config['mails']['mail1'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.mail1');
      $config['mails']['mail2'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.mail2');

      // Adresse.
      $config['address']['parts']['address1'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.address1');
      $config['address']['parts']['address2'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.address2');
      $config['address']['parts']['address3'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.address3');
      $config['address']['cp'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.cp');
      $config['address']['city'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.city');
      $config['address']['coords']['lat'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.lat');
      $config['address']['coords']['lng'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.lng');

      // Slogan.
      $config['slogan'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.slogan');

      // SIRET.
      $config['siret'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.siret');

      // Horaires.
      $config['horaires'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.horaires');

      // Réseaux sociaux.
      $config['socials']['facebook'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.facebook');
      $config['socials']['google-plus'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.googleplus');
      $config['socials']['twitter'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.twitter');
      $config['socials']['linkedin'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.linkedin');
      $config['socials']['youtube'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.youtube');
      $config['socials']['viadeo'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.viadeo');
      $config['socials']['instagram'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.instagram');
      $config['socials']['pinterest'] = \Drupal::config('gazoline_manager.settings')
        ->get('gazoline_manager.pinterest');
    }

    return $config;
  }

  /**
   * Retourne un menu.
   *
   * @param $output
   * @param $input
   * @param bool $parent
   */
  public static function generate_submenu_tree(&$output, &$input, $parent = FALSE)
  {
    $input = array_values($input);

    foreach ($input as $key => $item) {
      if ($item->link->isEnabled() && get_class($item->link) != 'Drupal\Core\Menu\InaccessibleMenuLink') {
        $name = $item->link->getTitle();
        $url = $item->link->getUrlObject();
        $url_string = $url->toString();

        if ($parent === FALSE) {
          $output[$key] = [
            'name' => $name,
            'url' => $url_string,
          ];
        } else {
          $output['child'][$key] = [
            'name' => $name,
            'url' => $url_string,
          ];
        }

        if ($item->hasChildren) {
          if ($item->depth == 1) {
            GazolineManagerController::generate_submenu_tree($output[$key], $item->subtree, $key);
          } else {
            GazolineManagerController::generate_submenu_tree($output['child'][$key], $item->subtree, $key);
          }
        }
      }
    }
  }

  /**
   * Retourne le code HTML
   * d'un menu.
   *
   * @param string $menu_name
   * @param null|string $type
   *
   * @return string
   */
  public static function get_menu_html($menu_name, $type = NULL)
  {
    $menu = \Drupal::menuTree()->load($menu_name, new MenuTreeParameters());
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $menu = \Drupal::menuTree()->transform($menu, $manipulators);
    GazolineManagerController::generate_submenu_tree($render, $menu);
    $html = [];

    switch ($type) {
      default:
        if (isset($render) && !empty($render)) {
          foreach ($render as $item) {
            $html[] = '<li><a href="' . $item['url'] . '">' . $item['name'] . '</a></li>';
          }
        }
        break;
    }

    return implode('', $html);
  }

  public function remaining_day_tranche(){

      $response = [];
      try{

        $tranches = \Drupal::entityQuery('node')
          ->condition('status', 1)
          ->condition('type', "tranche")
          //->condition('field_statut_tranche', 23)
          //->condition('field_date_de_fin', date("Y/m/d"), '<=')
          ->execute();

        foreach ($tranches as $key => $tranche_id) 
        {
          $tranche = \Drupal::entityTypeManager()->getStorage('node')->load($tranche_id);
            //dump($tranche->nid->value); die;
          $remaining = $tranche->get('field_remaining_days_chantier')->value;
          $remaining_prep = $tranche->get('field_remaining_days_prep')->value;
          $duree = $tranche->get('field_duree_du_chantier')->value;
          $duree_prep = $tranche->get('field_duree_de_preparation')->value;
          if  ($remaining == null || $remaining == []){
            $remaining = $duree;
          }
          if  ($remaining_prep == null || $remaining_prep == []){
                $remaining_prep = $duree_prep;
          }
          $date_prep = $tranche->get('field_date_prep_chantier')->date->getTimestamp();
          $date_chantier = $tranche->get('field_date_de_demarrage')->date->getTimestamp();
          $date_fin_chantier = $tranche->get('field_date_de_fin')->date->getTimestamp();
          $date_fin_prep = $tranche->get('field_date_de_fin_prep')->date->getTimestamp();

          if ( (time() > $date_prep) && (time() <= $date_fin_prep) && $tranche->field_statut_tranche_prep->target_id !== "24")
          {
              $tranche->set('field_remaining_days_prep', $remaining_prep-1);
              $advancement_prep = 100*(((time()-$date_prep)/86400) / $duree_prep); //var_dump($advancement_prep);
              $tranche->set('field_avancement_prep', $advancement_prep);
          }
          if ((time() > $date_chantier) && (time() <= $date_fin_chantier) && $tranche->field_statut_tranche->target_id !== "24"){
              $tranche->set('field_remaining_days_chantier', $remaining-1);
              $advancement = 100 * ((time()-$date_chantier)/86400) / $duree;
              $tranche->set('field_avancement', $advancement);
          }

          $tranche->save();

          $response['tranches'][] = $tranche->id();

        }
        $response['status'] = 200;
      }catch (\Exception $e) {
        $response['status'] = 500;
        $response['result'] = $e->getMessage();
      }

      return new JsonResponse($response);
  
  }
}
