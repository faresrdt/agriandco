<?php

class Renderer
{

    /**
     * Fonction d'affichage des pages
     * @param string $pageName
     * @param array $variables (optionnal)
     */
    public static function render(string $pageName, array $variables = [])
    {

        $productModel = new \Models\Produits;
        $articleModel = new \Models\Articles;
        $userModel    = new \Models\User;

        extract($variables);
        ob_start();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        } 
        $allProducts = $productModel->queryAll(['order' => 'date_appro DESC', 'table' => 'produits']);

        $allArticles = $articleModel->queryAll(['order' => 'created_at DESC', 'table' => 'articles']);

        $allUsers = $userModel->queryAll(['order' => 'id DESC', 'table' => 'users']);

        //Blocage des pages nécessitant une connexion si la personne n'est pas connectée
        $blockPage = ['adminSpace', 'userSpace', 'newArticle', 'newProduit', 'newUser'];

        $forbidPage = false;
        foreach ($blockPage as $key => $value) {

            if ($pageName === $value) {
                $forbidPage = true;
            }
        }

        if (\Controllers\User::isConnected() == false and $forbidPage === true) {
            \Http::redirect('index.php');
        } else {
            if($pageName === "connexion" and \Controllers\User::isConnected() == true){
                \Http::redirect('index.php');
            }else{
                require('views/' . $pageName . '.phtml');
            }
        }

        $pageContent = ob_get_clean();

        //Chargement du layout
        require('views/layout/layout.phtml');
    }
}
