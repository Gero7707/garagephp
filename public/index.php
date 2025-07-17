<?php
//Affiche directment les erreurs dans la page
init_set('display_error', 1);
error_reporting(E_ALL);

//Inclure l'autoloader
require_once __DIR__ . '/vendor/autoload.php';

//Import des classes
use App\Config\Config;
use App\Utils\Response;

//Démarrer une session ou reprend la session exxistante
session_start();

//Charger nos variables d'envirennement
Config::load();

//Définr des routes avec la bibliothèque FastRoute
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r){


    $r->addRoute('GET','/',[App\Controllers\HomeController::class , 'index']);
    $r->addRoute('GET','/login',[App\Controllers\AuthController::class , 'showlogin']);
    $r->addRoute('POST','/login',[App\Controllers\AuthController::class , 'login']);
    $r->addRoute('POST','/logout',[App\Controllers\AuthController::class , 'logout']);
    $r->addRoute('POST','/cars',[App\Controllers\CarController::class , 'index']);
    
});

//Traitement de la requète

//Récupérer la méthode HTTP (GET , POST , PUT , PATCH) et l'URI ( /login, /cars/1)
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

//Dispatcher FastRoute
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
