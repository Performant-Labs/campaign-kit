<?php

namespace Drupal\campaign_kit\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CampaignTransactionTypeForm.
 */
class CampaignTransactionTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $campaign_transaction_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $campaign_transaction_type->label(),
      '#description' => $this->t("Label for the Campaign transaction type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $campaign_transaction_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\campaign_kit\Entity\CampaignTransactionType::load',
      ],
      '#disabled' => !$campaign_transaction_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $campaign_transaction_type = $this->entity;
    $status = $campaign_transaction_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Campaign transaction type.', [
          '%label' => $campaign_transaction_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Campaign transaction type.', [
          '%label' => $campaign_transaction_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($campaign_transaction_type->toUrl('collection'));
  }

}
