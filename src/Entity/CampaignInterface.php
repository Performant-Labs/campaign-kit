<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Campaign entities.
 *
 * @ingroup campaign_kit
 */
interface CampaignInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Campaign name.
   *
   * @return string
   *   Name of the Campaign.
   */
  public function getName();

  /**
   * Sets the Campaign name.
   *
   * @param string $name
   *   The Campaign name.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignInterface
   *   The called Campaign entity.
   */
  public function setName($name);

  /**
   * Gets the Campaign creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Campaign.
   */
  public function getCreatedTime();

  /**
   * Sets the Campaign creation timestamp.
   *
   * @param int $timestamp
   *   The Campaign creation timestamp.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignInterface
   *   The called Campaign entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Campaign published status indicator.
   *
   * Unpublished Campaign are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Campaign is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Campaign.
   *
   * @param bool $published
   *   TRUE to set this Campaign to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignInterface
   *   The called Campaign entity.
   */
  public function setPublished($published);

  /**
   * Gets the Campaign revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Campaign revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignInterface
   *   The called Campaign entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Campaign revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Campaign revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignInterface
   *   The called Campaign entity.
   */
  public function setRevisionUserId($uid);

}
