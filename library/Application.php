<?php


class Application
{
        /**
         * Processus à lancer pour le fonctionement du site
         * Pas d'argument
         */
    public static function  process()
    {
       
        //$controllerName = Controller à appeler, par defaut "Home"
        //$task = tache a effectuer, par defaut "index"
        $controllerName = "Home";
        $task = "index";

        //On controle le nom du controller à appeler
        if(!isset($_GET['ctrl'])){
            $controllerName = $controllerName;
        }else{
            if(!empty($_GET['ctrl'])){
                $controllerName = ucfirst($_GET['ctrl']);
            }
        }
    
        
        //On controle le nom de la tache à appeler
        if(!isset($_GET['task'])){
            $controllerName = "Home";
            $task = $task;
        }elseif($controllerName === "Home"){
            $task = $task;
        }else{
            if(!empty($_GET['task'])){
                $task = ucfirst($_GET['task']);
            }
        }
        

        //On concatène pour obtenir le chemin avec le nom du controller
        $controllerName = "\controllers\\" . $controllerName;

        //On créer une instance du controller et on appel ensuite la tache que l'on souhaite
        $controller = new $controllerName();
        $controller->$task();
    }
}