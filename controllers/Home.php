<?php

namespace Controllers;


class Home extends Controller
{
    protected $modelName = \Models\Produits::class;

    /**
     * 1. Affichage de la page par defaut, page d'accueil du site
     */
    public function index()
    {


        $pageTitle = "Home";

        \Renderer::render($pageTitle, compact('pageTitle'));
    }
}
