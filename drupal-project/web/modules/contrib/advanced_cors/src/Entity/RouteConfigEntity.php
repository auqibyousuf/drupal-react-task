<?php

namespace Drupal\advanced_cors\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Route configuration entity.
 *
 * @ConfigEntityType(
 *   id = "route_config",
 *   label = @Translation("Route configuration"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\advanced_cors\RouteConfigEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\advanced_cors\Form\RouteConfigEntityForm",
 *       "edit" = "Drupal\advanced_cors\Form\RouteConfigEntityForm",
 *       "delete" = "Drupal\advanced_cors\Form\RouteConfigEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\advanced_cors\RouteConfigEntityHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "route_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "weight" = "weight"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "weight",
 *     "patterns",
 *     "status",
 *     "allowed_headers",
 *     "allowed_methods",
 *     "allowed_origins",
 *     "exposed_headers",
 *     "max_age",
 *     "supports_credentials"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/services/advanced_cors/{route_config}",
 *     "add-form" = "/admin/config/services/advanced_cors/add",
 *     "edit-form" = "/admin/config/services/advanced_cors/{route_config}/edit",
 *     "delete-form" = "/admin/config/services/advanced_cors/{route_config}/delete",
 *     "collection" = "/admin/config/services/advanced_cors"
 *   }
 * )
 */
class RouteConfigEntity extends ConfigEntityBase implements RouteConfigEntityInterface {

  /**
   * The Route configuration ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Route configuration label.
   *
   * @var string
   */
  protected $label;

  /**
   * The weight of this config.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * {@inheritdoc}
   */
  public function getPatterns(): array {
    return $this->splitAndFilterValue('patterns');
  }

  /**
   * {@inheritdoc}
   */
  public function getAllowedOrigins(): array {
    return $this->splitAndFilterValue('allowed_origins');
  }

  /**
   * Splits a newline-delimited config setting into distinct, non-empty values.
   *
   * Leading and trailing whitespace is trimmed out of each value.
   *
   * @param string $config_key
   *   The machine name of the configuration value containing the
   *   newline-delimited values.
   *
   * @return string[]
   *   The split, trimmed, non-empty values obtained from the configured
   *   setting.
   */
  protected function splitAndFilterValue(string $config_key): array {
    $split_values   = explode(PHP_EOL, $this->get($config_key) ?? '');
    $trimmed_values = array_map('trim', $split_values);

    return array_filter($trimmed_values);
  }
}
