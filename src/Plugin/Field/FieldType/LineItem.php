<?php

namespace Drupal\invoicer\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Provides a field type of items for the invoices.
 *
 * @FieldType(
 *   id = "line_item",
 *   label = @Translation("Line item field"),
 *   default_widget = "line_item_widget",
 *   default_formatter = "line_item_formatter"
 * )
 */
class LineItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'item' => [
          'type' => 'text',
          'size' => 'small',
          'not null' => FALSE,
        ],
        'quantity' => [
          'type' => 'float',
          'not null' => FALSE,
        ],
        'amount' => [
          'type' => 'float',
          'not null' => FALSE,
        ],
        'gst' => [
          'type' => 'float',
          'not null' => FALSE,
        ],
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('item')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['item'] = DataDefinition::create('string')
      ->setLabel(t('Item description'));

    $properties['quantity'] = DataDefinition::create('float')
      ->setLabel(t('Item quantity'));

    $properties['amount'] = DataDefinition::create('float')
      ->setLabel(t('Item amount'));

    $properties['gst'] = DataDefinition::create('float')
      ->setLabel(t('Item gst'));

    return $properties;
  }

}
