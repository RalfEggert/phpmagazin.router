<?php
/**
 * PHP Magazin: Artikel über PHP Router
 *
 * @author     Ralf Eggert <ralf@travello.de>
 * @link       https://www.travello.de/
 * @copyright  Travello GmbH, 2017
 */

use FastRoute\DataGenerator\GroupCountBased as GroupCountBasedGenerator;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Zend\Diactoros\ServerRequestFactory;

define('ROUTER_NAME', 'FastRoute');
define('ROUTER_ROUTE', '/fast');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../handler/handler.php';

/** @var RouteCollector $routeCollector */
$routeCollector = new RouteCollector(
    new Std(), new GroupCountBasedGenerator()
);
$routeCollector->addRoute('GET', '/fast/', $homeHandler);
$routeCollector->addRoute('GET', '/fast/{id:\d+}', $showHandler);
$routeCollector->addRoute('GET', '/fast/create', $createGetHandler);
$routeCollector->addRoute('POST', '/fast/create', $createPostHandler);

$dispatcher = new GroupCountBasedDispatcher($routeCollector->getData());

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routeInfo = $dispatcher->dispatch(
    $request->getMethod(), $request->getUri()->getPath()
);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        $response = $fileNotFoundHandler();

        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $response = $methodNotAllowedHandler();

        break;

    case Dispatcher::FOUND:
    default:
        foreach ($routeInfo[2] as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }

        $handler = $routeInfo[1];

        $response = $handler($request);

        break;
}

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}

echo $response->getBody();
