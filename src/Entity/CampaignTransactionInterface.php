<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Campaign transaction entities.
 *
 * @ingroup campaign_kit
 */
interface CampaignTransactionInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Campaign transaction name.
   *
   * @return string
   *   Name of the Campaign transaction.
   */
  public function getName();

  /**
   * Sets the Campaign transaction name.
   *
   * @param string $name
   *   The Campaign transaction name.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignTransactionInterface
   *   The called Campaign transaction entity.
   */
  public function setName($name);

  /**
   * Gets the Campaign transaction creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Campaign transaction.
   */
  public function getCreatedTime();

  /**
   * Sets the Campaign transaction creation timestamp.
   *
   * @param int $timestamp
   *   The Campaign transaction creation timestamp.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignTransactionInterface
   *   The called Campaign transaction entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Campaign transaction published status indicator.
   *
   * Unpublished Campaign transaction are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Campaign transaction is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Campaign transaction.
   *
   * @param bool $published
   *   TRUE to set this Campaign transaction to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignTransactionInterface
   *   The called Campaign transaction entity.
   */
  public function setPublished($published);

  /**
   * Gets the Campaign transaction revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Campaign transaction revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignTransactionInterface
   *   The called Campaign transaction entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Campaign transaction revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Campaign transaction revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\campaign_kit\Entity\CampaignTransactionInterface
   *   The called Campaign transaction entity.
   */
  public function setRevisionUserId($uid);

}
