<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Campaign donation entities.
 *
 * @ingroup campaign_kit
 */
interface CampaignDonationInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Campaign donation name.
   *
   * @return string
   *   Name of the Campaign donation.
   */
  public function getName();

  /**
   * Sets the Campaign donation name.
   *
   * @param string $name
   *   The Campaign donation name.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignDonationInterface
   *   The called Campaign donation entity.
   */
  public function setName($name);

  /**
   * Gets the Campaign donation creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Campaign donation.
   */
  public function getCreatedTime();

  /**
   * Sets the Campaign donation creation timestamp.
   *
   * @param int $timestamp
   *   The Campaign donation creation timestamp.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignDonationInterface
   *   The called Campaign donation entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Campaign donation published status indicator.
   *
   * Unpublished Campaign donation are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Campaign donation is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Campaign donation.
   *
   * @param bool $published
   *   TRUE to set this Campaign donation to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignDonationInterface
   *   The called Campaign donation entity.
   */
  public function setPublished($published);

}
