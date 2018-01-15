<?php

namespace Drupal\campaign_kit\Form;

use Drupal\campaign_kit\Entity\Campaign;
use Drupal\campaign_kit\Entity\CampaignTransaction;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

// Added
use \Drupal\campaign_kit\Entity\CampaignDonation;

/**
 * Class DonateForm.
 */
class DonateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'donate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $campaignId = NULL, $campaignChildId = NULL) {
    // Check if the Campaign is 'standalone or parent or child'
    if($campaignId && $campaignChildId) {
      //drupal_set_message('Campaign - ID: '.$campaignId);
      //drupal_set_message('Campaign child - ID: '.$campaignChildId);
      $form['campaign_type'] = array(
        '#type' => 'hidden',
        '#value' => 'child',
      );
      $campaignChild = Campaign::load($campaignChildId);
      $campaignParentId = $campaignChild->getParent();
      $form['parent_id'] = array(
        '#type' => 'hidden',
        '#value' => $campaignParentId->id(),
      );
    } else {
      //drupal_set_message('Campaign - ID: '.$campaignId);
      $form['campaign_type'] = array(
        '#type' => 'hidden',
        '#value' => 'standalone',
      );
    }

    // Get currency from plugin
    $config = \Drupal::config('campaign_kit.paymentprocessor');
    $clientPlugin =  $config->get('campaign_kit');
    $clientPlugin['currency'];
    // pay_pal_email

    // frequency
    switch ($clientPlugin['frequency']){
      case 'onetime':
        $frequency = array(
          'onetime' => $this->t('One-time'),
        );
        break;
      case 'monthly':
        $frequency = array(
          'monthly' => $this->t('Monthly'),
        );
        break;
      case 'both_or':
        $frequency = array(
          //'other' => $this->t('Other'),
          'onetime' => $this->t('One-time'),
          'monthly' => $this->t('Monthly'),
        );
        break;
    }

    // Donation fields
    $form['amount'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Select amount'),
    ];

    // Testing PayPal
    $form['amount']['cmd'] = array(
      '#type' => 'hidden',
      '#value' => '_xclick',
    );

    $form['amount']['no_note'] = array(
      '#type' => 'hidden',
      '#value' => '1',
    );

    // TODO: Ask Andre about this
    $form['amount']['lc'] = array(
      '#type' => 'hidden',
      '#value' => 'US',
    );

    $form['amount']['currency_code'] = array(
      '#type' => 'hidden',
      '#value' => $clientPlugin['currency'],
    );

    $form['amount']['bn'] = array(
      '#type' => 'hidden',
      '#value' => 'PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest',
    );

    // If it's a child or standalone/parent
    if($campaignId && $campaignChildId) {
      $form['amount']['item_number'] = array(
        '#type' => 'hidden',
        '#value' => $campaignChildId,
      );

      $form['amount']['test_item'] = array(
        '#type' => 'hidden',
        '#value' => 'Campaign kit '.$campaignChildId,
      );
    } else {
      $form['amount']['item_number'] = array(
        '#type' => 'hidden',
        '#value' => $campaignId,
      );

      $form['amount']['test_item'] = array(
        '#type' => 'hidden',
        '#value' => 'Campaign kit '.$campaignId,
      );
    }

    $form['amount']['txn_id'] = array(
      '#type' => 'hidden',
      '#value' => 'txn_id',
    );

    $form['amount']['txn_type'] = array(
      '#type' => 'hidden',
      '#value' => 'txn_type',
    );

    $form['amount']['settings']['donation'] = array(
      '#type' => 'radios',
      //'#title' => $this->t('Poll status'),
      '#default_value' => 'other',
      '#options' => array(
        '25.00' => $this->t('$25'),
        '50.00' => $this->t('$50'),
        '100.00' => $this->t('$100'),
        '500.00' => $this->t('$500'),
        'other' => $this->t('Other'),
      ),
      '#attributes' => array(
        'onclick' => 'getdonationamount()'),
    );

    $form['amount']['amount'] = [
      '#type' => 'number',
      '#prefix' => '<div class="donation-data-amount">',
      '#title' => t('Your donation'),
      '#required' => TRUE,
      '#field_suffix' => $clientPlugin['currency']
    ];

    // TODO: Update this with plugin
    $form['amount']['recurrence']['time'] = array(
      '#type' => 'radios',
      //'#title' => $this->t('Poll status'),
      '#default_value' => $clientPlugin['frequency'],
      '#options' => $frequency,
      '#suffix' => '</div>',
    );

    // CheckBoxes.
    $form['amount']['dedication']['dedicate_donation'] = [
      '#type' => 'checkboxes',
      '#options' => ['1' => t('Dedication my donation in honor or in memory of someone')],
      '#attributes' => array(
        'onclick' => 'hasdedication()'),
    ];

    $form['dedication-information'] = [
      '#type' => 'fieldset',
    ];

    $form['dedication-information']['dedication-type']['dedicate_type'] = array(
      '#type' => 'radios',
      '#default_value' => '',
      '#options' => array(
        'in_honor_of' => $this->t('In honor of...'),
        'in_memory_of' => $this->t('In memory of...'),
      ),
    );

    $form['dedication-information']['honoree_first_name'] = array(
      '#type' => 'textfield',
      '#prefix' => '<div class="data-dedication-info">',
      '#title' => t("Honoree's Name "),
      '#attributes' => array('placeholder' => t('First Name')),
      //'#required' => TRUE,
    );

    $form['dedication-information']['honoree_last_name'] = array(
      '#type' => 'textfield',
      '#suffix' => '</div>',
      '#title' => t("Honoree's Last Name "),
      '#attributes' => array('placeholder' => t('Last Name')),
      //'#required' => TRUE,
    );

    // Email Recipient’s Info.
    $form['dedication-information']['recipient_mail'] = [
      '#type' => 'email',
      '#title' => t("Recipient's info"),
      '#description' => 'Optionally send a notification email',
      '#attributes' => array('placeholder' => t('Email Address')),
      //'#required' => TRUE,
    ];

    $form['dedication-information']['recipient_first_name'] = array(
      '#type' => 'textfield',
      '#prefix' => '<div class="data-dedication-info">',
      '#title' => t("Recipient's Name "),
      '#attributes' => array('placeholder' => t('First Name')),
      //'#required' => TRUE,
    );

    $form['dedication-information']['recipient_last_name'] = array(
      '#type' => 'textfield',
      '#suffix' => '</div>',
      '#title' => t("Recipient's Last Name "),
      '#attributes' => array('placeholder' => t('Last Name')),
      //'#required' => TRUE,
    );

    // Textarea.
    $form['dedication-information']['recipient_message'] = [
      '#type' => 'textarea',
      '#title' => t('Your Message to the Recipient'),
      '#description' => t('Please spell check, include how you would like your name(s) signed, mention the amount if desired, and format your message as you would like it delivered.'),
    ];

    $form['donor-information'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Your information'),
    ];

    // Select.
    $form['donor-information']['title'] = [
      '#type' => 'select',
      '#title' => $this->t('Title'),
      '#options' => [
        'mr' => $this->t('Mr.'),
        'ms' => $this->t('Ms.'),
        'mrs' => $this->t('Mrs.'),
        'miss' => $this->t('Miss'),
        'dr' => $this->t('Dr.'),
      ],
      '#required' => TRUE,
      '#empty_option' => $this->t('Please select'),
      //'#description' => $this->t('Select, #type = select'),
    ];

    $form['donor-information']['first_name'] = array(
      '#type' => 'textfield',
      '#title' => t('First Name '),
      '#default_value' => 'Campaign',
      '#required' => TRUE,
    );

    $form['donor-information']['last_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Last Name '),
      '#default_value' => 'Kit 2017',
      '#required' => TRUE,
    );

    $form['donor-information']['payer_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#default_value' => 'aangel-buyer@mac.com',
      '#field_suffix' => $this->t('Your receipt will be emailed here.'),
    ];
    /*
    $form['donor-information']['opt_in'] = array(
      '#type' => 'textfield',
      '#title' => t('Opt in'),
    );
    */

    $form['donor-information']['comment'] = array(
      '#type' => 'textfield',
      '#title' => t('Leave a comment'),
      '#field_suffix' => $this->t('100 Character Limit.'),
      '#maxlength' => '100'
    );

    $form['payment-details'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Payment Details'),
    ];

    $form['payment-details']['name'] = array(
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#required' => TRUE,
      '#default_value' => 'name',
    );

    $form['payment-details']['address'] = array(
      '#type' => 'textfield',
      '#title' => t('Address'),
      '#required' => TRUE,
    );

    $form['payment-details']['address2'] = array(
      '#type' => 'textfield',
      '#title' => t('Address 2'),
      //'#required' => TRUE,
    );

    $form['payment-details']['city'] = array(
      '#type' => 'textfield',
      '#title' => t('City'),
      //'#required' => TRUE,
    );

    $form['payment-details']['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => [
        'uruguay' => $this->t('Uruguay'),
        'colombia' => $this->t('Colombia'),
        'ecuador' => $this->t('Ecuador'),
        'chile' => $this->t('Chile'),
        'argentina' => $this->t('Argentina'),
      ],
      //'#required' => TRUE,
      '#empty_option' => $this->t('Please select'),
      //'#description' => $this->t('Select, #type = select'),
    ];

    $form['payment-details']['postal_code'] = array(
      '#type' => 'textfield',
      '#title' => t('Postal code'),
      //'#required' => TRUE,
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Send your gift'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    //if (strlen($form_state->getValue('quantity')) < 8) {
    //$form_state->setErrorByName('quantity', $this->t('Mobile number is too short.'));
    //}
    //if ($form_state->getValue('amount') <= 0){
    //$form_state->setErrorByName('amount', $this->t('The amount must be greater than zero.'));
    //}
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Trying things
    $schemeHttpHost = \Drupal::request()->getSchemeAndHttpHost();

    // Get currency from plugin
    $config = \Drupal::config('campaign_kit.paymentprocessor');
    $clientPlugin =  $config->get('campaign_kit');
    $clientPlugin['pay_pal_email'];

    // Create Campaign Donation entity
    $campaignDonation = CampaignDonation::create(
      array(
        'type' => 'campaign_donation',
        'name' => 'Campaign donation test',
        'user_id' => array(
          'target_id' => 1,
        ),
        'payment_status' => 'Pending',
        'anonymous' => 1,
        'first_name' => $form_state->getValue('first_name'),
        'last_name' => $form_state->getValue('last_name'),
        'mail' => $form_state->getValue('payer_email'),
        'amount' => $form_state->getValue('amount'),
        'campaign_id' => array(
          'target_id' => $form_state->getValue('item_number'),
        ),
        'payment_txn_id' => $form_state->getValue('payment_txn_id'),
        'currency' => $form_state->getValue('currency_code'),
        'referred_by' => 'referred_by',
        'title' => $form_state->getValue('title'),
        'opt_in' => 0,
        'payment_processor' => 'payment_processor',
        'dedicate_donation' => $form_state->getValue('dedicate_donation'),
        'dedicate_type' => $form_state->getValue('dedicate_type'),
        'honoree_first_name' => $form_state->getValue('honoree_first_name'),
        'honoree_last_name' => $form_state->getValue('honoree_last_name'),
        'recipient_mail' => $form_state->getValue('recipient_mail'),
        'recipient_first_name' => $form_state->getValue('recipient_first_name'),
        'recipient_last_name' => $form_state->getValue('recipient_last_name'),
        'recipient_message' => array(
          'value' => $form_state->getValue('recipient_message'),
          'format' => 'basic_html'
        ),
      )
    );
    $campaignDonation->save();

    $campaignDonationId = $campaignDonation->id();

    // Create Campaign Transaction entity
    $campaignTransaction = CampaignTransaction::create(
      array(
        'type' => 'campaign_transaction',
        'name' => $form_state->getValue('name'),
        'address_1' => $form_state->getValue('address'),
        'address_2' => $form_state->getValue('address2'),
        'city' => $form_state->getValue('city'),
        'country' => $form_state->getValue('country'),
        'postal_code' => $form_state->getValue('postal_code'),
        'campaign_id' => array(
          'target_id' => $form_state->getValue('item_number'),
        ),
      )
    );
    $campaignTransaction->save();

    /**
     * PayPal settings
     */
    // TODO updates this with plugin
    $paypal_email = $clientPlugin['pay_pal_email'];

    // TODO update with dynamic URL
    if ($form_state->getValue('campaign_type') == 'standalone') {
      $return_url = $schemeHttpHost."/campaign/".$form_state->getValue('item_number')."/thank-you/".$campaignDonationId; // OK
      $cancel_url = $schemeHttpHost.'/campaign/'.$form_state->getValue('item_number'); // OK
    } else {
      $return_url = $schemeHttpHost."/campaign/".$form_state->getValue('parent_id')."/child/".$form_state->getValue('item_number')."/thank-you/".$campaignDonationId; // OK
      $cancel_url = $schemeHttpHost.'/campaign/'.$form_state->getValue('parent_id')."/child/".$form_state->getValue('item_number'); // OK
    }
    $notify_url = $schemeHttpHost.'/payment_processor_ipn/notification/ipn';

    $item_name = $form_state->getValue('test_item');
    $item_amount = $clientPlugin['amount'];
    $item_currency = $clientPlugin['currency'];

    if (($form_state->getValue('txn_id') != '') && ($form_state->getValue('txn_type') != '')) {
      $querystring = '';
      // Firstly Append paypal account to querystring
      $querystring .= "?business=" . urlencode($paypal_email) . "&";

      // Append amount& currency (£) to querystring so it cannot be edited in html

      // The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
      $querystring .= "item_name=" . urlencode($item_name) . "&";
      $querystring .= "amount=" . urlencode($item_amount) . "&";
      $querystring .= "currency=" . urlencode($item_currency) . "&";

      // loop for posted values and append to querystring
      foreach ($_POST as $key => $value) {
        //$value = urlencode(stripslashes($value));
        $querystring .= $key . "=" . $form_state->getValue($key) . "&";
      }

      // Append paypal return addresses
      $querystring .= "return=" . urlencode(stripslashes($return_url)) . "&";
      $querystring .= "cancel_return=" . urlencode(stripslashes($cancel_url)) . "&";
      $querystring .= "notify_url=" . urlencode($notify_url);

      // Append querystring with custom field
      //$querystring .= "&custom=".USERID;

      // Redirect to paypal IPN
      //drupal_set_message($querystring);
      header('location:https://www.sandbox.paypal.com/cgi-bin/webscr' . $querystring);
      exit();
    }

    //$form_state->hasField('abc');
    if ($form_state->getValue('txn_id') != 'txn_id'){
      drupal_set_message('Yes, it has: '.$form_state->getValue('txn_id'));
    } else {
      drupal_set_message('No, it does not.');
    }
  }
}
