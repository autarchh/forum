<?php
    require "vendor/autoload.php";//fichier activant l'autoload de Composer
    require "config.php";//contient les valeurs par défaut de l'application
    
    session_start();

    use App\Service\Router;
    
    $csrf_token = Router::generateToken();
    Router::CSRFProtection();
    
    $response = Router::handleRequest();

    ob_start();
    //on inclut le fichier vue transmis par le controller en allant dans 
    //le chemin des vues par défaut
    include(VIEW_PATH.$response["view"]);
    
    $content = ob_get_contents();

    ob_end_clean();

    require("view/layout.php");
