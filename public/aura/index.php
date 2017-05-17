<?php
/**
 * PHP Magazin: Artikel über PHP Router
 *
 * @author     Ralf Eggert <ralf@travello.de>
 * @link       https://www.travello.de/
 * @copyright  Travello GmbH, 2017
 */

use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequestFactory;

require_once __DIR__ . '/../../vendor/autoload.php';

$homeHandler = function () {
    $tpl = implode('', file(__DIR__ . '/tpl/home.html'));

    return new HtmlResponse($tpl);
};

$showHandler = function ($request) {
    /** @var ServerRequestInterface $request */
    $id = (int)$request->getAttribute('id');

    $tpl = implode('', file(__DIR__ . '/tpl/show.html'));
    $tpl = str_replace('%%id%%', $id, $tpl);

    return new HtmlResponse($tpl);
};

$createGetHandler = function () {
    $tpl = implode('', file(__DIR__ . '/tpl/create-get.html'));

    return new HtmlResponse($tpl);
};

$createPostHandler = function ($request) {
    /** @var ServerRequestInterface $request */
    $postData = $request->getParsedBody();
    $title    = (string)$postData['title'];

    $tpl = implode('', file(__DIR__ . '/tpl/create-post.html'));
    $tpl = str_replace('%%title%%', $title, $tpl);

    return new HtmlResponse($tpl);
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

$route = $routerContainer->getMatcher()->match($request);

if (!$route) {
    $tpl = implode('', file(__DIR__ . '/tpl/404.html'));

    $response = new HtmlResponse($tpl);
} else {
    foreach ($route->attributes as $key => $val) {
        $request = $request->withAttribute($key, $val);
    }

    $callable = $route->handler;

    /** @var HtmlResponse $response */
    $response = $callable($request);
}

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}

echo $response->getBody();
