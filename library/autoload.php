<?php

spl_autoload_register(function($className){
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    } 

    $className = str_replace("\\", "/", $className);
    require_once("$className.php");
});