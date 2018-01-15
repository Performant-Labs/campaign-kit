<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Donation type entity.
 *
 * @ConfigEntityType(
 *   id = "donation_type",
 *   label = @Translation("Donation type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\campaign_kit\DonationTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\campaign_kit\Form\DonationTypeForm",
 *       "edit" = "Drupal\campaign_kit\Form\DonationTypeForm",
 *       "delete" = "Drupal\campaign_kit\Form\DonationTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\campaign_kit\DonationTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "donation_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "donation",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/donation_type/{donation_type}",
 *     "add-form" = "/admin/structure/donation_type/add",
 *     "edit-form" = "/admin/structure/donation_type/{donation_type}/edit",
 *     "delete-form" = "/admin/structure/donation_type/{donation_type}/delete",
 *     "collection" = "/admin/structure/donation_type"
 *   }
 * )
 */
class DonationType extends ConfigEntityBundleBase implements DonationTypeInterface {

  /**
   * The Donation type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Donation type label.
   *
   * @var string
   */
  protected $label;

}
