<?php

namespace Drupal\invoicer\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Invoice entities.
 */
class InvoiceViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    return $data;
  }

}
