<?php

namespace Drupal\campaign_kit\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\campaign_kit\PaymentProcessorPluginManager;

/**
 * Class PaymentProcessorForm.
 */
class PaymentProcessorForm extends FormBase {

  /**
   * Drupal\campaign_kit\PaymentProcessorPluginManager definition.
   *
   * @var \Drupal\campaign_kit\PaymentProcessorPluginManager
   */
  protected $pluginManagerPaymentProcessor;

  /**
   * Constructs a new PaymentProcessorForm object.
   *
   * @param \Drupal\campaign_kit\PaymentProcessorPluginManager $plugin_manager_payment_processor
   */
  public function __construct(PaymentProcessorPluginManager $plugin_manager_payment_processor) {
    $this->pluginManagerPaymentProcessor = $plugin_manager_payment_processor;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.payment_processor')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'payment_processor_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $pluginId = NULL) {
    drupal_set_message('Change the fields with your PayPal information.');

    // Using Payment Processor service
    $plugin = $this->pluginManagerPaymentProcessor->createInstance($pluginId, ['of' => 'configuration values']);
    $items['description'] = $plugin->description();
    $items['currency'] = $plugin->getCurrency();

    $paymentProcessorConfiguration = $plugin->getPluginInformation();

    $form['account_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Account id'),
      '#description' => $this->t('This is the Account PayPal ID'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $paymentProcessorConfiguration['account_id'],
    ];

    // $pay_pal_email
    $form['pay_pal_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#description' => 'Email, #type = email',
      '#default_value' => $paymentProcessorConfiguration['pay_pal_email'],
    ];

    // Currency
    $form['currency'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Currency'),
      '#description' => $this->t('This currency type wil be used for all transactions'),
      '#maxlength' => 6,
      '#size' => 6,
      '#default_value' => $paymentProcessorConfiguration['currency'],
    ];

    // Version
    $form['version'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Version Plugin'),
      '#description' => $this->t('This the Version number Plugin'),
      '#maxlength' => 6,
      '#size' => 6,
      '#default_value' => $paymentProcessorConfiguration['version'],
      '#disable' => true,
    ];
    // Select for allowed frequency
    $form['frequency'] = [
      '#type' => 'select',
      '#title' => $this->t('Frequency allowed'),
      '#default_value' => ($paymentProcessorConfiguration['frequency'] != 'none'? $paymentProcessorConfiguration['frequency']:'empty_option'),
      '#options' => [
        'onetime' => $this->t('One time'),
        'monthly' => $this->t('Monthly'),
        'both_or' => $this->t('Both'),
      ],
      '#empty_option' => $this->t('- None -'),
      //'#description' => $this->t('Select, #type = select'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    //parent::validateForm($form, $form_state);
    if ((int)$form_state->getValue('account_id') == 0) {
      $form_state->setErrorByName('account_id', $this->t('Account ID is incorrect. '.$form_state->getValue('account_id')));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    // TODO: Edit config file in Drupal DATABASE
    $pluginUpdated = [];
    $plugin = $this->pluginManagerPaymentProcessor->createInstance('pay_pal', ['of' => 'configuration values']);

    //$item_name = $form_state->getValue('test_item');
    //drupal_set_message('Account id got by Alvaro: '.$form_state->getValue('account_id'));

    $pluginUpdated = array(
      'account_id' => $form_state->getValue('account_id'),
      'pay_pal_email' => $form_state->getValue('pay_pal_email'),
      'currency' => $form_state->getValue('currency'),
      'version' => $form_state->getValue('version'),
      'frequency' => $form_state->getValue('frequency'),
    );

    $plugin->editPluginInformation($pluginUpdated);
  }

}
