<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Donation entity.
 *
 * @ingroup campaign_kit
 *
 * @ContentEntityType(
 *   id = "donation",
 *   label = @Translation("Donation"),
 *   bundle_label = @Translation("Donation type"),
 *   handlers = {
 *     "storage" = "Drupal\campaign_kit\DonationStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\campaign_kit\DonationListBuilder",
 *     "views_data" = "Drupal\campaign_kit\Entity\DonationViewsData",
 *     "translation" = "Drupal\campaign_kit\DonationTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\campaign_kit\Form\DonationForm",
 *       "add" = "Drupal\campaign_kit\Form\DonationForm",
 *       "edit" = "Drupal\campaign_kit\Form\DonationForm",
 *       "delete" = "Drupal\campaign_kit\Form\DonationDeleteForm",
 *     },
 *     "access" = "Drupal\campaign_kit\DonationAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\campaign_kit\DonationHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "donation",
 *   data_table = "donation_field_data",
 *   revision_table = "donation_revision",
 *   revision_data_table = "donation_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer donation entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/donation/{donation}",
 *     "add-page" = "/admin/structure/donation/add",
 *     "add-form" = "/admin/structure/donation/add/{donation_type}",
 *     "edit-form" = "/admin/structure/donation/{donation}/edit",
 *     "delete-form" = "/admin/structure/donation/{donation}/delete",
 *     "version-history" = "/admin/structure/donation/{donation}/revisions",
 *     "revision" = "/admin/structure/donation/{donation}/revisions/{donation_revision}/view",
 *     "revision_revert" = "/admin/structure/donation/{donation}/revisions/{donation_revision}/revert",
 *     "revision_delete" = "/admin/structure/donation/{donation}/revisions/{donation_revision}/delete",
 *     "translation_revert" = "/admin/structure/donation/{donation}/revisions/{donation_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/donation",
 *   },
 *   bundle_entity_type = "donation_type",
 *   field_ui_base_route = "entity.donation_type.edit_form"
 * )
 */
class Donation extends RevisionableContentEntityBase implements DonationInterface {

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

    // If no revision author has been set explicitly, make the donation owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
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
      ->setDescription(t('The user ID of author of the Donation entity.'))
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
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
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

    $fields['anonynous'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('anonynous'))
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

    $fields['amount_one_time'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Goal Amount One Time'))
      ->setDescription('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'decimal',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'decimal',
        'weight' => -3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['amount_monthly'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Goal Amount Monthly'))
      ->setDescription('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'decimal',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'decimal',
        'weight' => -3,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
      ->setDescription('')
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'adobe',
        'type' => 'datetime',
        'weight' => -2,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'datetime',
        'weight' => -2,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['customer_external_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Customer external ID'))
      ->setDescription('')
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 60,
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
        'weight' => -1,
      ])
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
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => 0,
      ))
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
        'weight' => 1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    /*
    $fields['telephone'] = BaseFieldDefinition::create('telephone')
      ->setLabel(t('Telephone Number'))
      ->setDescription((t('The Telephone Number of the Donation.')))
      ->setRevisionable(TRUE)
      ->setSettings(array(
        'max_length' => 30,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'telephone_default',
        'weight' => 1,
      ])
      ->setDisplayOptions('form', array(
        'type' => 'telephone_default',
        'weight' => 1,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);*/

    $fields['title'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Title'))
      ->setDescription('')
      ->setSettings(array(
        'max_length' => 6,
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
        'weight' => 2,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 2,
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
        'weight' => 2,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'boolean',
        'weight' => 2,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['address_1'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Address 1'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 120,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['address_2'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Address 2'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 120,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['apartment_suite'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Apartment suite'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 60,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['city'] = BaseFieldDefinition::create('string')
      ->setLabel(t('City'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 60,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['state'] = BaseFieldDefinition::create('string')
      ->setLabel(t('State'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 60,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['country'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Country'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 30,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['postal_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Postal Code'))
      ->setDescription('')
      ->setSettings([
        'max_length' => 30,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
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
        'weight' => 4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'settings' => array(
          'placeholder' => '',
        ),
        'weight' => 4,
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
      ->setDescription(t('A boolean indicating whether the Donation is published.'))
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
