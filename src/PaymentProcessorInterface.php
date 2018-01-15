<?php

namespace Drupal\campaign_kit;

/**
 * An interface for all Payment Processors type plugins.
 *
 * When defining a new plugin type you need to define an interface that all
 * plugins of the new type will implement. This ensures that consumers of the
 * plugin type have a consistent way of accessing the plugin's functionality. It
 * should include access to any public properties, and methods for accomplishing
 * whatever business logic anyone accessing the plugin might want to use.
 *
 * For example, an image manipulation plugin might have a "process" method that
 * takes a known input, probably an image file, and returns the processed
 * version of the file.
 *
 * In our case we'll define methods for accessing the human readable description
 * of a payment processor and the account ID. As well as a method to get the
 * account ID.
 *
 * Interface PaymentProcessorInterface
 *
 * @package Drupal\campaign_kit
 */
interface PaymentProcessorInterface{

  /**
   * Provide a description of the payment processor.
   *
   * @return string
   *    A string description of the payment processor.
   */
  public function description();

  /**
   * Provide the account id of the payment processor.
   *
   * @return int
   *    The Account ID.
   */
  public function accountId();


}