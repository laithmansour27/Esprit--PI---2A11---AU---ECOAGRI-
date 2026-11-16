<?php
// Front controller: every request enters here, then the Router picks a Controller.
define('ROOT', dirname(__DIR__));
require ROOT . '/app/bootstrap.php';

$router = new Router(require ROOT . '/app/routes.php');
$router->dispatch();
