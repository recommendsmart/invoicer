<?php

namespace Drupal\invoicer\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Witget for line_item elements.
 *
 * @FieldWidget(
 *   id = "line_item_widget",
 *   label = @Translation("Line item widget"),
 *   field_types = {
 *     "line_item"
 *   }
 * )
 */
class LineItemWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $elements['item'] = [
      '#type' => 'textfield',
      '#title' => t('Description'),
      '#default_value' => $items->get($delta)->get('item')->getValue(),
      '#size' => 30,
      '#maxlength' => 128,
    ];

    $quantity = $items->get($delta)->get('quantity')->getValue();
    $quantity = (!is_null($quantity) ? $quantity : 1);
    $elements['quantity'] = [
      '#type' => 'number',
      '#title' => t('Quantity'),
      '#default_value' => $quantity,
      '#size' => 2,
      '#scale' => 2,
      '#step' => 0.01,
      '#maxlength' => 4,
      '#attributes' => ['class' => ['quantity']],
    ];

    $amount = $items->get($delta)->get('amount')->getValue();
    $amount = (!is_null($amount) ? $amount : 0);
    $elements['amount'] = [
      '#type' => 'number',
      '#title' => t('Unit Price'),
      '#default_value' => $amount,
      '#size' => 4,
      '#scale' => 2,
      '#maxlength' => 6,
      '#step' => 0.01,
      '#attributes' => ['class' => ['amount']],
    ];

    $gst = $items->get($delta)->get('gst')->getValue();
    $gst = (!is_null($gst) ? $gst : 0);

    $config = \Drupal::service('config.factory')->get('invoicer.settings');
    $gstOptions = $config->get('gst_types');
    $options = [];
    foreach ($gstOptions as $gstOption) {
      $value = $gstOption['value'];
      $gstType = $gstOption['value_label'];
      $options[$value] = $gstType;
    };

    $elements['gst'] = [
      '#type' => 'select',
      '#title' => t('GST'),
      '#default_value' => $gst,
      '#scale' => '4',
      '#options' => $options,
      '#attributes' => ['class' => ['gst']],
    ];

	$elements['base_price'] = [
      '#type' => 'number',
      '#title' => t('Price before Tax'),
      '#default_value' => $quantity * $amount,
      '#step' => 0.01,
      '#size' => 20,
      '#scale' => 2,
      '#maxlength' => 10,
      '#step' => 0.01,
      '#attributes' => ['class' => ['base_price']],
    ];

    $elements['total_price'] = [
      '#type' => 'number',
      '#title' => t('Price with Tax'),
      '#default_value' => $quantity * $amount * (1 + $gst * (0.01)),
      '#size' => 20,
      '#scale' => 2,
      '#step' => 0.01,
      '#maxlength' => 12,
      '#attributes' => ['class' => ['total_price']],
    ];
    return $elements;
  }

}
