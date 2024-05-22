<?php

namespace Drupal\advanced_cors\EventSubscriber;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Drupal\advanced_cors\Entity\RouteConfigEntityInterface;
use Drupal\advanced_cors\PatternsCache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Event Subscriber for adding headers.
 */
class AdvancedCorsEventSubscriber implements EventSubscriberInterface {

  /**
   * The entity storage class.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The path alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * Patterns cache service.
   *
   * @var \Drupal\advanced_cors\PatternsCache
   */
  protected $patterns;

  /**
   * Constructs a new CORS response event subscriber.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity manager.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The alias manager.
   * @param \Drupal\Core\Path\PathMatcherInterface $path_matcher
   *   The path matcher.
   * @param \Drupal\advanced_cors\PatternsCache $patterns
   *   The pattern cache.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException|\Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, AliasManagerInterface $alias_manager, PathMatcherInterface $path_matcher, PatternsCache $patterns) {
    $this->storage = $entityTypeManager->getStorage('route_config');
    $this->aliasManager = $alias_manager;
    $this->pathMatcher = $path_matcher;
    $this->patterns = $patterns;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];

    $events[KernelEvents::RESPONSE] = 'onResponse';

    return $events;
  }

  /**
   * Adds headers to the response.
   */
  public function onResponse(ResponseEvent $event) {
    $request = $event->getRequest();
    $path_info = $request->getPathInfo();
    $current_path = $this->aliasManager->getPathByAlias($path_info);

    foreach ($this->patterns->getPatterns() as $pattern => $id) {
      if ($this->pathMatcher->matchPath($current_path, $pattern)) {
        $config = $this->storage->load($id);

        if (!empty($config)) {
          assert($config instanceof RouteConfigEntityInterface);
          $this->addCorsHeaders($event, $config);
        }

        break;
      }
    }
  }

  /**
   * Adds CORS headers to the response.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The response event from Drupal core (contains request and response).
   * @param \Drupal\advanced_cors\Entity\RouteConfigEntityInterface $config
   *   The configuration for this module.
   */
  protected function addCorsHeaders(ResponseEvent $event,
                                    RouteConfigEntityInterface $config) {
    $mapping_headers = [
      'allowed_headers' => 'Access-Control-Allow-Headers',
      'allowed_methods' => 'Access-Control-Allow-Methods',
      'exposed_headers' => 'Access-Control-Expose-Headers',
      'max_age' => 'Access-Control-Max-Age',
      'supports_credentials' => 'Access-Control-Allow-Credentials',
    ];

    $request  = $event->getRequest();
    $response = $event->getResponse();

    foreach ($mapping_headers as $config_name => $header) {
      $configured_value = trim($config->get($config_name) ?? '');

      if (!empty($configured_value)) {
        $response->headers->set($header, $configured_value, TRUE);
      }
    }

    // Handle "Access-Control-Allow-Origin" specially.
    $allowed_origins = $config->getAllowedOrigins();

    if (!empty($allowed_origins)) {
      $allowed_origin = $this->selectOrigin($request, $allowed_origins);

      $response->headers->set(
        'Access-Control-Allow-Origin',
        $allowed_origin,
        TRUE
      );
    }
  }

  /**
   * Selects the appropriate allowed origin to return for the request origin.
   *
   * If one of the configured allowed origins matches the "Origin" header in the
   * request, that value is returned. If none of the origins match, then the
   * first allowed origin is returned (this should result in an error in the
   * browser, but provides guidance to the end-user/end-developer that their
   * request origin may be incorrect).
   *
   * This is necessary because the "Access-Control-Allow-Origin" can only
   * specify a single origin value. See:
   * https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Information about the incoming request.
   * @param string[] $allowed_origins
   *   The array of configured allowed origin values.
   *
   * @return string
   *   The value to send back in the "Access-Control-Allow-Origin" header.
   */
  protected function selectOrigin(Request $request,
                                  array $allowed_origins): string {
    $request_origin = $request->headers->get('Origin', '');
    $response_origin = reset($allowed_origins);

    foreach ($allowed_origins as $allowed_origin) {
      if ($allowed_origin === $request_origin) {
        $response_origin = $allowed_origin;
        break;
      }
    }

    return $response_origin;
  }

}
