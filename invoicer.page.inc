<?php

/**
 * @file
 * Contains invoicer.page.inc.
 *
 * Page callback for Invoice entities.
 */

use Drupal\user\Entity\User;

/**
 * Prepares variables for Invoice templates.
 *
 * Default template: invoicer.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_invoicer(array &$variables) {
  // Load the current user.
  $user = User::load(\Drupal::currentUser()->id());

  $variables['elements']['logo'] = 'logo';
}
