<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Campaign donation entities.
 */
class CampaignDonationViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
