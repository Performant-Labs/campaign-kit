<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class CampaignUpdateStorage extends SqlContentEntityStorage implements CampaignUpdateStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(CampaignUpdateInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {campaign_update_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {campaign_update_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(CampaignUpdateInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {campaign_update_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('campaign_update_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
