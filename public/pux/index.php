<?php
/**
 * PHP Magazin: Artikel über PHP Router
 *
 * @author     Ralf Eggert <ralf@travello.de>
 * @link       https://www.travello.de/
 * @copyright  Travello GmbH, 2017
 */

use Pux\Mux;
use Pux\RouteExecutor;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

define('ROUTER_NAME', 'Pux');
define('ROUTER_ROUTE', '/pux');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../handler/other_handlers.php';

$timeStart = microtime(true);

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$mux = new Mux();
$mux->get('/pux/', $homeHandler);
$mux->get('/pux/:id', $showHandler, ['require' => ['id' => '\d+']]);
$mux->get('/pux/create', $createGetHandler);
$mux->post('/pux/create', $createPostHandler);

$route = $mux->dispatch($request->getUri()->getPath());

if (isset($route[3]['variables']) && isset($route[3]['vars'])) {
    foreach ($route[3]['variables'] as $key) {
        $request = $request->withAttribute($key, $route[3]['vars'][$key]);
    }
}

/** @var Response $response */
$response = RouteExecutor::execute($route, ['request' => $request]);

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}

echo $response->getBody();

$timeEnd = microtime(true);

$fileName = __DIR__ . '/../../data/log/pux.log';
file_put_contents($fileName, ($timeEnd - $timeStart) . "\n", FILE_APPEND);
