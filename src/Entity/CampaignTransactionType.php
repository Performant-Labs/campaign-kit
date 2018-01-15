<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Campaign transaction type entity.
 *
 * @ConfigEntityType(
 *   id = "campaign_transaction_type",
 *   label = @Translation("Campaign transaction type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\campaign_kit\CampaignTransactionTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\campaign_kit\Form\CampaignTransactionTypeForm",
 *       "edit" = "Drupal\campaign_kit\Form\CampaignTransactionTypeForm",
 *       "delete" = "Drupal\campaign_kit\Form\CampaignTransactionTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\campaign_kit\CampaignTransactionTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "campaign_transaction_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "campaign_transaction",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/campaign_transaction_type/{campaign_transaction_type}",
 *     "add-form" = "/admin/structure/campaign_transaction_type/add",
 *     "edit-form" = "/admin/structure/campaign_transaction_type/{campaign_transaction_type}/edit",
 *     "delete-form" = "/admin/structure/campaign_transaction_type/{campaign_transaction_type}/delete",
 *     "collection" = "/admin/structure/campaign_transaction_type"
 *   }
 * )
 */
class CampaignTransactionType extends ConfigEntityBundleBase implements CampaignTransactionTypeInterface {

  /**
   * The Campaign transaction type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Campaign transaction type label.
   *
   * @var string
   */
  protected $label;

}
