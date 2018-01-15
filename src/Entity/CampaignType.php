<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Campaign type entity.
 *
 * @ConfigEntityType(
 *   id = "campaign_type",
 *   label = @Translation("Campaign type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\campaign_kit\CampaignTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\campaign_kit\Form\CampaignTypeForm",
 *       "edit" = "Drupal\campaign_kit\Form\CampaignTypeForm",
 *       "delete" = "Drupal\campaign_kit\Form\CampaignTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\campaign_kit\CampaignTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "campaign_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "campaign",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/campaign/campaign_type/{campaign_type}",
 *     "add-form" = "/campaign/campaign_type/add",
 *     "edit-form" = "/campaign/campaign_type/{campaign_type}/edit",
 *     "delete-form" = "/campaign/campaign_type/{campaign_type}/delete",
 *     "collection" = "/campaign/campaign_type"
 *   }
 * )
 */
class CampaignType extends ConfigEntityBundleBase implements CampaignTypeInterface {

  /**
   * The Campaign type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Campaign type label.
   *
   * @var string
   */
  protected $label;

}
