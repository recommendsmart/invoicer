<?php

namespace Drupal\invoicer\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure file system settings for this site.
 */
class InvoicerConfigForm extends ConfigFormBase {
  protected $delimiter;
  protected $configuration;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
    $this->delimiter = "|";
    $this->configuration = $config_factory->getEditable('invoicer.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'invoicer_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['invoicer.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = array();

    $form['gst_types'] = array(
      '#type' => 'textarea',
      '#title' => t('GST types'),
      '#size' => 5000,
      '#maxlength' => 5000,
      '#default_value' => $this->getFormattedValues($this->configuration->get('gst_types')),
      '#description' => t('The GST types'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    foreach ($form_state->getValues() as $key => $value) {
      $values = NULL;
      if ($key == 'gst_types') {
        $gstTypes = explode("\n", $value);

        $result = [];
        foreach ($gstTypes as $index => $gstType) {
          $gstType = trim($gstType);

          $position = mb_strpos($gstType, $this->delimiter);

          $gst = trim(mb_substr($gstType, 0, $position));
          $gstTypeHumanReadable = trim(mb_substr($gstType, $position + 1, mb_strlen($gstType)));
          if (is_numeric($gst)) {
            $result[$index]['value'] = $gst;
            $result[$index]['value_label'] = $gstTypeHumanReadable;
          }
        }

        $values = $result;
      }

      $this->configuration->set($key, $values);
    }

    $this->configuration->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Convert a values array into a string to use in textareas.
   *
   * @param array $values
   *   The array of [value => value_label] elements.
   *
   * @return string
   *   String one element per line in format value|value_label
   */
  private function getFormattedValues(array $values) {
    $result = [];
    foreach ($values as $value) {
      $result[] = "{$value['value']}{$this->delimiter}{$value['value_label']}";
    }
    return implode("\n", $result);
  }

}
