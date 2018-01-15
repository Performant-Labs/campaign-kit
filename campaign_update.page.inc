<?php

/**
 * @file
 * Contains campaign_update.page.inc.
 *
 * Page callback for Campaign update entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Campaign update templates.
 *
 * Default template: campaign_update.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_campaign_update(array &$variables) {
  // Fetch CampaignUpdate Entity Object.
  $campaign_update = $variables['elements']['#campaign_update'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
