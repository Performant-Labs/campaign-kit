<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\campaign_kit\Entity\DonationInterface;

/**
 * Defines the storage handler class for Donation entities.
 *
 * This extends the base storage class, adding required special handling for
 * Donation entities.
 *
 * @ingroup campaign_kit
 */
interface DonationStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Donation revision IDs for a specific Donation.
   *
   * @param \Drupal\campaign_kit\Entity\DonationInterface $entity
   *   The Donation entity.
   *
   * @return int[]
   *   Donation revision IDs (in ascending order).
   */
  public function revisionIds(DonationInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Donation author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Donation revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\campaign_kit\Entity\DonationInterface $entity
   *   The Donation entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(DonationInterface $entity);

  /**
   * Unsets the language for all Donation with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
