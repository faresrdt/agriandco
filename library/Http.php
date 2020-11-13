<?php

class Http{

    /**
     * Fonction pour rediriger avec header location
     * @param string $url
     */
    public static function redirect(string $url): void
    {
    
        header("Location: $url");
        exit();
    }
}