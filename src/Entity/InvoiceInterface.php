<?php

namespace Drupal\invoicer\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Invoice entities.
 *
 * @ingroup invoicer
 */
interface InvoiceInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Invoice name.
   *
   * @return string
   *   Name of the Invoice.
   */
  public function getName();

  /**
   * Sets the Invoice name.
   *
   * @param string $name
   *   The Invoice name.
   *
   * @return \Drupal\invoicer\Entity\InvoiceInterface
   *   The called Invoice entity.
   */
  public function setName($name);

  /**
   * Gets the Invoice creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Invoice.
   */
  public function getCreatedTime();

  /**
   * Sets the Invoice creation timestamp.
   *
   * @param int $timestamp
   *   The Invoice creation timestamp.
   *
   * @return \Drupal\invoicer\Entity\InvoiceInterface
   *   The called Invoice entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Invoice published status indicator.
   *
   * Unpublished Invoice are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Invoice is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Invoice.
   *
   * @param bool $published
   *   TRUE to set this Invoice to published, FALSE to unpublished.
   *
   * @return \Drupal\invoicer\Entity\InvoiceInterface
   *   The called Invoice entity.
   */
  public function setPublished($published);

}
