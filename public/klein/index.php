<?php
/**
 * PHP Magazin: Artikel über PHP Router
 *
 * @author     Ralf Eggert <ralf@travello.de>
 * @link       https://www.travello.de/
 * @copyright  Travello GmbH, 2017
 */

use Klein\Klein;
use Zend\Diactoros\ServerRequestFactory;

define('ROUTER_NAME', 'Klein');
define('ROUTER_ROUTE', '/klein');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../handler/klein_handlers.php';

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$klein = new Klein();
$klein->respond('GET', '/klein/', $homeHandler);
$klein->respond('GET', '/klein/[i:id]', $showHandler);
$klein->respond('GET', '/klein/create', $createGetHandler);
$klein->respond('POST', '/klein/create', $createPostHandler);

$klein->dispatch();
