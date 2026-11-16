<?php
// One line per page: 'METHOD /url' => [Controller, 'method'].
return [
    'GET /' => ['HomeController', 'index'],
    'GET /projet' => ['HomeController', 'project'],
    'GET /services' => ['HomeController', 'services'],
    'GET /connexion' => ['AuthController', 'login'],
    'POST /connexion' => ['AuthController', 'login'],
    'GET /inscription' => ['AuthController', 'register'],
    'POST /inscription' => ['AuthController', 'register'],
    'POST /deconnexion' => ['AuthController', 'logout'],
    'GET /dashboard' => ['DashboardController', 'index'],
    'GET /module' => ['DashboardController', 'module'],
];
