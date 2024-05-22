<?php

namespace Drupal\advanced_cors\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Route configuration entities.
 */
interface RouteConfigEntityInterface extends ConfigEntityInterface {

  /**
   * Gets the configured patterns for paths to which this config applies.
   *
   * The user supplies patterns in a newline-delimited textarea; this method
   * converts them to an array of trimmed strings automatically.
   *
   * @return string[]
   */
  public function getPatterns(): array;

  /**
   * Gets the configured CORS origins this config allows.
   *
   * The user supplies origins in a newline-delimited textarea; this method
   * converts them to an array of trimmed strings automatically.
   *
   * @return string[]
   */
  public function getAllowedOrigins(): array;

}
