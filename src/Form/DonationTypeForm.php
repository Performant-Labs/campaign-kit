<?php

namespace Drupal\campaign_kit\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DonationTypeForm.
 */
class DonationTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $donation_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $donation_type->label(),
      '#description' => $this->t("Label for the Donation type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $donation_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\campaign_kit\Entity\DonationType::load',
      ],
      '#disabled' => !$donation_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $donation_type = $this->entity;
    $status = $donation_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Donation type.', [
          '%label' => $donation_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Donation type.', [
          '%label' => $donation_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($donation_type->toUrl('collection'));
  }

}
