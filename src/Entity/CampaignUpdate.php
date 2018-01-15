<?php

namespace Drupal\campaign_kit\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Campaign update entity.
 *
 * @ingroup campaign_kit
 *
 * @ContentEntityType(
 *   id = "campaign_update",
 *   label = @Translation("Campaign update"),
 *   bundle_label = @Translation("Campaign update type"),
 *   handlers = {
 *     "storage" = "Drupal\campaign_kit\CampaignUpdateStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\campaign_kit\CampaignUpdateListBuilder",
 *     "views_data" = "Drupal\campaign_kit\Entity\CampaignUpdateViewsData",
 *     "translation" = "Drupal\campaign_kit\CampaignUpdateTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\campaign_kit\Form\CampaignUpdateForm",
 *       "add" = "Drupal\campaign_kit\Form\CampaignUpdateForm",
 *       "edit" = "Drupal\campaign_kit\Form\CampaignUpdateForm",
 *       "delete" = "Drupal\campaign_kit\Form\CampaignUpdateDeleteForm",
 *     },
 *     "access" = "Drupal\campaign_kit\CampaignUpdateAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\campaign_kit\CampaignUpdateHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "campaign_update",
 *   data_table = "campaign_update_field_data",
 *   revision_table = "campaign_update_revision",
 *   revision_data_table = "campaign_update_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer campaign update entities",
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
 *     "canonical" = "/admin/structure/campaign_update/{campaign_update}",
 *     "add-page" = "/admin/structure/campaign_update/add",
 *     "add-form" = "/admin/structure/campaign_update/add/{campaign_update_type}",
 *     "edit-form" = "/admin/structure/campaign_update/{campaign_update}/edit",
 *     "delete-form" = "/admin/structure/campaign_update/{campaign_update}/delete",
 *     "version-history" = "/admin/structure/campaign_update/{campaign_update}/revisions",
 *     "revision" = "/admin/structure/campaign_update/{campaign_update}/revisions/{campaign_update_revision}/view",
 *     "revision_revert" = "/admin/structure/campaign_update/{campaign_update}/revisions/{campaign_update_revision}/revert",
 *     "revision_delete" = "/admin/structure/campaign_update/{campaign_update}/revisions/{campaign_update_revision}/delete",
 *     "translation_revert" = "/admin/structure/campaign_update/{campaign_update}/revisions/{campaign_update_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/campaign_update",
 *   },
 *   bundle_entity_type = "campaign_update_type",
 *   field_ui_base_route = "entity.campaign_update_type.edit_form"
 * )
 */
class CampaignUpdate extends RevisionableContentEntityBase implements CampaignUpdateInterface {

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

    // If no revision author has been set explicitly, make the campaign_update owner the
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
      ->setDescription(t('The user ID of author of the Campaign update entity.'))
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
        'max_length' => 120,
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

    $fields['campaign_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Campaign'))
      ->setDescription('')
      ->setSetting('target_type', 'campaign')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => -3,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['campaign_update'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Message'))
      ->setDescription('')
      ->setSettings(array(
        'default_value' => '',
        'text_processing' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'text_textarea',
        'weight' => 4,
        'settings' => array(
          'rows' => 4,
        ),
      ))
      ->setDisplayOptions('view', array(
        'type' => 'text_textarea',
        'weight' => 4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Campaign update is published.'))
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
