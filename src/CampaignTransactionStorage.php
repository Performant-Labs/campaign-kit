<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class CampaignTransactionStorage extends SqlContentEntityStorage implements CampaignTransactionStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(CampaignTransactionInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {campaign_transaction_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {campaign_transaction_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(CampaignTransactionInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {campaign_transaction_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('campaign_transaction_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
