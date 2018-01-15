<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Campaign donation entities.
 *
 * @ingroup campaign_kit
 */
class CampaignDonationListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Campaign donation ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\campaign_kit\Entity\CampaignDonation */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.campaign_donation.edit_form',
      ['campaign_donation' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
