<?php

namespace Drupal\invoicer\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\Component\Datetime\TimeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Form controller for Invoice edit forms.
 *
 * @ingroup invoicer
 */
class InvoiceForm extends ContentEntityForm {
  /**
   * @var \Drupal\Core\Entity\Query\QueryFactoryInterface
   *
   * The entity query service.
   */
  protected $entityQuery;

  /**
   * Constructs a ContentEntityForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\Query\QueryFactory
   *   The queryFactory.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info, QueryFactory $entity_query, TimeInterface $time = NULL) {
    parent::__construct($entity_manager, $entity_type_bundle_info, $time);
    $this->entityManager = $entity_manager;
    $this->entityQuery = $entity_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    try {
      $datetime = $container->get('datetime.time');
    }
    catch (ServiceNotFoundException $e) {
      $datetime = NULL;
    }

    return new static(
      $container->get('entity.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity.query'),
      $datetime
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->setDefaultValues();
    $form = parent::buildForm($form, $form_state);

    $form['#attached']['library'][] = 'invoicer/invoice_form';

    // Control fieldset.
    $form['control'] = [
      '#type' => 'fieldset',
      '#title' => t('Control'),
      '#weight' => 0,
    ];
    $form['control']['number'] = $form['number'];
    unset($form['number']);
    $form['control']['date'] = $form['date'];
    unset($form['date']);

    // Customer fieldset.
    $form['customer'] = [
      '#type' => 'fieldset',
      '#title' => t('Customer'),
      '#weight' => 1,
    ];
    $form['customer']['customer_id'] = $form['customer_id'];
    unset($form['customer_id']);
    $form['customer']['customer_name'] = $form['customer_name'];
    unset($form['customer_name']);
    $form['customer']['customer_address'] = $form['customer_address'];
    unset($form['customer_address']);

    // Provider Fieldset.
    $form['provider'] = [
      '#type' => 'fieldset',
      '#title' => t('Provider'),
      '#weight' => 2,
    ];
    $form['provider']['provider_id'] = $form['provider_id'];
    unset($form['provider_id']);
    $form['provider']['provider_name'] = $form['provider_name'];
    unset($form['provider_name']);
    $form['provider']['provider_address'] = $form['provider_address'];
    unset($form['provider_address']);

    // Abstract fieldset.
    $form['abstract'] = [
      '#type' => 'fieldset',
      '#title' => t('Abstract'),
      '#weight' => 4,
    ];

    $form['abstract']['sub_total'] = $form['sub_total'];
    unset($form['sub_total']);
    $form['abstract']['sub_total']['#attributes']['class'][] = 'abstract-subtotal';

    $form['abstract']['gst'] = $form['gst'];
    unset($form['gst']);
    $form['abstract']['gst']['#attributes']['class'][] = 'abstract-gst';

    $form['abstract']['total'] = $form['total'];
    unset($form['total']);
    $form['abstract']['total']['#attributes']['class'][] = 'abstract-total';

    $form['comments']['#weight'] = 5;

    // Default values.
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Invoice.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Invoice.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.invoice.canonical', ['invoice' => $entity->id()]);
  }

  /**
   * Set values for empty invoices.
   */
  protected function setDefaultValues() {
    // Load the current user.
    $user = User::load(\Drupal::currentUser()->id());

    if ($this->entity->date->value == '') {
      $this->entity->date->value = date('Y-m-d');
    }

    $fids = $this->entityQuery
      ->get('invoice')
      ->condition('date', date('Y'), 'CONTAINS')
      ->sort('number', 'DESC')
      ->range(0, 1)
      ->execute();

    if (count($fids) == 0) {
      // First invoice in serial should have number 1.
      $this->entity->number->value = 1;
    } else {
      // Get the last element and set next as default value.
      $last_invoice = $this->entity->load(array_pop($fids));
      $this->entity->number->value = $last_invoice->get('number')->value + 1;
    }


  }

}
