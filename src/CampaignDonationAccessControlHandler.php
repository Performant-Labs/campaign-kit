<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Campaign donation entity.
 *
 * @see \Drupal\campaign_kit\Entity\CampaignDonation.
 */
class CampaignDonationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\campaign_kit\Entity\CampaignDonationInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished campaign donation entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published campaign donation entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit campaign donation entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete campaign donation entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add campaign donation entities');
  }

}
