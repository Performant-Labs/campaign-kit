<?php

/**
 * @file
 * Contains campaign_donation.page.inc.
 *
 * Page callback for Campaign donation entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Campaign donation templates.
 *
 * Default template: campaign_donation.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_campaign_donation(array &$variables) {
  // Fetch CampaignDonation Entity Object.
  $campaign_donation = $variables['elements']['#campaign_donation'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
