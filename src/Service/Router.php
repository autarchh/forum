<?php
    namespace App\Service;

    abstract class Router
    {
        /**
         * Traite la demande de l'utilisateur et renvoie le résultat d'un controller
         * 
         * @return array - un tableau contenant "view", la vue à afficher et "data", les données nécessaires à la vue
         */
        public static function handleRequest(): array
        {

            $ctrl = DEFAULT_CTRL;//home par défaut
            $action = DEFAULT_ACTION;//action "index" par défaut
            
            //ya t il un param ctrl dans l'URL ? Si y a pas
            if(isset($_GET["ctrl"])){
                $ctrl = $_GET["ctrl"];//on récupère le param ctrl de l'URL
            }

            //depuis ce param, on fabrique le nom officiel du controller voulu
            //"home" => "HomeController"
            $ctrlname = ucfirst($ctrl)."Controller";
            //on fabrique aussi le namespace de la classe Controller à charger
            //"App\Controller\HomeController"
            //FQCN = Fully Qualified Class Name = namespace+nom_classe
            $ctrlFQCN = "App\\Controller\\".$ctrlname;
            //si le chemin fabriqué ci-dessus ne correspond pas à un fichier existant
            if(!class_exists($ctrlFQCN)){
                //@TODO : fichier introuvable = 404.php
                header('HTTP/1.0 404 Not Found');
            }
            else{
                //si un param action est dans l'URL ET si ce param correspond à une méthode du controller
                if(isset($_GET["action"]) && method_exists($ctrlFQCN, $_GET['action'])){
                    //l'action à executer est celle de l'URL
                    $action = $_GET['action'];
                    //@TODO : action inconnue = 404.php
                }
               
            }
            //on instancie le controller voulu => new HomeController()
            $controller = new $ctrlFQCN();
            //la response à traiter sera le retour de l'appel de la méthode du controller
            //$response = HomeController->index()
            return $controller->$action();
        }

        public static function redirect($route)
        {
            header("Location:".$route);
            die;
        }

        public static function generateToken()
        {
            $key = bin2hex(random_bytes(32));
            $csrf_token = hash_hmac("sha256", SECRET_APP, $key);
            setcookie("CSRF_KEY", $csrf_token);
            return $csrf_token;
        }

        public static function CSRFProtection(){
            

            if(!empty($_POST) && isset($_POST["csrf_token"])){
                if(!hash_equals($_POST["csrf_token"], $_COOKIE["CSRF_KEY"])){
                    session_destroy();
                    session_start();
                    Session::setMessage("error", "Invalid CSRF token");
                    self::redirect("index.php");
                }
            }
        }

    }