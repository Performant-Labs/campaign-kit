<?php

namespace Drupal\campaign_kit\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CampaignUpdateTypeForm.
 */
class CampaignUpdateTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $campaign_update_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $campaign_update_type->label(),
      '#description' => $this->t("Label for the Campaign update type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $campaign_update_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\campaign_kit\Entity\CampaignUpdateType::load',
      ],
      '#disabled' => !$campaign_update_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $campaign_update_type = $this->entity;
    $status = $campaign_update_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Campaign update type.', [
          '%label' => $campaign_update_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Campaign update type.', [
          '%label' => $campaign_update_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($campaign_update_type->toUrl('collection'));
  }

}
