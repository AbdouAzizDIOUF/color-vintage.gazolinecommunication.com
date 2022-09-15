<?php

namespace Drupal\gazoline_manager\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\gazoline_manager\Controller\GazolineManagerController;

class GazolineManagerForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gazoline_manager_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('gazoline_manager.settings');


      /**
       * Cordonnées Bancaire.
       */
      $form['bancaire'] = [
          '#type' => 'details',
          '#title' => 'Cordonnées Bancaire',
          '#open' => TRUE,
      ];
      $form['bancaire']['paypal'] = [
          '#type' => 'details',
          '#title' => 'Cordonnées Paypal',
          '#open' => FALSE,
      ];

      $form['bancaire']['paypal']['client_id'] = [
          '#type' => 'textfield',
          '#title' => 'Client ID :',
          '#default_value' => $config->get('gazoline_manager.paypal.client_id'),
      ];

      $form['bancaire']['paypal']['client_secret'] = [
          '#type' => 'textfield',
          '#title' => 'Client Secret :',
          '#default_value' => $config->get('gazoline_manager.paypal.client_secret'),
      ];

      $form['bancaire']['stripe'] = [
          '#type' => 'details',
          '#title' => 'Cordonnées Stripe',
          '#open' => FALSE,
      ];

      $form['bancaire']['stripe']['cle_publique'] = [
          '#type' => 'textfield',
          '#title' => 'Clé publique :',
          '#default_value' => $config->get('gazoline_manager.stripe.cle_publique'),
      ];

      $form['bancaire']['stripe']['cle_secrete'] = [
          '#type' => 'textfield',
          '#title' => 'Clé secréte',
          '#default_value' => $config->get('gazoline_manager.stripe.cle_secrete'),
      ];

    /**
     * Téléphones.
     */
    $form['phones'] = [
      '#type' => 'details',
      '#title' => 'Téléphones',
      '#open' => TRUE,
    ];
    $form['phones']['phone1'] = [
      '#type' => 'tel',
      '#title' => 'Téléphone principal :',
      '#default_value' => $config->get('gazoline_manager.phone1'),
    ];
    $form['phones']['phone2'] = [
      '#type' => 'tel',
      '#title' => 'Téléphone secondaire :',
      '#default_value' => $config->get('gazoline_manager.phone2'),
    ];
    $form['phones']['fax'] = [
      '#type' => 'tel',
      '#title' => 'Fax :',
      '#default_value' => $config->get('gazoline_manager.fax'),
    ];


    /**
     * Emails.
     */
    $form['mails'] = [
      '#type' => 'details',
      '#title' => 'Emails',
      '#open' => TRUE,
    ];
    $form['mails']['mail1'] = [
      '#type' => 'email',
      '#title' => 'Email principal :',
      '#default_value' => $config->get('gazoline_manager.mail1'),
    ];
    $form['mails']['mail2'] = [
      '#type' => 'email',
      '#title' => 'Email secondaire :',
      '#default_value' => $config->get('gazoline_manager.mail2'),
    ];

    /**
     * Adresse.
     */
    $form['address'] = [
      '#type' => 'details',
      '#title' => 'Adresse',
      '#open' => TRUE,
    ];
    $form['address']['address1'] = [
      '#type' => 'textfield',
      '#title' => 'Adresse :',
      '#default_value' => $config->get('gazoline_manager.address1'),
    ];
    $form['address']['address2'] = [
      '#type' => 'textfield',
      '#title' => NULL,
      '#default_value' => $config->get('gazoline_manager.address2'),
    ];
    $form['address']['address3'] = [
      '#type' => 'textfield',
      '#title' => NULL,
      '#default_value' => $config->get('gazoline_manager.address3'),
    ];
    $form['address']['cp'] = [
      '#type' => 'textfield',
      '#title' => 'Code postal :',
      '#default_value' => $config->get('gazoline_manager.cp'),
      '#attributes' => [
        'maxlength' => 5,
      ],
    ];
    $form['address']['city'] = [
      '#type' => 'textfield',
      '#title' => 'Ville :',
      '#default_value' => $config->get('gazoline_manager.city'),
    ];
    $form['address']['coords'] = [
      '#type' => 'details',
      '#title' => 'Coordonnées géographiques',
      '#open' => FALSE,
    ];
    $form['address']['coords']['generate'] = [
      '#type' => 'checkbox',
      '#title' => 'Obtenir automatiquement',
      '#default_value' => '1',
      '#attributes' => [
        'checked' => 'checked',
      ],
      '#description' => 'Cochez pour tenter d\'obtenir la latitude et la longitude via l\'adresse renseignée.<br/>Il est également possible de connaitre ces informations sur <a href="https://www.coordonnees-gps.fr/" target="_blank">ce site</a>.',
    ];
    $form['address']['coords']['lat'] = [
      '#type' => 'textfield',
      '#title' => 'Latitude :',
      '#default_value' => $config->get('gazoline_manager.lat'),
    ];
    $form['address']['coords']['lng'] = [
      '#type' => 'textfield',
      '#title' => 'Longitude :',
      '#default_value' => $config->get('gazoline_manager.lng'),
    ];

    /**
     * Autres infos.
     */
    $form['other'] = [
      '#type' => 'details',
      '#title' => 'Autres informations',
      '#open' => TRUE,
    ];
    $form['other']['slogan'] = [
      '#type' => 'textfield',
      '#title' => 'Slogan :',
      '#default_value' => $config->get('gazoline_manager.slogan'),
    ];
    $form['other']['siret'] = [
      '#type' => 'textfield',
      '#title' => 'SIRET :',
      '#default_value' => $config->get('gazoline_manager.siret'),
    ];
    $form['other']['horaires'] = [
      '#type' => 'textarea',
      '#title' => 'Horaires :',
      '#default_value' => $config->get('gazoline_manager.horaires'),
    ];

    /**
     * Réseaux sociaux.
     */
    $form['social'] = [
      '#type' => 'details',
      '#title' => 'Réseaux sociaux',
      '#open' => TRUE,
    ];
    $form['social']['facebook'] = [
      '#type' => 'textfield',
      '#title' => 'Facebook :',
      '#default_value' => $config->get('gazoline_manager.facebook'),
    ];
    $form['social']['googleplus'] = [
      '#type' => 'textfield',
      '#title' => 'Google+ :',
      '#default_value' => $config->get('gazoline_manager.googleplus'),
    ];
    $form['social']['twitter'] = [
      '#type' => 'textfield',
      '#title' => 'Twitter :',
      '#default_value' => $config->get('gazoline_manager.twitter'),
    ];
    $form['social']['linkedin'] = [
      '#type' => 'textfield',
      '#title' => 'LinkedIn :',
      '#default_value' => $config->get('gazoline_manager.linkedin'),
    ];
    $form['social']['youtube'] = [
      '#type' => 'textfield',
      '#title' => 'Youtube :',
      '#default_value' => $config->get('gazoline_manager.youtube'),
    ];
    $form['social']['viadeo'] = [
      '#type' => 'textfield',
      '#title' => 'Viadeo :',
      '#default_value' => $config->get('gazoline_manager.viadeo'),
    ];
    $form['social']['pinterest'] = [
      '#type' => 'textfield',
      '#title' => 'Pinterest :',
      '#default_value' => $config->get('gazoline_manager.pinterest'),
    ];
    $form['social']['instagram'] = [
      '#type' => 'textfield',
      '#title' => 'Instagram :',
      '#default_value' => $config->get('gazoline_manager.instagram'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Latitude.
    /*if (!empty($form_state->getValue('lat')) && !is_numeric($form_state->getValue('lat'))) {
      $form_state->setErrorByName('lat', 'La latitude doit être un nombre.');
    }

    // Longitude.
    if (!empty($form_state->getValue('lng')) && !is_numeric($form_state->getValue('lng'))) {
      $form_state->setErrorByName('lng', 'La longitude doit être un nombre.');
    }*/
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('gazoline_manager.settings');


      // Coordonnées Bancaire.
      $config->set('gazoline_manager.paypal.client_id', $form_state->getValue('client_id'));
      $config->set('gazoline_manager.paypal.client_secret', $form_state->getValue('client_secret'));
      $config->set('gazoline_manager.stripe.cle_publique', $form_state->getValue('cle_publique'));
      $config->set('gazoline_manager.stripe.cle_secrete', $form_state->getValue('cle_secrete'));


    // Téléphones.
    $config->set('gazoline_manager.phone1', $form_state->getValue('phone1'));
    $config->set('gazoline_manager.phone2', $form_state->getValue('phone2'));
    $config->set('gazoline_manager.fax', $form_state->getValue('fax'));

    // Emails.
    $config->set('gazoline_manager.mail1', $form_state->getValue('mail1'));
    $config->set('gazoline_manager.mail2', $form_state->getValue('mail2'));

    // Adresse.
    $config->set('gazoline_manager.address1', $form_state->getValue('address1'));
    $config->set('gazoline_manager.address2', $form_state->getValue('address2'));
    $config->set('gazoline_manager.address3', $form_state->getValue('address3'));
    $config->set('gazoline_manager.cp', $form_state->getValue('cp'));
    $config->set('gazoline_manager.city', $form_state->getValue('city'));

    if ($form_state->getValue('generate') == 1) {
      $address = [
//        $form_state->getValue('address1'),
        $form_state->getValue('address2'),
        $form_state->getValue('address3'),
        $form_state->getValue('cp'),
        $form_state->getValue('city'),
      ];

      $lat_lng = GazolineManagerController::get_lat_lng_from_address($address);

      $config->set('gazoline_manager.lat', $lat_lng->lat);
      $config->set('gazoline_manager.lng', $lat_lng->lng);
    }
    else {
      $config->set('gazoline_manager.lat', $form_state->getValue('lat'));
      $config->set('gazoline_manager.lng', $form_state->getValue('lng'));
    }

    // Slogan.
    $config->set('gazoline_manager.slogan', $form_state->getValue('slogan'));

    // SIRET.
    $config->set('gazoline_manager.siret', $form_state->getValue('siret'));

    // Horaires.
    $config->set('gazoline_manager.horaires', $form_state->getValue('horaires'));

    // Réseaux sociaux.
    $config->set('gazoline_manager.facebook', $form_state->getValue('facebook'));
    $config->set('gazoline_manager.googleplus', $form_state->getValue('googleplus'));
    $config->set('gazoline_manager.twitter', $form_state->getValue('twitter'));
    $config->set('gazoline_manager.linkedin', $form_state->getValue('linkedin'));
    $config->set('gazoline_manager.youtube', $form_state->getValue('youtube'));
    $config->set('gazoline_manager.viadeo', $form_state->getValue('viadeo'));
    $config->set('gazoline_manager.instagram', $form_state->getValue('instagram'));
    $config->set('gazoline_manager.pinterest', $form_state->getValue('pinterest'));

    $config->save();

    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'gazoline_manager.settings',
    ];
  }

}