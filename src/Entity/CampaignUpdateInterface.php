<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Campaign update entities.
 *
 * @ingroup campaign_kit
 */
interface CampaignUpdateInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Campaign update name.
   *
   * @return string
   *   Name of the Campaign update.
   */
  public function getName();

  /**
   * Sets the Campaign update name.
   *
   * @param string $name
   *   The Campaign update name.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignUpdateInterface
   *   The called Campaign update entity.
   */
  public function setName($name);

  /**
   * Gets the Campaign update creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Campaign update.
   */
  public function getCreatedTime();

  /**
   * Sets the Campaign update creation timestamp.
   *
   * @param int $timestamp
   *   The Campaign update creation timestamp.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignUpdateInterface
   *   The called Campaign update entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Campaign update published status indicator.
   *
   * Unpublished Campaign update are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Campaign update is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Campaign update.
   *
   * @param bool $published
   *   TRUE to set this Campaign update to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignUpdateInterface
   *   The called Campaign update entity.
   */
  public function setPublished($published);

  /**
   * Gets the Campaign update revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Campaign update revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignUpdateInterface
   *   The called Campaign update entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Campaign update revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Campaign update revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignUpdateInterface
   *   The called Campaign update entity.
   */
  public function setRevisionUserId($uid);

}
