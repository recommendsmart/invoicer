<?php

namespace Drupal\invoicer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Form\FormInterface;

/**
 * Invoices list filter form.
 */
class InvoicerFilterForm extends FormBase implements FormInterface {

  /**
   * Filters to apply.
   *
   * @var array
   */
  protected $filters;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'invoicer_filter_form';
  }

  /**
   * Get a list of all filters to apply.
   *
   * @return array
   *   Filters to apply.
   */
  protected function getFilters() {
    $filters = [
      'series' => [
        '#title' => 'Series',
        '#type' => 'select',
        '#options' => ['2019' => '2019', '2020' => '2020', '2021' => '2021', '0' => 'any'],
        '#attributes' => ['onchange' => 'this.form.submit();'],
      ],
      'quarter' => [
        '#title' => 'Quarter',
        '#type' => 'select',
        '#options' => [
          '1' => t('1st quarter'),
          '2' => t('2nd quarter'),
          '3' => t('3th quarter'),
          '4' => t('4th quarter'),
          '0' => t('any'),
        ],
        '#attributes' => ['onchange' => 'this.form.submit();'],
      ],
      'status' => [
        '#title' => 'Payment',
        '#type' => 'select',
        '#options' => [
          '0' => t('Any'),
          '1' => t('Payed'),
          '-1' => t('Pending'),
        ],
        '#attributes' => ['onchange' => 'this.form.submit();'],
      ],
      'text' => [
        '#title' => 'Quick search',
        '#type' => 'textfield',
        '#attributes' => ['onchange' => 'this.form.submit();'],
      ],
    ];

    return $filters;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'invoicer/invoice_list';

    $form['filters'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => ['fieldgroup'],
      ],
    ];

    $form['filters']['status'] = $this->getFilters();

    // Set current values from session.
    foreach ($form['filters']['status'] as $key => $filter) {
      if (!empty($_SESSION['invoicer_filter'][$key])) {
        $form['filters']['status'][$key]['#default_value'] = $_SESSION['invoicer_filter'][$key];
      }
    }

    $form['filters']['actions'] = array(
      '#type' => 'actions',
      '#attributes' => array('class' => array('container-inline')),
    );
    $form['filters']['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
    );

    if (!empty($_SESSION['invoicer_filter'])) {
      $form['filters']['actions']['reset'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Reset'),
        '#limit_validation_errors' => array(),
        '#submit' => array('::resetForm'),
      );
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $filters = $this->getFilters();
    foreach ($filters as $name => $filter) {
      if ($form_state->hasValue($name)) {
        $_SESSION['invoicer_filter'][$name] = $form_state->getValue($name);
      }
    }
  }

  /**
   * Resets the filter form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function resetForm(array &$form, FormStateInterface $form_state) {
    $_SESSION['invoicer_filter'] = array();
  }

}
