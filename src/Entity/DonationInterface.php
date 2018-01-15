<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Donation entities.
 *
 * @ingroup campaign_kit
 */
interface DonationInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Donation name.
   *
   * @return string
   *   Name of the Donation.
   */
  public function getName();

  /**
   * Sets the Donation name.
   *
   * @param string $name
   *   The Donation name.
   *
   * @return \Drupal\campaign_kit\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setName($name);

  /**
   * Gets the Donation creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Donation.
   */
  public function getCreatedTime();

  /**
   * Sets the Donation creation timestamp.
   *
   * @param int $timestamp
   *   The Donation creation timestamp.
   *
   * @return \Drupal\campaign_kit\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Donation published status indicator.
   *
   * Unpublished Donation are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Donation is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Donation.
   *
   * @param bool $published
   *   TRUE to set this Donation to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\campaign_kit\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setPublished($published);

  /**
   * Gets the Donation revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Donation revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\campaign_kit\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Donation revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Donation revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\campaign_kit\Entity\DonationInterface
   *   The called Donation entity.
   */
  public function setRevisionUserId($uid);

}
