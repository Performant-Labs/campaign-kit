<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Campaign donation entity.
 *
 * @ingroup campaign_kit
 *
 * @ContentEntityType(
 *   id = "campaign_donation",
 *   label = @Translation("Campaign donation"),
 *   bundle_label = @Translation("Campaign donation type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\campaign_kit\CampaignDonationListBuilder",
 *     "views_data" = "Drupal\campaign_kit\Entity\CampaignDonationViewsData",
 *     "translation" = "Drupal\campaign_kit\CampaignDonationTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\campaign_kit\Form\CampaignDonationForm",
 *       "add" = "Drupal\campaign_kit\Form\CampaignDonationForm",
 *       "edit" = "Drupal\campaign_kit\Form\CampaignDonationForm",
 *       "delete" = "Drupal\campaign_kit\Form\CampaignDonationDeleteForm",
 *     },
 *     "access" = "Drupal\campaign_kit\CampaignDonationAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\campaign_kit\CampaignDonationHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "campaign_donation",
 *   data_table = "campaign_donation_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer campaign donation entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/campaign_donation/{campaign_donation}",
 *     "add-page" = "/admin/structure/campaign_donation/add",
 *     "add-form" = "/admin/structure/campaign_donation/add/{campaign_donation_type}",
 *     "edit-form" = "/admin/structure/campaign_donation/{campaign_donation}/edit",
 *     "delete-form" = "/admin/structure/campaign_donation/{campaign_donation}/delete",
 *     "collection" = "/admin/structure/campaign_donation",
 *   },
 *   bundle_entity_type = "campaign_donation_type",
 *   field_ui_base_route = "entity.campaign_donation_type.edit_form"
 * )
 */
class CampaignDonation extends ContentEntityBase implements CampaignDonationInterface {

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
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
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
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Campaign donation entity.'))
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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 60,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['payment_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Payment Status'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 30,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['anonymous'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Anonymous'))
      ->setDescription('')
      ->setSetting('on_label', t("Label that actually shows up on the form"))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'boolean',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'boolean',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('First Name'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 30,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Last Name'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 30,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['mail'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
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
        'weight' => 2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['amount'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Payment amount'))
      ->setDescription('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'decimal',
        'weight' => 3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'decimal',
        'weight' => 3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['campaign_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Campaign ID'))
      ->setDescription('')
      ->setSetting('target_type', 'campaign')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 3,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['payment_txn_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Payment Txn ID'))
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

    $fields['currency'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Currency'))
      ->setDescription('')
      ->setRevisionable(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 6,
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

    $fields['referred_by'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Referred by'))
      ->setDescription('')
      ->setRevisionable(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 250,
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

    $fields['title'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Title'))
      ->setDescription('')
      ->setSettings(array(
        'max_length' => 10,
        'text_processing' => 0,
        'allowed_values' => array(
          'mr' => 'Mr.',
          'ms' => 'Ms.',
          'mrs' => 'Mrs.',
          'miss' => 'Miss',
          'dr' => 'Dr.',
        ),
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => 5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['opt_in'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Opt in'))
      ->setDescription('')
      ->setSetting('on_label', t("Label that actually shows up on the form"))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'boolean',
        'weight' => 5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'boolean',
        'weight' => 5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['payment_processor'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Payment processor"))
      ->setDescription('')
      ->setSettings([
        'max_length' => 60,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => '',
        ),
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['dedicate_donation'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Dedicate donation'))
      ->setDescription('')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'boolean',
        'weight' => 5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'settings' => array(
          'display_label' => TRUE,
        ),
        'weight' => 5,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['dedicate_type'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Type'))
      ->setDescription('')
      ->setSettings(array(
        'max_length' => 20,
        'text_processing' => 0,
        'allowed_values' => array(
          'in_honor_of' => 'In honor of..',
          'in_memory_of' => 'In memory of..',
        ),
      ))
      ->setDefaultValue('usd')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => 5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['honoree_first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Honoree's First Name"))
      ->setDescription('')
      ->setSettings([
        'max_length' => 30,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => 'First Name',
        ),
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['honoree_last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Honoree's Last Name"))
      ->setDescription('')
      ->setSettings([
        'max_length' => 30,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => 'Last Name',
        ),
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['recipient_mail'] = BaseFieldDefinition::create('email')
      ->setLabel(t("Recipient's info "))
      ->setDescription('')
      ->setSettings(array(
        'max_length' => 120,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => 'Email address',
        ),
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['recipient_first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Recipient's First Name"))
      ->setDescription('')
      ->setSettings([
        'max_length' => 30,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => 'First Name',
        ),
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['recipient_last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Recipient's Last Name"))
      ->setDescription('')
      ->setSettings([
        'max_length' => 30,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => 'Last Name',
        ),
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['recipient_message'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Your Message to the Recipient'))
      ->setDescription('')
      ->setSettings(array(
        'default_value' => '',
        'text_processing' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'text_textarea',
        'weight' => 5,
        'settings' => array(
          'rows' => 4,
        ),
      ))
      ->setDisplayOptions('view', array(
        'type' => 'text_textarea',
        'weight' => 5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Campaign donation is published.'))
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
