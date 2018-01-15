<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Campaign entities.
 *
 * @ingroup campaign_kit
 */
class CampaignListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Campaign ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\campaign_kit\Entity\Campaign */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      //'entity.campaign.edit_form',
      'campaign_kit.campaign_kit_controller_view_campaign_entity',
      //['campaign' => $entity->id()]
      ['campaignId' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
