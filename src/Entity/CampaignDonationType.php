<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Campaign donation type entity.
 *
 * @ConfigEntityType(
 *   id = "campaign_donation_type",
 *   label = @Translation("Campaign donation type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\campaign_kit\CampaignDonationTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\campaign_kit\Form\CampaignDonationTypeForm",
 *       "edit" = "Drupal\campaign_kit\Form\CampaignDonationTypeForm",
 *       "delete" = "Drupal\campaign_kit\Form\CampaignDonationTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\campaign_kit\CampaignDonationTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "campaign_donation_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "campaign_donation",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/campaign_donation_type/{campaign_donation_type}",
 *     "add-form" = "/admin/structure/campaign_donation_type/add",
 *     "edit-form" = "/admin/structure/campaign_donation_type/{campaign_donation_type}/edit",
 *     "delete-form" = "/admin/structure/campaign_donation_type/{campaign_donation_type}/delete",
 *     "collection" = "/admin/structure/campaign_donation_type"
 *   }
 * )
 */
class CampaignDonationType extends ConfigEntityBundleBase implements CampaignDonationTypeInterface {

  /**
   * The Campaign donation type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Campaign donation type label.
   *
   * @var string
   */
  protected $label;

}
