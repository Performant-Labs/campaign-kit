<?php

namespace Drupal\campaign_kit;

use Drupal\Component\Plugin\PluginBase;

/**
 * A base class to help developers implement their own payment processor plugins.
 *
 * This is a helper class which makes it easier for other developers to
 * implement payment processor plugins in their own modules. In PaymentProcessorBase
 * we provide some generic methods for handling tasks that are common to pretty
 * much all payment processor plugins. Thereby reducing the amount of boilerplate
 * code required to implement a payment processor plugin.
 *
 * In this case both the description and the account ID properties can be read from
 * the @PaymentProcessor annotation. In most cases it is probably fine to just use that
 * value without any additional processing. However, if an individual plugin
 * needed to provide special handling around either of these things it could
 * just override the method in that class definition for that plugin.
 *
 * We intentionally declare our base class as abstract, and don't implement the
 * order() method required by \Drupal\campaign_kit\PaymentProcessorInterface.
 * This way even if they are using our base class, developers will always be
 * required to define an order() method for their custom payment processor type.
 *
 * @see \Drupal\campaign_kit\Annotation\PaymentProcessor
 * @see \Drupal\campaign_kit\PaymentProcessorInterface
 *
 * Class PaymentProcessorBase
 *
 * @package Drupal\campaign_kit
 */
abstract class PaymentProcessorBase extends PluginBase implements PaymentProcessorInterface{

  public function accountId() {
    // TODO: Implement accountId() method.
    return $this->pluginDefinition['accountId'];
  }

  public function getCurrency() {
    // TODO: Implement accountId() method.
    return 'USD';
  }
}
