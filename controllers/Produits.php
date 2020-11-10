<?php

namespace Controllers;


class Produits extends Controller
{

    protected $modelName = \Models\Produits::class;
    protected $pageName = "Produit";
    protected $repertoire = "produits";
    protected $ctrlName = "produits";
    protected $table = "produits";



    /**
     * Fonction pour séparer les résultats et les envoyer correctement à la vue
     */
    public function separateItem()
    {

        $items = $this->model->queryAll(['order' => 'date_appro', 'table' => 'produits']);
        $fruits = [];
        $legumes = [];

        foreach ($items as $value) {

            if ($value['type'] === "fruit") {
                array_push($fruits, $value);
            } else {
                array_push($legumes, $value);
            }
        }

        $pageTitle = "Tous les produits";
        $pageName = $_GET['page'];

        \Renderer::render($pageName, compact('pageTitle', 'items', 'fruits', 'legumes'));
    }
}
