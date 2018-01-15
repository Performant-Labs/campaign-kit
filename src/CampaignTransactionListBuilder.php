<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Campaign transaction entities.
 *
 * @ingroup campaign_kit
 */
class CampaignTransactionListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Campaign transaction ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\campaign_kit\Entity\CampaignTransaction */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.campaign_transaction.edit_form',
      ['campaign_transaction' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
