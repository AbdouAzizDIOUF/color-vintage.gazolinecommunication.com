<?php

namespace Drupal\adminimal_gazoline\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class AdminimalGazolineForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'adminimal_gazoline_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('adminimal_gazoline.settings');

    /**
     * Logo.
     */
    $form['logo'] = [
        '#type' => 'details',
        '#title' => 'Logo',
        '#open' => TRUE,
    ];
    $form['logo']['color1'] = [
        '#type' => 'color',
        '#title' => 'Couleur 1',
        '#default_value' => $config->get('adminimal_gazoline.color1'),
    ];
    $form['logo']['color2'] = [
        '#type' => 'color',
        '#title' => 'Couleur 2',
        '#default_value' => $config->get('adminimal_gazoline.color2'),
    ];

    /**
     * Raccourcis.
     */
    $form['shortcuts'] = [
        '#type' => 'details',
        '#title' => 'Raccourcis',
        '#open' => TRUE,
    ];
    $form['shortcuts']['display'] = [
        '#type' => 'checkbox',
        '#title' => 'Afficher',
        '#default_value' => '1',
        '#attributes' => [
            'checked' => 'checked',
        ],
    ];

    /**
     * Multi site.
     */
    $form['multisite'] = [
        '#type' => 'details',
        '#title' => 'Multisite',
        '#open' => TRUE,
    ];
    $form['multisite']['multisite_actif'] = [
        '#type' => 'checkbox',
        '#title' => 'Activer le multisite menu',
        '#default_value' => $config->get('adminimal_gazoline.multisite_actif'),
        '#attributes' => $config->get('adminimal_gazoline.multisite_actif') ? [
            'checked' => 'checked',
        ] : []
    ];
    $form['multisite']['multisite_json'] = [
        '#type' => 'textarea',
        '#title' => 'Admin menu JSON',
        '#default_value' =>  $config->get('adminimal_gazoline.multisite_json'),
        '#attributes' => !$config->get('adminimal_gazoline.multisite_actif') ? [ 'disabled' => 'disabled'] : ['placeholder' => '{monsite1.com: menu_machine_name_1,monsite2.com: menu_machine_name_2}']
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $config = $this->config('adminimal_gazoline.settings');

    // Téléphones.
    $config->set('adminimal_gazoline.color1', $form_state->getValue('color1'));
    $config->set('adminimal_gazoline.color2', $form_state->getValue('color2'));

    // Emails.
    $config->set('adminimal_gazoline.display', $form_state->getValue('display'));

    // Multisite.
    $config->set('adminimal_gazoline.multisite_actif', $form_state->getValue('multisite_actif'));
    $config->set('adminimal_gazoline.multisite_json', $form_state->getValue('multisite_json'));
    

    $config->save();

    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
        'adminimal_gazoline.settings',
    ];
  }

}