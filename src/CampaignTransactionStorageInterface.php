<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\campaign_kit\Entity\CampaignTransactionInterface;

/**
 * Defines the storage handler class for Campaign transaction entities.
 *
 * This extends the base storage class, adding required special handling for
 * Campaign transaction entities.
 *
 * @ingroup campaign_kit
 */
interface CampaignTransactionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Campaign transaction revision IDs for a specific Campaign transaction.
   *
   * @param \Drupal\campaign_kit\Entity\CampaignTransactionInterface $entity
   *   The Campaign transaction entity.
   *
   * @return int[]
   *   Campaign transaction revision IDs (in ascending order).
   */
  public function revisionIds(CampaignTransactionInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Campaign transaction author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Campaign transaction revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\campaign_kit\Entity\CampaignTransactionInterface $entity
   *   The Campaign transaction entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CampaignTransactionInterface $entity);

  /**
   * Unsets the language for all Campaign transaction with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
