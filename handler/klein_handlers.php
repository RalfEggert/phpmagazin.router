<?php
/**
 * PHP Magazin: Artikel über PHP Router
 *
 * @author     Ralf Eggert <ralf@travello.de>
 * @link       https://www.travello.de/
 * @copyright  Travello GmbH, 2017
 */

use Klein\Request;

$renderer = function ($tpl) {
    $html = implode('', file($tpl));
    $html = str_replace('%%router_name%%', ROUTER_NAME, $html);
    $html = str_replace('%%router_route%%', ROUTER_ROUTE, $html);

    return $html;
};

$homeHandler = function () use ($renderer) {
    $tpl = __DIR__ . '/../tpl/home.html';

    $html = $renderer($tpl);

    return $html;
};

$fileNotFoundHandler = function () use ($renderer) {
    $tpl = __DIR__ . '/../tpl/404.html';

    $html = $renderer($tpl);

    return $html;
};

$methodNotAllowedHandler = function () use ($renderer) {
    $tpl = __DIR__ . '/../tpl/405.html';

    $html = $renderer($tpl);

    return $html;
};

$showHandler = function ($request) use ($renderer) {
    /** @var Request $request */
    $id = (int)$request->param('id');

    $tpl = __DIR__ . '/../tpl/show.html';

    $html = $renderer($tpl);
    $html = str_replace('%%id%%', $id, $html);

    return $html;
};

$createGetHandler = function () use ($renderer) {
    $tpl = __DIR__ . '/../tpl/create-get.html';

    $html = $renderer($tpl);

    return $html;
};

$createPostHandler = function ($request) use ($renderer) {
    /** @var Request $request */
    $postData = $request->paramsPost();
    $title    = (string)$postData['title'];

    $tpl = __DIR__ . '/../tpl/create-post.html';

    $html = $renderer($tpl);
    $html = str_replace('%%title%%', $title, $html);

    return $html;
};

