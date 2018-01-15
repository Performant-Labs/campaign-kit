<?php

/**
 * @file
 * Contains campaign_transaction.page.inc.
 *
 * Page callback for Campaign transaction entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Campaign transaction templates.
 *
 * Default template: campaign_transaction.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_campaign_transaction(array &$variables) {
  // Fetch CampaignTransaction Entity Object.
  $campaign_transaction = $variables['elements']['#campaign_transaction'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
