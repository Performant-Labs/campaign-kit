<?php

namespace Drupal\campaign_kit\Plugin\PaymentProcessor;

use Drupal\campaign_kit\PaymentProcessorBase;

/**
 * Provides a PayPal payment processor.
 *
 * Because the plugin manager class for our plugin uses annotated class
 * discovery, our PayPal payment processor only needs to exist within the
 * Plugin\PaymentProcessor namespace, and provide a PaymentProcessor annotation
 * to be declared as a plugin. This is defined in
 * \Drupal\payment_processor\PaymentProcessorPluginManager::__construct().
 *
 * The following is the plugin annotation. This is parsed by Doctrine to make
 * the plugin definition. Any values defined here will be available in the
 * plugin definition.
 *
 * This should be used for metadata that is specifically required to instantiate
 * the plugin, or for example data that might be needed to display a list of all
 * available plugins where the user selects one. This means many plugin
 * annotations can be reduced to a plugin ID, a label and perhaps a description.
 *
 * @PaymentProcessor(
 *   id = "pay_pal",
 *   description = @Translation("Online, payment, campaign kit for PayPal"),
 *   accountId = 72738839
 * )
 */
class PayPal extends PaymentProcessorBase{

  /**
   * @return string
   */
  public function description() {
    // TODO: Implement description() method.
    //return $this->pluginDefinition['description'];
    return 'This is my custom description from PayPal.php';
  }

  /**
   * @return string
   */
  public function getCurrency() {
    // Calling Config 'paymentprocessor.yml'
    $config = \Drupal::config('campaign_kit.paymentprocessor');
    return $config->get('campaign_kit.currency');
  }

  /**
   * @return string
   */
  public function getClientEmail() {
    // Calling Config 'paymentprocessor.yml'
    $config = \Drupal::config('campaign_kit.paymentprocessor');
    return $config->get('campaign_kit.pay_pal_email');
  }

  /**
   * @return array|mixed|null
   */
  public function getPluginInformation() {
    // TODO: complete all necessary information about client PayPal plugin
    // Calling Config 'paymentprocessor.yml'
    $config = \Drupal::config('campaign_kit.paymentprocessor');
    $clientPlugin =  $config->get('campaign_kit');

    return $clientPlugin;
  }

  /**
   * @param array $plugin
   */
  public function editPluginInformation(array $plugin) {
    $config = \Drupal::service('config.factory')->getEditable('campaign_kit.paymentprocessor');

    // Set and save new message value.
    //$config->set('campaign_kit.account_id', $plugin['account_id'])->save();

    foreach ($plugin as $key => $value) {
      $nameField = 'campaign_kit.'.$key;
      $config->set($nameField, $value)->save();
    }

  }

}