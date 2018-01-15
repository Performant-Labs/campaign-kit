<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Campaign update type entity.
 *
 * @ConfigEntityType(
 *   id = "campaign_update_type",
 *   label = @Translation("Campaign update type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\campaign_kit\CampaignUpdateTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\campaign_kit\Form\CampaignUpdateTypeForm",
 *       "edit" = "Drupal\campaign_kit\Form\CampaignUpdateTypeForm",
 *       "delete" = "Drupal\campaign_kit\Form\CampaignUpdateTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\campaign_kit\CampaignUpdateTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "campaign_update_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "campaign_update",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/campaign_update_type/{campaign_update_type}",
 *     "add-form" = "/admin/structure/campaign_update_type/add",
 *     "edit-form" = "/admin/structure/campaign_update_type/{campaign_update_type}/edit",
 *     "delete-form" = "/admin/structure/campaign_update_type/{campaign_update_type}/delete",
 *     "collection" = "/admin/structure/campaign_update_type"
 *   }
 * )
 */
class CampaignUpdateType extends ConfigEntityBundleBase implements CampaignUpdateTypeInterface {

  /**
   * The Campaign update type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Campaign update type label.
   *
   * @var string
   */
  protected $label;

}
