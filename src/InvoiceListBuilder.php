<?php

namespace Drupal\invoicer;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Invoice entities.
 *
 * @ingroup invoicer
 */
class InvoiceListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * Stores the sum of all subtotal on the list.
   *
   * @var int
   */
  protected $subTotal = 0;

  /**
   * Stores the sum of all total on the list.
   *
   * @var int
   */
  protected $total = 0;

  /**
   * The form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['date'] = $this->t('Date');
    $header['Number'] = $this->t('Number');
    $header['name'] = $this->t('Name');
    $header['customer_name'] = $this->t('Customer name');
    $header['sub_total'] = $this->t('Subtotal price');
    $header['total'] = $this->t('Total price');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\invoicer\Entity\Invoice */
    $row['date'] = $entity->date->value;
    $row['number'] = $entity->series->value . '-' . $entity->number->value;
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.invoice.canonical', [
          'invoice' => $entity->id(),
        ]
      )
    );
    $row['customer_name'] = $entity->customer_name->value;
    $row['sub_total'] = $entity->sub_total->value;
    $row['total'] = $entity->total->value;

    $this->subTotal += $entity->sub_total->value;
    $this->total += $entity->total->value;

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    $formBuilder = \Drupal::getContainer()->get('form_builder');
    $build['filters'] = $formBuilder->getForm('Drupal\invoicer\Form\InvoicerFilterForm');
    $build['filters']['#weight'] = '-10';
    $build['table']['#rows'][] = [
      '',
      '',
      '',
      '',
      $this->subTotal, $this->total,
      '',
    ];
    return $build;
  }

  /**
   * Loads entity IDs using a pager sorted by the entity id.
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->sort('number');

    if (!empty($_SESSION['invoicer_filter']['series'])) {
      $query->condition('series', $_SESSION['invoicer_filter']['series']);
    }

    if (!empty($_SESSION['invoicer_filter']['text'])) {
      $or = $query->orConditionGroup()
        ->condition('name', '%' . $_SESSION['invoicer_filter']['text'] . '%', 'LIKE')
        ->condition('customer_name', '%' . $_SESSION['invoicer_filter']['text'] . '%', 'LIKE');
      $query->condition($or);
    }

    if (!empty($_SESSION['invoicer_filter']['status'])) {
      switch ($_SESSION['invoicer_filter']['status']) {
        case "1":
          $query->condition('status', TRUE);
          break;

        case "-1":
          $query->condition('status', FALSE);
          break;
      }
    }

    if (!empty($_SESSION['invoicer_filter']['quarter'])) {
      switch ($_SESSION['invoicer_filter']['quarter']) {
        case "1":
          $or = $query->orConditionGroup()
            ->condition('date', '%-01-%', 'LIKE')
            ->condition('date', '%-02-%', 'LIKE')
            ->condition('date', '%-03-%', 'LIKE');
          $query->condition($or);
          break;

        case "2":
          $or = $query->orConditionGroup()
            ->condition('date', '%-04-%', 'LIKE')
            ->condition('date', '%-05-%', 'LIKE')
            ->condition('date', '%-06-%', 'LIKE');
          $query->condition($or);
          break;

        case "3":
          $or = $query->orConditionGroup()
            ->condition('date', '%-07-%', 'LIKE')
            ->condition('date', '%-08-%', 'LIKE')
            ->condition('date', '%-09-%', 'LIKE');
          $query->condition($or);
          break;

        case "4":
          $or = $query->orConditionGroup()
            ->condition('date', '%-10-%', 'LIKE')
            ->condition('date', '%-11-%', 'LIKE')
            ->condition('date', '%-12-%', 'LIKE');
          $query->condition($or);
          break;

      }
    }
    return $query->execute();
  }

}
