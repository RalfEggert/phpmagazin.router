<?php
/**
 * PHP Magazin: Artikel über PHP Router
 *
 * @author     Ralf Eggert <ralf@travello.de>
 * @link       https://www.travello.de/
 * @copyright  Travello GmbH, 2017
 */

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

$renderer = function ($tpl) {
    $html = implode('', file($tpl));
    $html = str_replace('%%router_name%%', ROUTER_NAME, $html);
    $html = str_replace('%%router_route%%', ROUTER_ROUTE, $html);

    return $html;
};

$homeHandler = function () use ($renderer) {
    $tpl = __DIR__ . '/../tpl/home.html';

    $html = $renderer($tpl);

    return new HtmlResponse($html);
};

$fileNotFoundHandler = function () use ($renderer) {
    $tpl = __DIR__ . '/../tpl/404.html';

    $html = $renderer($tpl);

    return new HtmlResponse($html);
};

$methodNotAllowedHandler = function () use ($renderer) {
    $tpl = __DIR__ . '/../tpl/405.html';

    $html = $renderer($tpl);

    return new HtmlResponse($html);
};

$showHandler = function ($request) use ($renderer) {
    if (is_array($request) && isset($request['request'])) {
        $request = $request['request'];
    }

    /** @var ServerRequestInterface $request */
    $id = (int)$request->getAttribute('id');

    $tpl = __DIR__ . '/../tpl/show.html';

    $html = $renderer($tpl);
    $html = str_replace('%%id%%', $id, $html);

    return new HtmlResponse($html);
};

$createGetHandler = function () use ($renderer) {
    $tpl = __DIR__ . '/../tpl/create-get.html';

    $html = $renderer($tpl);

    return new HtmlResponse($html);
};

$createPostHandler = function ($request) use ($renderer) {
    if (is_array($request) && isset($request['request'])) {
        $request = $request['request'];
    }

    /** @var ServerRequestInterface $request */
    $postData = $request->getParsedBody();
    $title    = (string)$postData['title'];

    $tpl = __DIR__ . '/../tpl/create-post.html';

    $html = $renderer($tpl);
    $html = str_replace('%%title%%', $title, $html);

    return new HtmlResponse($html);
};

