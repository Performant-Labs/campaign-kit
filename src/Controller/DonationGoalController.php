<?php

namespace Drupal\campaign_kit\Controller;

use Drupal\campaign_kit\Entity\CampaignDonation;
use Drupal\Core\Controller\ControllerBase;
use Drupal\campaign_kit\Entity\Campaign;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use \Drupal\Core\Entity\EntityTypeManagerInterface;

//Added
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Drupal\Core\Form\FormStateInterface;

// To create a link
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Class DonationGoalController.
 */
class DonationGoalController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The entity manager
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface.
   */
  protected $entityManager;

  /**
   * The entity form builder
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface.
   */
  protected $entityFormBuilder;

  /**
   * Constructs a new UserRegisterBlock plugin
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface|\Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entityFormBuilder
   *   The entity form builder.
   *
   * @internal param array $configuration A configuration array containing information about the plugin instance.*   A configuration array containing information about the plugin instance.
   * @internal param string $plugin_id The plugin_id for the plugin instance.*   The plugin_id for the plugin instance.
   * @internal param mixed $plugin_definition The plugin implementation definition.*   The plugin implementation definition.
   */
  public function __construct(EntityTypeManagerInterface $entityManager, EntityFormBuilderInterface $entityFormBuilder) {
    $this->entityManager = $entityManager;
    $this->entityFormBuilder = $entityFormBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @noinspection PhpParamsInspection */
    return new static(
      $container->get('entity.manager'),
      $container->get('entity.form_builder')
    );
  }

  /**
   * Campaign Kit Page administration.
   *
   * @return array|string.
   *
   */
  public function campaignKitPage() {
    $res['description']= [
      '#type' => 'markup',
      '#markup' => $this->t("Welcome to Campaign Admin page"),
    ];

    // Campaign list
    $campaignList = Url::fromRoute('entity.campaign.collection');
    $linkCampaignList = Link::fromTextAndUrl(t('Campaign List'), $campaignList);

    // Campaign type
    $campaignType = Url::fromRoute('entity.campaign_type.collection');
    $linkCampaignType = Link::fromTextAndUrl(t('Campaign type'), $campaignType);

    // Campaign Donation list
    $campaignDonationList = Url::fromRoute('entity.campaign_donation.collection');
    $linkCampaignDonationList = Link::fromTextAndUrl(t('Campaign donation list'), $campaignDonationList);

    // Campaign Donation type
    $campaignDonationType = Url::fromRoute('entity.campaign_donation_type.collection');
    $linkCampaignDonationType = Link::fromTextAndUrl(t('Campaign donation type'), $campaignDonationType);

    // Campaign Update list
    $campaignUpdateList = Url::fromRoute('entity.campaign_update.collection');
    $linkCampaignUpdateList = Link::fromTextAndUrl(t('Campaign update list'), $campaignUpdateList);

    // Campaign Update type
    $campaignUpdateType= Url::fromRoute('entity.campaign.collection');
    $linkCampaignUpdateType = Link::fromTextAndUrl(t('Campaign update type'), $campaignUpdateType);

    // Campaign Transaction list
    $campaignTransactionList = Url::fromRoute('entity.campaign_transaction.collection');
    $linkCampaignTransactionList = Link::fromTextAndUrl(t('Campaign transaction list'), $campaignTransactionList);

    // Campaign Update type
    $campaignTransactionType= Url::fromRoute('entity.campaign_transaction_type.collection');
    $linkCampaignTransactionType = Link::fromTextAndUrl(t('Campaign transaction type'), $campaignTransactionType);

    // Donation list
    $donationList = Url::fromRoute('entity.donation.collection');
    $linkDonationList = Link::fromTextAndUrl(t('Donation list'), $donationList);

    // Donation type
    $donationType = Url::fromRoute('entity.donation_type.collection');
    $linkDonationType = Link::fromTextAndUrl(t('Donation type'), $donationType);

    $linksCampaignPages = array(
      $linkCampaignList,
      $linkCampaignType,
      $linkCampaignDonationList,
      $linkCampaignDonationType,
      $linkCampaignUpdateList,
      $linkCampaignUpdateType,
      $linkCampaignTransactionList,
      $linkCampaignTransactionType,
      $linkDonationList,
      $linkDonationType
    );

    foreach ($linksCampaignPages as $linksCampaignPage) {
      $rows[] = array(
        '#markup' => $linksCampaignPage
      );
    }

    // Build a render array which will be themed as a table with a pager.
    $res['pager_example'] = [
      '#rows' => $rows,
      '#header' => [t('Campaign links'),],
      '#type' => 'table',
      '#empty' => t('No content available.'),
    ];

    $res['plugin']= [
      '#type' => 'markup',
      '#markup' => $this->t("Plugin(s) available in Campaign Kit"),
    ];

    // Getting plugin(s)
    // Donation type
    $pluginPayPal = Url::fromRoute('campaign_kit.payment_processor_form', array('pluginId' => 'pay_pal'));
    $linkPluginPayPal = Link::fromTextAndUrl(t('PayPal plugin'), $pluginPayPal);

    $plugins = array(
      $linkPluginPayPal,
    );

    foreach ($plugins as $plugin) {
      $rowsPlugin[] = array(
        '#markup' => $plugin
      );
    }

    // Build a render array which will be themed as a table with a pager.
    $res['plugin_table'] = [
      '#rows' => $rowsPlugin,
      '#header' => [t('Campaign Kit - Payment Processor Plugin(s)'),],
      '#type' => 'table',
      '#empty' => t('No content available.'),
    ];

    return $res;
  }

  /**
   * View Campaign entity.
   *
   * @param $campaignId
   *
   * @return array | object.
   * Return Campaign array.
   */
  public function viewCampaign($campaignId) {
    //Local variables
    $campaign = array();

    //Check if the entity exists
    $ids = \Drupal::entityQuery('campaign')
      ->condition('id', $campaignId, '=')
      ->execute();
    //$AccountPlan = AccountPlan::load(reset($ids));

    $entityCampaign = Campaign::load($campaignId);
    if ($entityCampaign->getCampaignType() == 'child') {
      $parent = $entityCampaign->getParent();
      $parentId = $parent->id();
      $urlDonation = Url::fromRoute('campaign_kit.campaign_kit_controller_view_campaign_child_entity', array('campaignParentId' => $parentId, 'campaignChildId' => $campaignId));
      $urlString = $urlDonation->toString();
      return new RedirectResponse($urlString);
    }

    if ($ids) {
      //View builder
      $entityType = 'campaign';
      $view_mode = 'campaign_';

      // Fields to render
      $fields = array(
        'title',
        'parent_id',
        'campaign_type',
        'header_image',
        'mail',
        'description',
        'short_description',
      );

      // Get the Campaign entity object
      //$entityCampaign = \Drupal::entityTypeManager()->getStorage($entityType)->load($entityId);
      $entityCampaign = Campaign::load($campaignId);
      // Get the View for this Campaign entity object
      $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder($entityType);
      //$pre_render = $viewBuilder->view($entityCampaign, $view_mode);
      //$render_output = render($pre_render);

      // Build the Campaign array to render
      foreach ($fields as $field_name) {
        if ($entityCampaign->hasField($field_name) && $entityCampaign->access('view')) {
          $value = $entityCampaign->get($field_name);
          $output = $viewBuilder->viewField($value, $view_mode);
          $output['#cache']['tags'] = $entityCampaign->getCacheTags();
          $campaign[$field_name] = $output;
        }
      }
      //Get the campaign entity
      //Build the Campaign object
      $campaign['id'] = $entityCampaign->getCampaignID();
      $campaign['one_time_donation_goal'] = (int)$entityCampaign->getGoal();
      $campaign['monthly_donation_goal'] = (int)$entityCampaign->getGoalSustaining();
      $campaign['campaign_status'] = $entityCampaign->getCampaignStatus();
      $campaign['facebook_url'] = $entityCampaign->getFacebookPath();
      $campaign['twitter_url'] = $entityCampaign->getTwitterPath();
      $campaign['google_url'] = $entityCampaign->getGooglePath();
      $campaign['donation_frequency_allowed'] = $entityCampaign->getFrequencyAllowed();
      $campaign['campaign_type'] = $entityCampaign->getCampaignType();
      $campaign['daysLeft'] = $entityCampaign->getDaysLeft();
      $campaign['goal'] = $entityCampaign->getGoal();
      //TODO: change 'author' for 'author_name'
      $campaign['author'] = $entityCampaign->getOwnerName();

      // Get Campaign's money information
      $campaign['numSupporters'] = $entityCampaign->getNumberSupporters();
      $campaign['goalSaved'] = $entityCampaign->getRaisedAmount();
      $campaign['percentage'] = $entityCampaign->getPercentage();
      // Get currency from plugin
      $config = \Drupal::config('campaign_kit.paymentprocessor');
      $clientPlugin =  $config->get('campaign_kit');
      $campaign['currency'] = $clientPlugin['currency'];

      // Create links for Campaign Page
      // I want to participate link
      $urlParticipate = Url::fromRoute('campaign_kit.campaign_kit_controller_add_child_page', array('campaignParentId' => $campaign['id']));
      $linkParticipate = Link::fromTextAndUrl(t('I Want to participate!'), $urlParticipate);
      $campaign['participate_link'] = $linkParticipate->toRenderable();
      $campaign['participate_link']['#attributes'] = array('class' => array('donate-button'));

      // Donate link
      $urlDonation = Url::fromRoute('campaign_kit.donate_form_campaign', array('campaignId' => $campaign['id']));
      $linkDonation = Link::fromTextAndUrl(t('Donate Now'), $urlDonation);
      $campaign['donate_link'] = $linkDonation->toRenderable();
      $campaign['donate_link']['#attributes'] = array('class' => array('donate-button'));

      // Create social network links
      //$entityCampaign->getFacebookPath();
      $urlFacebook = Url::fromUri($entityCampaign->getFacebookPath());
      $linkFacebook = Link::fromTextAndUrl(t('Share on Facebook'), $urlFacebook);
      $campaign['facebook_link'] = $linkFacebook->toRenderable();
      $campaign['facebook_link']['#attributes'] = array('class' => array('facebook-icon'), 'target' => '_blank', 'title' => t('Share on Facebook'));

      $urlTwitter = Url::fromUri($entityCampaign->getTwitterPath());
      $linkTwitter = Link::fromTextAndUrl(t('Share on Twitter'), $urlTwitter);
      $campaign['twitter_link'] = $linkTwitter->toRenderable();
      $campaign['twitter_link']['#attributes'] = array('class' => array('twitter-icon'), 'target' => '_blank', 'title' => t('Share on Twitter'));

      $urlGoogle = Url::fromUri($entityCampaign->getGooglePath());
      $linkGoogle = Link::fromTextAndUrl(t('Share on Google+'), $urlGoogle);
      $campaign['google_link'] = $linkGoogle->toRenderable();
      $campaign['google_link']['#attributes'] = array('class' => array('google-icon'), 'target' => '_blank', 'title' => t('Share on Google +'));

      $urlFriend= Url::fromUri('mailto:?');
      $linkFriend = Link::fromTextAndUrl(t('Email a friend'), $urlFriend);
      $campaign['email_friend_link'] = $linkFriend->toRenderable();
      $campaign['email_friend_link']['#attributes'] = array('class' => array('email-icon'), 'title' => t('Email a friend'));

      // Create Subscribe link
      $urlSubscribe= Url::fromUri('https://www.facebook.com/');
      $linkSubscribe = Link::fromTextAndUrl(t('Subscribe to Updates'), $urlSubscribe);
      $campaign['subscribe_link'] = $linkSubscribe->toRenderable();
      $campaign['subscribe_link']['#attributes'] = array('class' => array('subscribe-button'), 'title' => t('Subscribe to Updates'));

      // Create Continue to homepage link
      $urlHome = Url::fromRoute('<front>');
      $linkHome = Link::fromTextAndUrl(t('Continue to homepage'), $urlHome);
      $campaign['home_page_link'] = $linkHome->toRenderable();
      $campaign['home_page_link']['#attributes'] = array('class' => array('default-button'), 'title' => t('Continue to homepage'));

      // Get views and add them to Campaign array
      $renderSupporters = views_embed_view('supporters', array('display_id' => 'block_1', 'check_access' => TRUE));
      $renderCampaignUpdates = views_embed_view('campaign_updates', array('display_id' => 'block_1', 'check_access' => TRUE));

      $campaign['supportersView'] = $renderSupporters;
      $campaign['updatesView'] = $renderCampaignUpdates;

    } else {
      drupal_set_message("Please review the campaign exists, we can't find the campaign given", 'error');
    }

    $res = array(
      '#theme' => 'campaign--parent--display',
      '#campaign' => $campaign,
    );

    return $res;
  }

  /**
   * View Campaign entity.
   *
   * @param $campaignParentId
   * @param $campaignChildId
   *
   * @return array Return Campaign array.
   * Return Campaign array.
   * @internal param $campaignId
   *
   */
  public function viewCampaignChild($campaignParentId, $campaignChildId) {
    //Local variables
    $campaign = array();

    // Check if the Campaign parent entity exists
    $idParent = \Drupal::entityQuery('campaign')
      ->condition('id', $campaignParentId, '=')
      ->condition('campaign_type', 'parent', '=')
      ->execute();
    if ($idParent) {
      // Check if the Campaign child entity exists
      $idChild = \Drupal::entityQuery('campaign')
        ->condition('id', $campaignChildId, '=')
        ->condition('campaign_type', 'child', '=')
        ->execute();
      //$AccountPlan = AccountPlan::load(reset($ids));

      if ($idChild) {
        // View builder
        $entityType = 'campaign';
        $view_mode = 'campaign_';

        // Fields to render
        $fields = array(
          'title',
          'parent_id',
          'campaign_type',
          'header_image',
          'mail',
          'description',
          'short_description',
        );

        // Get the Campaign entity object
        //$entityCampaign = \Drupal::entityTypeManager()->getStorage($entityType)->load($entityId);
        $entityCampaign = Campaign::load($campaignChildId);
        // Get the View for this Campaign entity object
        $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder($entityType);
        //$pre_render = $viewBuilder->view($entityCampaign, $view_mode);
        //$render_output = render($pre_render);

        // Build the Campaign array to render
        foreach ($fields as $field_name) {
          if ($entityCampaign->hasField($field_name) && $entityCampaign->access('view')) {
            $value = $entityCampaign->get($field_name);
            $output = $viewBuilder->viewField($value, $view_mode);
            $output['#cache']['tags'] = $entityCampaign->getCacheTags();
            $campaign[$field_name] = $output;
          }
        }
        // Get the campaign entity
        //$campaignLoaded = Campaign::load($campaignId);
        // Build the Campaign object
        $campaign['id'] = $entityCampaign->getCampaignID();
        $campaign['one_time_donation_goal'] = (int)$entityCampaign->getGoal();
        $campaign['monthly_donation_goal'] = (int)$entityCampaign->getGoalSustaining();
        $campaign['campaign_status'] = $entityCampaign->getCampaignStatus();
        $campaign['facebook_url'] = $entityCampaign->getFacebookPath();
        $campaign['twitter_url'] = $entityCampaign->getTwitterPath();
        $campaign['google_url'] = $entityCampaign->getGooglePath();
        $campaign['donation_frequency_allowed'] = $entityCampaign->getFrequencyAllowed();
        $campaign['campaign_type'] = $entityCampaign->getCampaignType();
        $campaign['daysLeft'] = $entityCampaign->getDaysLeft();
        $campaign['goal'] = (int)$entityCampaign->getGoal();
        //TODO: change 'author' for 'author_name'
        $campaign['author'] = $entityCampaign->getOwnerName();

        // Get Campaign's money information
        $campaign['numSupporters'] = $entityCampaign->getNumberSupporters();
        $campaign['goalSaved'] = $entityCampaign->getRaisedAmount();
        $campaign['percentage'] = $entityCampaign->getPercentage();
        // Get currency from plugin
        $config = \Drupal::config('campaign_kit.paymentprocessor');
        $clientPlugin =  $config->get('campaign_kit');
        $campaign['currency'] = $clientPlugin['currency'];

        // Create links for Campaign Page
        // Donate link
        $urlDonation = Url::fromRoute('campaign_kit.donate_form_campaign_child', array('campaignId' => $campaignParentId, 'campaignChildId' => $campaign['id']));
        $linkDonation = Link::fromTextAndUrl(t('Donate Now'), $urlDonation);
        $campaign['donate_link'] = $linkDonation->toRenderable();
        $campaign['donate_link']['#attributes'] = array('class' => array('donate-button'));

        // Create social network links
        //$entityCampaign->getFacebookPath();
        $urlFacebook = Url::fromUri($entityCampaign->getFacebookPath());
        $linkFacebook = Link::fromTextAndUrl(t('Share on Facebook'), $urlFacebook);
        $campaign['facebook_link'] = $linkFacebook->toRenderable();
        $campaign['facebook_link']['#attributes'] = array('class' => array('facebook-icon'), 'target' => '_blank', 'title' => t('Share on Facebook'));

        $urlTwitter = Url::fromUri($entityCampaign->getTwitterPath());
        $linkTwitter = Link::fromTextAndUrl(t('Share on Twitter'), $urlTwitter);
        $campaign['twitter_link'] = $linkTwitter->toRenderable();
        $campaign['twitter_link']['#attributes'] = array('class' => array('twitter-icon'), 'target' => '_blank', 'title' => t('Share on Twitter'));

        $urlGoogle = Url::fromUri($entityCampaign->getGooglePath());
        $linkGoogle = Link::fromTextAndUrl(t('Share on Google+'), $urlGoogle);
        $campaign['google_link'] = $linkGoogle->toRenderable();
        $campaign['google_link']['#attributes'] = array('class' => array('google-icon'), 'target' => '_blank', 'title' => t('Share on Google +'));

        $urlFriend= Url::fromUri('mailto:?');
        $linkFriend = Link::fromTextAndUrl(t('Email a friend'), $urlFriend);
        $campaign['email_friend_link'] = $linkFriend->toRenderable();
        $campaign['email_friend_link']['#attributes'] = array('class' => array('email-icon'), 'title' => t('Email a friend'));

        // Create Subscribe link
        $urlSubscribe= Url::fromUri('https://www.facebook.com/');
        $linkSubscribe = Link::fromTextAndUrl(t('Subscribe to Updates'), $urlSubscribe);
        $campaign['subscribe_link'] = $linkSubscribe->toRenderable();
        $campaign['subscribe_link']['#attributes'] = array('class' => array('subscribe-button'), 'title' => t('Subscribe to Updates'));

        // Create Continue to homepage link
        $urlHome = Url::fromRoute('<front>');
        $linkHome = Link::fromTextAndUrl(t('Continue to homepage'), $urlHome);
        $campaign['home_page_link'] = $linkHome->toRenderable();
        $campaign['home_page_link']['#attributes'] = array('class' => array('subscribe-icon'), 'title' => t('Continue to homepage'));

        // Render Supporters and Campaign Update views
        $renderSupporters = views_embed_view('supporters', array('display_id' => 'block_2', 'check_access' => TRUE));
        $renderCampaignUpdates = views_embed_view('campaign_updates', array('display_id' => 'block_2', 'check_access' => TRUE));
        $campaign['supportersView'] = $renderSupporters;
        $campaign['updatesView'] = $renderCampaignUpdates;

      } else {
        drupal_set_message("Please review the Child campaign", 'error');
      }
    } else {
      drupal_set_message("Please review the Parent ID campaign.", 'error');
    }

    // Response
    $res = array(
      '#theme' => 'campaign--child--display',
      '#campaignChild' => $campaign,
    );

    return $res;
  }

  /**
   * Badge page.
   *
   * @return array
   *   Return Template for Badge page.
   */
  public function badge() {
    $res = array(
      '#theme'=> 'campaign-badge',
    );

    return $res;
  }

  /**
   * Create Child Campaign page.
   *
   * @param $campaignParentId
   *
   * @return array.
   * @internal param $idCampaign
   *
   */
  public function addChild($campaignParentId) {

    $campaign = $this->entityManager->getStorage('campaign') ->create(array('type' => 'child'));
    $childCreateForm = $this->entityFormBuilder->getForm($campaign, 'child');

    /*
    // Option 1
    $entity = User::create();
    $user_form = \Drupal::service('entity.form_builder')->getForm($entity, 'default');

    $entityCampaign = Campaign::load(2);
    $build = array();
    $form = $this->entityManager
      ->getFormObject('campaign', 'standalone_campaign')
      ->setEntity($entityCampaign);
    $build[] = \Drupal::formBuilder()->getForm($form);

    // Option 2
    $build = array();
    $campaign = $this->entityManager->getStorage('campaign')
      ->load(2);
    $build[] = $this->entityFormBuilder->getForm($campaign, 'child_campaign');
    */

    $res = array(
      '#theme' => 'campaign--child--add',
      '#child_create_form' => $childCreateForm,
    );
    return $res;
  }

  /**
   * Thank you with Campaign ID page.
   *
   * @param $campaignId
   *
   * @param $campaignDonationId
   *
   * @return array.
   * Return Template thank you.
   */
  public function thankYouId($campaignId, $campaignDonationId) {
    $campaign = array();
    if (is_numeric($campaignId)){
      // Check if the entity exists
      $ids = \Drupal::entityQuery('campaign')
        ->condition('id', $campaignId, '=')
        ->execute();
      //$AccountPlan = AccountPlan::load(reset($ids));
    } else {
      drupal_set_message("Please review the campaign exists, we can't find the campaign given", 'error');
    }

    if ($ids) {
      // View builder
      $entityType = 'campaign';
      $view_mode = 'campaign_';

      // Fields to render
      $fields = array(
        'title',
        'parent_id',
        'campaign_type',
        'header_image',
        'mail',
        'thank_you_message',
        'donation_page_title',
        'donation_page_description',
      );

      // Get the Campaign entity object
      //$entityCampaign = \Drupal::entityTypeManager()->getStorage($entityType)->load($entityId);
      $entityCampaign = Campaign::load($campaignId);
      // Get the View for this Campaign entity object
      $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder($entityType);

      //$pre_render = $viewBuilder->view($entityCampaign, $view_mode);
      //$render_output = render($pre_render);

      // Build the array to render
      foreach ($fields as $field_name) {
        if ($entityCampaign->hasField($field_name) && $entityCampaign->access('view')) {
          $value = $entityCampaign->get($field_name);
          $output = $viewBuilder->viewField($value, $view_mode);
          $output['#cache']['tags'] = $entityCampaign->getCacheTags();
          $campaign[$field_name] = $output;
        }
      }
      // Check if it is a parent or child
      if ($entityCampaign->get('campaign_type')->value == 'child') {
        $parent = $entityCampaign->getParent();
        // Back to campaign link
        $urlBackToCampaign = Url::fromRoute('campaign_kit.campaign_kit_controller_view_campaign_child_entity', array(
          'campaignParentId' => $parent->id->value,
          'campaignChildId' => $campaignId,
        ));
        $linkBackToCampaign = Link::fromTextAndUrl(t('Back to campaign page!'), $urlBackToCampaign);
        $campaign['back_to_campaign_link'] = $linkBackToCampaign->toRenderable();
        $campaign['back_to_campaign_link']['#attributes'] = array('class' => array('donate-button', 'campaign-default--button'));

      } else {
        // Back to campaign link
        $urlBackToCampaign = Url::fromRoute('campaign_kit.campaign_kit_controller_view_campaign_entity', array(
          'campaignId' => $campaignId
        ));
        $linkBackToCampaign = Link::fromTextAndUrl(t('Back to campaign page!'), $urlBackToCampaign);
        $campaign['back_to_campaign_link'] = $linkBackToCampaign->toRenderable();
        $campaign['back_to_campaign_link']['#attributes'] = array('class' => array('donate-button', 'campaign-default--button'));
      }

      // Query Campaign Donation fields.
      $idCampaignDonation = \Drupal::entityQuery('campaign_donation')
        //->condition('first_name', 'Andre CK', '=')
        ->condition('id', $campaignDonationId, '=')
        ->condition('campaign_id', $campaignId, '=')
        ->execute();

      if ($idCampaignDonation){
        // Load Campaign Donation entity
        $entityCampaignDonation = CampaignDonation::load(reset($idCampaignDonation));

        //View builder
        $entityCampaignDonationType = 'campaign_donation';
        $viewModeCampaignDonation = 'default';

        // Fields to render
        $fieldsCampaignDonation = array(
          'payment_processor',
          'recipient_message',
          //'dedicate_donation',
        );

        $viewBuilderCampaignDonation = \Drupal::entityTypeManager()->getViewBuilder($entityCampaignDonationType);

        foreach ($fieldsCampaignDonation as $field_name) {
          if ($entityCampaignDonation->hasField($field_name) && $entityCampaignDonation->access('view')) {
            $value2 = $entityCampaignDonation->get($field_name);
            $output2 = $viewBuilderCampaignDonation->viewField($value2, $viewModeCampaignDonation);
            $output2['#cache']['tags'] = $entityCampaignDonation->getCacheTags();
            $campaign[$field_name] = $output2;
          }
        }

        if ($entityCampaignDonation->get('dedicate_donation')->value == '1'){
          $campaign['dedicate_donation'] = true;
        } else {
          $campaign['dedicate_donation'] = false;
        }
        // Getting just values
        $campaign['dedicate_type'] = $entityCampaignDonation->get('dedicate_type')->value;
        $campaign['honoree_first_name'] = $entityCampaignDonation->get('honoree_first_name')->value;
        $campaign['honoree_last_name'] = $entityCampaignDonation->get('honoree_last_name')->value;
        $campaign['recipient_mail'] = $entityCampaignDonation->get('recipient_mail')->value;
        $campaign['recipient_first_name'] = $entityCampaignDonation->get('recipient_first_name')->value;
        $campaign['recipient_last_name'] = $entityCampaignDonation->get('recipient_last_name')->value;
        //$campaign['recipient_message'] = $entityCampaignDonation->get('recipient_message')->value;

      } else {
        drupal_set_message("There is no donations for this campaign", 'error');
      }

    } else {
      drupal_set_message("Please review the campaign exists, we can't find the campaign given", 'error');
    }
    $res = array(
      '#theme'=> 'campaign--thank-you',
      '#campaign' => $campaign,
    );

    return $res;
  }

  /**
   * @param $campaignParentId
   * @param $campaignChildId
   *
   * @param $campaignDonationId
   *
   * @return array
   */
  public function thankYouChild($campaignParentId, $campaignChildId, $campaignDonationId) {

    $campaign = array();
    if (is_numeric($campaignParentId)){
      //Check if the entity exists
      $idParent = \Drupal::entityQuery('campaign')
        ->condition('id', $campaignParentId, '=')
        ->execute();
      //$AccountPlan = AccountPlan::load(reset($ids));
    } else {
      drupal_set_message("Please review the campaign exists, we can't find the campaign given", 'error');
    }

    if ($idParent) {
      //Check if the Campaign child entity exists
      $idChild = \Drupal::entityQuery('campaign')
        ->condition('id', $campaignChildId, '=')
        ->condition('campaign_type', 'child', '=')
        ->execute();
      //$AccountPlan = AccountPlan::load(reset($ids));

      if ($idChild) {
        //View builder
        $entityType = 'campaign';
        $view_mode = 'campaign_';

        // Fields to render
        $fields = [
          'title',
          'parent_id',
          'campaign_type',
          'header_image',
          'mail',
          'thank_you_message',
          'donation_page_title',
          'donation_page_description',
        ];

        // Get the Campaign entity object
        //$entityCampaign = \Drupal::entityTypeManager()->getStorage($entityType)->load($entityId);
        $entityCampaign = Campaign::load($campaignChildId);
        // Get the View for this Campaign entity object
        $viewBuilder = \Drupal::entityTypeManager()
          ->getViewBuilder($entityType);

        //$pre_render = $viewBuilder->view($entityCampaign, $view_mode);
        //$render_output = render($pre_render);

        // Build the array to render
        foreach ($fields as $field_name) {
          if ($entityCampaign->hasField($field_name) && $entityCampaign->access('view')) {
            $value = $entityCampaign->get($field_name);
            $output = $viewBuilder->viewField($value, $view_mode);
            //$output['#cache']['tags'] = $entityCampaign->getCacheTags();
            $campaign[$field_name] = $output;
          }
        }

        // Check if it is a parent or child
        if ($entityCampaign->get('campaign_type')->value == 'child') {
          $parent = $entityCampaign->getParent();
          // Back to campaign link
          $urlBackToCampaign = Url::fromRoute('campaign_kit.campaign_kit_controller_view_campaign_child_entity', [
            'campaignParentId' => $parent->id->value,
            'campaignChildId' => $campaignChildId,
          ]);
          $linkBackToCampaign = Link::fromTextAndUrl(t('Back to campaign page!'), $urlBackToCampaign);
          $campaign['back_to_campaign_link'] = $linkBackToCampaign->toRenderable();
          $campaign['back_to_campaign_link']['#attributes'] = [
            'class' => [
              'donate-button',
              'campaign-default--button'
            ]
          ];
        }

        // Query Campaign Donation fields.
        $idCampaignDonation = \Drupal::entityQuery('campaign_donation')
          //->condition('first_name', 'Andre CK', '=')
          ->condition('id', $campaignDonationId, '=')
          ->condition('campaign_id', $campaignChildId, '=')
          ->execute();

        if ($idCampaignDonation) {
          // Load Campaign Donation entity
          $entityCampaignDonation = CampaignDonation::load(reset($idCampaignDonation));

          //View builder
          $entityCampaignDonationType = 'campaign_donation';
          $viewModeCampaignDonation = 'default';

          // Fields to render
          $fieldsCampaignDonation = [
            'payment_processor',
            'recipient_message',
            //'dedicate_donation',
          ];

          $viewBuilderCampaignDonation = \Drupal::entityTypeManager()
            ->getViewBuilder($entityCampaignDonationType);

          foreach ($fieldsCampaignDonation as $field_name) {
            if ($entityCampaignDonation->hasField($field_name) && $entityCampaignDonation->access('view')) {
              $value2 = $entityCampaignDonation->get($field_name);
              $output2 = $viewBuilderCampaignDonation->viewField($value2, $viewModeCampaignDonation);
              $output2['#cache']['tags'] = $entityCampaignDonation->getCacheTags();
              $campaign[$field_name] = $output2;
            }
          }

          // Set True or False for 'dedicate_donation' field
          if ($entityCampaignDonation->get('dedicate_donation')->value == '1') {
            $campaign['dedicate_donation'] = TRUE;
          }
          else {
            $campaign['dedicate_donation'] = FALSE;
          }
          // TODO:Getting just values
          $campaign['dedicate_type'] = $entityCampaignDonation->get('dedicate_type')->value;
          $campaign['honoree_first_name'] = $entityCampaignDonation->get('honoree_first_name')->value;
          $campaign['honoree_last_name'] = $entityCampaignDonation->get('honoree_last_name')->value;
          $campaign['recipient_mail'] = $entityCampaignDonation->get('recipient_mail')->value;
          $campaign['recipient_first_name'] = $entityCampaignDonation->get('recipient_first_name')->value;
          $campaign['recipient_last_name'] = $entityCampaignDonation->get('recipient_last_name')->value;
          //$campaign['recipient_message'] = $entityCampaignDonation->get('recipient_message')->value;

        } else {
          drupal_set_message("There is no donations for this campaign", 'error');
        }

      }
      else {
        drupal_set_message("Please review the Child campaign", 'error');
      }
    } else {
      drupal_set_message("Please review the Parent ID campaign.", 'error');
    }

    //Response
    $res = array(
      '#theme' => 'campaign--child--thank-you',
      '#campaignChild' => $campaign,
    );

    return $res;
  }

  /**
   * Validate the campaign_standalone_campaign_form.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public static function campaign_standalone_campaign_form_validate(&$form, FormStateInterface $form_state) {
    //drupal('Validate from controller class');
    //if ((int)$form_state->getValue('field_campaign_amount') <= 50){
    //$form_state->setErrorByName('field_campaign_amount', t('The amount must be greater than zero.'));
    //}
    if ($form_state->getValue('title') === "amigos"){
      $form_state->setErrorByName('title', t('The name must be different.'));
    }
    //if ($form_state->getValue('payment_processor') != "other"){
    //$form_state->setErrorByName('payment_processor', t('The pp must be different.'));
    //}
    drupal_set_message('Validate campaign.');
  }

  /**
   * Validate the campaign_standalone_campaign_form.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public static function campaign_standalone_campaign_form_submit(&$form, FormStateInterface $form_state) {
    drupal_set_message('after_build campaign.');
  }


  /**
   * Returns Campaign title.
   *
   * @param $campaignId
   *
   * @return string
   */
  public function getCampaignTitle($campaignId) {

    //Check if the entity exists
    $ids = \Drupal::entityQuery('campaign')
      ->condition('id', $campaignId, '=')
      ->execute();
    //$AccountPlan = AccountPlan::load(reset($ids));

    if($ids) {
      $entityCampaign = Campaign::load($campaignId);

      return $entityCampaign->get('title')->value;
    } else {
      return '';
    }
  }
}
