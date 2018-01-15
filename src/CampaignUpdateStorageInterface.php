<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\campaign_kit\Entity\CampaignUpdateInterface;

/**
 * Defines the storage handler class for Campaign update entities.
 *
 * This extends the base storage class, adding required special handling for
 * Campaign update entities.
 *
 * @ingroup campaign_kit
 */
interface CampaignUpdateStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Campaign update revision IDs for a specific Campaign update.
   *
   * @param \Drupal\campaign_kit\Entity\CampaignUpdateInterface $entity
   *   The Campaign update entity.
   *
   * @return int[]
   *   Campaign update revision IDs (in ascending order).
   */
  public function revisionIds(CampaignUpdateInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Campaign update author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Campaign update revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\campaign_kit\Entity\CampaignUpdateInterface $entity
   *   The Campaign update entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CampaignUpdateInterface $entity);

  /**
   * Unsets the language for all Campaign update with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
