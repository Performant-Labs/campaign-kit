<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\campaign_kit\Entity\CampaignInterface;

/**
 * Defines the storage handler class for Campaign entities.
 *
 * This extends the base storage class, adding required special handling for
 * Campaign entities.
 *
 * @ingroup campaign_kit
 */
interface CampaignStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Campaign revision IDs for a specific Campaign.
   *
   * @param \Drupal\campaign_kit\Entity\CampaignInterface $entity
   *   The Campaign entity.
   *
   * @return int[]
   *   Campaign revision IDs (in ascending order).
   */
  public function revisionIds(CampaignInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Campaign author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Campaign revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\campaign_kit\Entity\CampaignInterface $entity
   *   The Campaign entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CampaignInterface $entity);

  /**
   * Unsets the language for all Campaign with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
