<?php
/**
 * PHP Magazin: Artikel über PHP Router
 *
 * @author     Ralf Eggert <ralf@travello.de>
 * @link       https://www.travello.de/
 * @copyright  Travello GmbH, 2017
 */

use Aura\Router\RouterContainer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;

require_once __DIR__ . '/../../vendor/autoload.php';

$response = new Zend\Diactoros\Response();

$homeHandler = function ($request, $response) {
    $tpl = implode('', file(__DIR__ . '/tpl/home.html'));

    /** @var ResponseInterface $response */
    $response->getBody()->write($tpl);

    return $response;
};

$showHandler = function ($request, $response) {
    /** @var ServerRequestInterface $request */
    $id = (int)$request->getAttribute('id');

    $tpl = implode('', file(__DIR__ . '/tpl/show.html'));
    $tpl = str_replace('%%id%%', $id, $tpl);

    /** @var ResponseInterface $response */
    $response->getBody()->write($tpl);

    return $response;
};

$createGetHandler = function ($request, $response) {
    $tpl = implode('', file(__DIR__ . '/tpl/create-get.html'));

    /** @var ResponseInterface $response */
    $response->getBody()->write($tpl);

    return $response;
};

$createPostHandler = function ($request, $response) {
    /** @var ServerRequestInterface $request */
    $postData = $request->getParsedBody();
    $title    = (string)$postData['title'];

    $tpl = implode('', file(__DIR__ . '/tpl/create-post.html'));
    $tpl = str_replace('%%title%%', $title, $tpl);

    /** @var ResponseInterface $response */
    $response->getBody()->write($tpl);

    return $response;
};

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

// get the route matcher from the container ...
$matcher = $routerContainer->getMatcher();

// .. and try to match the request to a route.
$route = $matcher->match($request);
if (!$route) {
    echo 'Keine Route für die Anfrage gefunden.';
    exit;
}

// add route attributes to the request
foreach ($route->attributes as $key => $val) {
    $request = $request->withAttribute($key, $val);
}

// dispatch the request to the route handler.
// (consider using https://github.com/auraphp/Aura.Dispatcher
// in place of the one callable below.)
$callable = $route->handler;

/** @var ResponseInterface $response */
$response = $callable($request, $response);

// emit the response
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
echo $response->getBody();
