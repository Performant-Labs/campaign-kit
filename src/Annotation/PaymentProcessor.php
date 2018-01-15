<?php

namespace Drupal\campaign_kit\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a PaymentProcessor annotation object.
 *
 * Provides an example of how to define a new annotation type for use in
 * defining a plugin type. Demonstrates documenting the various properties that
 * can be used in annotations for plugins of this type.
 *
 * Note that the "@ Annotation" line below is required and should be the last
 * line in the docblock. It's used for discovery of Annotation definitions.
 *
 * @see \Drupal\plugin_type_example\SandwichPluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class PaymentProcessor extends Plugin{
  public $description;

  public $accountId;
}