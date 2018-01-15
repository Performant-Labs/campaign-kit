<?php

namespace Drupal\campaign_kit;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\campaign_kit\Annotation\PaymentProcessor;

/**
 * A plugin manager for payment processor plugins.
 *
 * The PaymentProcessorPluginManager class extends the DefaultPluginManager to
 * provide a way to manage payment processor plugins. A plugin manager defines
 * a new plugin type and how instances of any plugin of that type will be
 * discovered, instantiated and more.
 *
 * Using the DefaultPluginManager as a starting point sets up our payment
 * processor plugin type to use annotated discovery.
 *
 * The plugin manager is also declared as a service in
 * 'name_module'.services.yml so that it can be easily accessed and used
 * anytime we need to work with payment processor plugins
 *
 * Class PaymentProcessorPluginManager
 *
 * @package Drupal\campaign_kit
 *
 */
class PaymentProcessorPluginManager extends DefaultPluginManager{
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    // We replaced the $subdir parameter with our own value.
    // This tells the plugin manager to look for Payment Processor plugins in the
    // 'src/Plugin/PaymentProcessor' subdirectory of any enabled modules. This also
    // serves to define the PSR-4 subnamespace in which payment processor plugins will
    // live. Modules can put a plugin class in their own namespace such as
    // Drupal\{module_name}\Plugin\Sandwich\MySandwichPlugin.
    $subdir = 'Plugin/PaymentProcessor';

    // The name of the interface that plugins should adhere to. Drupal will
    // enforce this as a requirement. If a plugin does not implement this
    // interface, Drupal will throw an error.
    $plugin_interface = PaymentProcessorInterface::class;

    // The name of the annotation class that contains the plugin definition.
    $plugin_definition_annotation_name = PaymentProcessor::class;

    parent::__construct($subdir, $namespaces, $module_handler, $plugin_interface, $plugin_definition_annotation_name);

    // This allows the plugin definitions to be altered by an alter hook. The
    // parameter defines the name of the hook, thus: hook_payment_processor_info_alter().
    // In this example, we implement this hook to change the plugin definitions:
    // see plugin_type_example_payment_processor_info_alter().
    $this->alterInfo('payment_processor_info');


  }
}