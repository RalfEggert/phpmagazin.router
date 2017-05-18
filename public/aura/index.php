<?php
/**
 * PHP Magazin: Artikel über PHP Router
 *
 * @author     Ralf Eggert <ralf@travello.de>
 * @link       https://www.travello.de/
 * @copyright  Travello GmbH, 2017
 */

use Aura\Router\RouterContainer;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequestFactory;

define('ROUTER_NAME', 'Aura.Router');
define('ROUTER_ROUTE', '/aura');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../handler/other_handlers.php';

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new RouterContainer();

$map = $routerContainer->getMap();
$map->get('aura.home', '/aura/', $homeHandler);
$map->get('aura.show', '/aura/{id}', $showHandler)->tokens(
    ['id' => '\d+']
);
$map->get('aura.create.get', '/aura/create', $createGetHandler);
$map->post('aura.create.post', '/aura/create', $createPostHandler);

$route = $routerContainer->getMatcher()->match($request);

if (!$route) {
    $response = $fileNotFoundHandler();
} else {
    foreach ($route->attributes as $key => $val) {
        $request = $request->withAttribute($key, $val);
    }

    $handler = $route->handler;

    /** @var HtmlResponse $response */
    $response = $handler($request);
}

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}

echo $response->getBody();
