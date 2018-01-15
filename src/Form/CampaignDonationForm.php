<?php

namespace Drupal\campaign_kit\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Campaign donation edit forms.
 *
 * @ingroup campaign_kit
 */
class CampaignDonationForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\campaign_kit\Entity\CampaignDonation */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Campaign donation.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Campaign donation.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.campaign_donation.canonical', ['campaign_donation' => $entity->id()]);
  }

}
