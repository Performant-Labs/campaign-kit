<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\campaign_kit\Entity\CampaignDonation;

/**
 * Defines the Campaign entity.
 *
 * @ingroup campaign_kit
 *
 * @ContentEntityType(
 *   id = "campaign",
 *   label = @Translation("Campaign"),
 *   bundle_label = @Translation("Campaign type"),
 *   handlers = {
 *     "storage" = "Drupal\campaign_kit\CampaignStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\campaign_kit\CampaignListBuilder",
 *     "views_data" = "Drupal\campaign_kit\Entity\CampaignViewsData",
 *     "translation" = "Drupal\campaign_kit\CampaignTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\campaign_kit\Form\CampaignForm",
 *       "add" = "Drupal\campaign_kit\Form\CampaignForm",
 *       "edit" = "Drupal\campaign_kit\Form\CampaignForm",
 *       "delete" = "Drupal\campaign_kit\Form\CampaignDeleteForm",
 *     },
 *     "access" = "Drupal\campaign_kit\CampaignAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\campaign_kit\CampaignHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "campaign",
 *   data_table = "campaign_field_data",
 *   revision_table = "campaign_revision",
 *   revision_data_table = "campaign_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer campaign entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/campaign/{campaign}",
 *     "add-page" = "/campaign/add",
 *     "add-form" = "/campaign/add/{campaign_type}",
 *     "edit-form" = "/campaign/{campaign}/edit",
 *     "delete-form" = "/campaign/{campaign}/delete",
 *     "version-history" = "/campaign/{campaign}/revisions",
 *     "revision" = "/campaign/{campaign}/revisions/{campaign_revision}/view",
 *     "revision_revert" = "/campaign/{campaign}/revisions/{campaign_revision}/revert",
 *     "revision_delete" = "/campaign/{campaign}/revisions/{campaign_revision}/delete",
 *     "translation_revert" = "/campaign/{campaign}/revisions/{campaign_revision}/revert/{langcode}",
 *     "collection" = "/campaign",
 *   },
 *   bundle_entity_type = "campaign_type",
 *   field_ui_base_route = "entity.campaign_type.edit_form"
 * )
 */
class Campaign extends RevisionableContentEntityBase implements CampaignInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the campaign owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('title', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * Custom
   * @return mixed
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * Custom
   * @param $name
   *
   * @return $this
   */
  public function setTitle($name) {
    $this->set('title', $name);
    return $this;
  }

  /**
   * Custom
   */
  public function getCampaignID() {
    return $this->get('id')->value;
  }

  /**
   * @return string
   */
  public function getCampaignType() {
    return $this->get('campaign_type')->value;
  }

  /**
   * Custom
   * @return mixed
   */
  public function getPath() {
    return $this->get('donate_path')->value;
  }

  /**
   * Custom Get goal amount one time
   * @return int
   */
  public function getGoal() {
    return (int)$this->get('one_time_donation_goal')->value;
  }

  /**
   * Custom Get goal amount monthly
   * @return int
   */
  public function getGoalSustaining() {
    return (int)$this->get('monthly_donation_goal')->value;
  }


  /**
   * Custom
   */
  public function getDateStart() {
    return $this->get('start_date')->value;
  }

  /**
   * Custom
   */
  public function getDaysLeft() {
    //Actual date
    $datetimeStart = new DrupalDateTime();
    //field_campaign_end_date field
    $datetimeEnd = new DrupalDateTime($this->get('end_date')->value);
    $interval = $datetimeStart->diff($datetimeEnd);

    //return $interval->format('%a');
    $positive = ($interval->format('%R') == '+')? True: False ;

    $daysLeft = (int) $interval->format('%a');
    if ($positive){
      if ($daysLeft > 0){
        $res = array(
          'days' => $daysLeft,
          'message' => $this->getDaysLeftMessage($daysLeft)
        );
      } else {
        $res = array(
          'days' => $daysLeft,
          'message' => $this->getDaysLeftMessage($daysLeft)
        );
      }
    } else {
      $daysLeftNegative = $daysLeft * (-1);
      $res = array(
        'days' => $daysLeftNegative,
        'message' => $this->getDaysLeftMessage($daysLeftNegative)
      );
    }
    return $res;
  }

  /**
   * Custom
   *
   * @param $daysLeft
   *
   * @return string
   */
  public function getDaysLeftMessage($daysLeft) {
    if ($daysLeft < 0) {
      $message = "Campaign Ended";
    }
    elseif ($daysLeft == 0) {
      $message = 'Today finishes';
    }
    elseif ($daysLeft == 1) {
      $message = 'Day left';
    } else {
      $message = 'Days left';
    }
    return $message;
  }

  /**
   * Custom
   */
  public function getCampaignStatus() {
    return $this->get('campaign_status')->value;
  }

  /**
   * Custom
   */
  public function getFacebookPath() {
    return $this->get('facebook_url')->value;
  }

  /**
   * Custom
   */
  public function getTwitterPath() {
    return $this->get('twitter_url')->value;
  }

  /**
   * Custom
   */
  public function getGooglePath() {
    return $this->get('google_url')->value;
  }

  /**
   * Custom
   */
  public function getFrequencyAllowed() {
    return $this->get('donation_frequency_allowed')->value;
  }

  /**
   * {@inheritdoc}
   * @return int
   * Return Parent ID.
   */
  public function getParent() {
    return $this->get('parent_id')->entity;
  }

  /**
   * Get number of Supporters/Donors
   */
  public function getNumberSupporters() {
    // TODO: getNumberSupportersMonthly function
    // Query number of supporters
    $campaignDonations = \Drupal::entityQuery('campaign_donation')
      ->condition('campaign_id', $this->get('id')->value, '=')
      ->condition('payment_status', 'Confirmed', '=')
      //->condition('anonymous', 0, '=')
      ->count()
      ->execute();
    return (int)$campaignDonations;
  }

  /**
   * Get percentage.
   *
   * @param $goal
   * @param $saved
   *
   * @return array|int
   * Return Percentage.
   */
  public function getPercentage(){
    // TODO: getPercentageMonthly function
    $saved = $this->getRaisedAmount();
    $goal = $this->getGoal();
    $percentage = ($saved * 100)/$goal;
    return (int)$percentage;
  }

  /**
   * @return float|int
   */
  public function getRaisedAmount() {
    // TODO: GET Campaign's 'saved' field of all Campaign Donation entities
    $idDonations = \Drupal::entityQuery('campaign_donation')
      ->condition('campaign_id', $this->get('id')->value, '=')
      ->condition('payment_status', 'Confirmed', '=')
      ->execute();
    $entries = CampaignDonation::loadMultiple($idDonations);

    $amountSaved = 0.0;
    foreach ($entries as $entry ) {
      //Here we can get whatever we want of each Campaign Donation entity
      $amountSaved += (float)$entry->get('amount')->value;
    }
    return $amountSaved;
  }

  /**
   * @return string
   */
  public function getOwnerName() {
    $fundraiser = $this->getOwner();
    if ($fundraiser->name->value != '') {
      $fundraiserName = $fundraiser->name->value;
    } else {
      $fundraiserName = "anonymous";
    }
    return $fundraiserName;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Campaign entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 120,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -22,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -22,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['campaign_type'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Campaign type'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setSettings(array(
        'max_length' => 16,
        'text_processing' => 0,
        'allowed_values' => array(
          'standalone' => 'Standalone',
          'parent' => 'Parent',
          'child' => 'Child',
        ),
      ))
      ->setDefaultValue('standalone')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -21,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => -21,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['campaign_status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Campaign status'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setSettings(array(
        'max_length' => 60,
        'text_processing' => 0,
        'allowed_values' => array(
          'open' => 'Open',
          'archived' => 'Archived',
          'closed' => 'Closed',
        ),
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -20,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => -20,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['team_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Team ID'))
      ->setDescription('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'integer',
        'weight' => -19,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -19,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['parent_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Parent campaign'))
      ->setDescription('')
      ->setSetting('target_type', 'campaign')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -18,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => -18,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['start_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Start date'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'adobe',
        'type' => 'datetime',
        'weight' => -17,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'datetime',
        'weight' => -17,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['end_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End date'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'adobe',
        'type' => 'datetime',
        'weight' => -16,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'datetime',
        'weight' => -16,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['short_description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Short description'))
      ->setDescription('')
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 200,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string',
        'weight' => -15,
      ))
      ->setDisplayOptions('view', array(
        'type' => 'string_textfield',
        'weight' => -15,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'text_processing' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'text_textarea',
        'weight' => -14,
        'settings' => array(
          'rows' => 4,
        ),
      ))
      ->setDisplayOptions('view', array(
        'type' => 'text_textarea',
        'weight' => -14,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['header_image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Header image'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label'   => 'above',
        'type'    => 'image',
        'weight'  => -13,
      ])
      ->setDisplayOptions('form', [
        'type'    => 'image_image',
        'weight'  => -13,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['confirmation_email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Confirmation email (admins)'))
      ->setDescription('')
      ->setRevisionable(TRUE)
      ->setSettings(array(
        'max_length' => 120,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -12,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -12,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['one_time_donation_goal_minimum'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Minimum one-time donation goal'))
      ->setDescription('')
      ->setDefaultValue(100)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'integer',
        'weight' => -11,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -11,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['monthly_donation_goal_minimum'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Minimum monthly donation goal'))
      ->setDescription('')
      ->setDefaultValue(100)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'integer',
        'weight' => -10,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -10,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['one_time_donation_goal_suggested'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Suggested one-time donation goal'))
      ->setDescription('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'integer',
        'weight' => -9,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -9,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['monthly_donation_goal_suggested'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Suggested monthly donation goal'))
      ->setDescription('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'integer',
        'weight' => -8,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -8,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['one_time_donation_goal'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('One-time donation goal'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'integer',
        'weight' => -7,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -7,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['monthly_donation_goal'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Monthly donation goal'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'integer',
        'weight' => -6,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -6,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['max_children'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Max. child campaigns'))
      ->setDescription('')
      ->setDefaultValue(100)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'integer',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'number',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['thank_you_message'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Thank-you message'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'text_processing' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'text_textarea',
        'weight' => -4,
        'settings' => array(
          'rows' => 4,
        ),
      ))
      ->setDisplayOptions('view', array(
        'type' => 'text_textarea',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['donate_path'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Donate path"))
      ->setDescription('')
      ->setSettings([
        'max_length' => 120,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => 'Type the donate url',
        ),
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['facebook_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Facebook URL'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 120,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => 'Type the Facebook url',
        ),
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['twitter_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Twitter URL'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 120,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => 'Type the Twitter url',
        ),
        'weight' => -1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['google_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Google Plus URL'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 120,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => 'Type the Google+ url',
        ),
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['donation_page_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Donation page title'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 120,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['donation_page_description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Donation page description'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'text_processing' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'text_textarea',
        'weight' => 2,
        'settings' => array(
          'rows' => 4,
        ),
      ))
      ->setDisplayOptions('view', array(
        'type' => 'text_textarea',
        'weight' => 2,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['donation_frequency_allowed'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Donation frequency allowed'))
      ->setDescription('')
      ->setRequired(TRUE)
      ->setSettings(array(
        'max_length' => 10,
        'text_processing' => 0,
        'allowed_values' => array(
          'onetime' => 'One time',
          'monthly' => 'Monthly',
          'both_or' => 'Both/Or',
        ),
      ))
      ->setDefaultValue('usd')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['campaign_external_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Campaign external ID'))
      ->setDescription('')
      ->setRevisionable(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 120,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => 4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => 4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Campaign is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
