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
    'GET /equipements' => ['EquipmentController', 'index'],
    'POST /equipements/creer' => ['EquipmentController', 'create'],
    'POST /equipements/modifier' => ['EquipmentController', 'update'],
    'POST /equipements/supprimer' => ['EquipmentController', 'delete'],
    'POST /maintenances/creer' => ['EquipmentController', 'createMaintenance'],
    'POST /maintenances/supprimer' => ['EquipmentController', 'deleteMaintenance'],

    // Gestion des utilisateurs - Administrateur
    'GET /utilisateurs' => ['UserController', 'index'],

    'GET /utilisateurs/ajouter' => ['UserController', 'create'],
    'POST /utilisateurs/ajouter' => ['UserController', 'create'],

    'GET /utilisateurs/modifier' => ['UserController', 'edit'],
    'POST /utilisateurs/modifier' => ['UserController', 'edit'],

    'POST /utilisateurs/supprimer' => ['UserController', 'delete'],

];
