<?php

namespace Drupal\invoicer\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Invoice entity.
 *
 * @ingroup invoicer
 *
 * @ContentEntityType(
 *   id = "invoice",
 *   label = @Translation("Invoice"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\invoicer\InvoiceListBuilder",
 *     "views_data" = "Drupal\invoicer\Entity\InvoiceViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\invoicer\Form\InvoiceForm",
 *       "add" = "Drupal\invoicer\Form\InvoiceForm",
 *       "edit" = "Drupal\invoicer\Form\InvoiceForm",
 *       "delete" = "Drupal\invoicer\Form\InvoiceDeleteForm",
 *     },
 *     "access" = "Drupal\invoicer\InvoiceAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\invoicer\InvoiceHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "invoice",
 *   admin_permission = "administer Invoice entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/invoices/invoice/{invoice}",
 *     "add-form" = "/invoices/invoice/add",
 *     "edit-form" = "/invoices/invoice/{invoice}/edit",
 *     "delete-form" = "/invoices/invoice/{invoice}/delete",
 *     "collection" = "/invoices/invoice",
 *   },
 *   field_ui_base_route = "invoice.settings"
 * )
 */
class Invoice extends ContentEntityBase implements InvoiceInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
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
      ->setDescription(t('The user ID of author of the Invoice entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Invoice entity.'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
      ->setDescription(t('Date of the invoice.'))
      ->setSetting('datetime_type', 'date')
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'label' => 'above',
        'weight' => -4,
        'type' => 'datetime_default',
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['number'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Number'))
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'number',
        'weight' => -4,
      ])
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['series'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Series'))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Is payed'))
      ->setDescription(t('A boolean indicating whether the Invoice is payed.'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', array(
        'label' => 'above',
        'type' => 'boolean_checkbox',
        'weight' => -4,
      ))
      ->setDisplayOptions('view', array(
            'label' => 'above',
            'weight' => 0,
        ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Customer info.
    $fields['customer_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Customer ID'))
      ->setSettings(['max_length' => 16, 'text_processing' => 0])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['customer_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Customer name'))
      ->setSettings(['max_length' => 16])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['customer_address'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Customer address'))
      ->setSettings(['max_length' => 256, 'text_processing' => 1])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Provider info.
    $fields['provider_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Provider ID'))
      ->setSettings(['max_length' => 16, 'text_processing' => 0])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['provider_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Provider name'))
      ->setSettings(['max_length' => 16])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['provider_address'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Provider address'))
      ->setSettings(['max_length' => 256, 'text_processing' => 1])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Subtotal.
    $fields['sub_total'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Subtotal'))
      ->setSettings(['precision' => 32, 'scale' => 2])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['gst'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('GST'))
      ->setSettings(['precision' => 32, 'scale' => 2])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['total'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Total'))
      ->setSettings(['precision' => 32, 'scale' => 2])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // General.
    $fields['comments'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Comments'))
      ->setSettings(['max_length' => 256, 'text_processing' => 1])
      ->setDisplayOptions('form', ['label' => 'above'])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'label' => 'above',
        'weight' => 5,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    $date = strtotime($this->date->value);
    $this->set('series', date('Y', $date));
  }

}
