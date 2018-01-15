<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Campaign update entity.
 *
 * @see \Drupal\campaign_kit\Entity\CampaignUpdate.
 */
class CampaignUpdateAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\campaign_kit\Entity\CampaignUpdateInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished campaign update entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published campaign update entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit campaign update entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete campaign update entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add campaign update entities');
  }

}
