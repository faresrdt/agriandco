<?php

namespace Controllers;

abstract class Controller
{

    protected $model;
    protected $modelName;
    protected $pageName;
    protected $repertoire;
    protected $ctrlName;
    protected $table;

    public function __construct()
    {
        $this->model = new $this->modelName;
    }

    public function __call($task, $arguments)
    {
        \Http::redirect('index.php');
    }

    /**
     * Fonction statique pour récupérer le nom de la page à afficher dans l'URL
     * @return string $page
     */
    public static function getPage(): string
    {
        if (!empty($_GET['page'])) {
            $page = htmlspecialchars($_GET['page']);
            return $page;
        } else {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "Aucun nom de page passé.";
            \Http::redirect('index.php');
        }
    }

    /**
     * Fonction statique pour récupérer le titre de la page à afficher dans l'URL
     * @return string $pageTitle
     */
    public static function getTitle(): string
    {
        if (!empty($_GET['pageTitle'])) {
            $pageTitle = htmlspecialchars($_GET['pageTitle']);
            return $pageTitle;
        } else {
            die('Aucun titre de page passé. [controller.php][getTitle]' . var_dump($_GET));
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "Aucun titre de page passé.";
            \Http::redirect('index.php');
        }
    }

    /**
     * Fonction statique pour récupérer l'id de l'élément dans l'URL
     * @return int $id
     */
    public static function getId(): int
    {
        if (!empty($_GET['id'])) {
            $id = htmlspecialchars(intval($_GET['id']));
            return $id;
        } else {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "Problème avec l'id.";
            \Http::redirect('index.php');
        }
    }

    /**
     * Fonction pour formater les noms pour pouvoir les rentrer dans la BDD sans espace
     * @param string $name 
     */
    public static function formatName($name)
    {

        //On enlève d'abord les espaces
        $name = trim(str_replace(' ', '-', $name));

        //Ensuite les accents
        $accents        = ["à", "é", "è", "À", "É", "È"];
        $rplcAccent     = ["A", "E", "E", "A", "E", "E"];
        $name = str_replace($accents, $rplcAccent, $name);

        return $name;
    }

    /**
     * Fonction statique pour savoir si un user est connecté
     * @return bool
     */
    public static function isConnected(): bool
    {
        if (isset($_SESSION['status']) and $_SESSION['status'] === "connected") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fonction statique pour savoir si c'est bien l'admin qui est connecté
     * @return bool
     */
    public static function isAdmin(): bool
    {
        if (isset($_SESSION['role']) and $_SESSION['role'] === 'admin') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fonction pour afficher une page en particulier sans action définie
     */
    public function render(?array $variables = [])
    {
        $pageName   = self::getPage();

        if ($pageName === "newProduit") {

            $pageTitle = "Nouveau produit";
        } elseif ($pageName === "newArticle") {

            $pageTitle = "Nouvel Article";
        } elseif ($pageName === "newUser") {

            $pageTitle = "Nouvel Utilisateur";
        } else {
            $pageTitle  = self::getTitle();
        }

        \Renderer::render($pageName, compact('pageTitle', 'variables'));
    }

    /**
     * Fonction pour inserer des données dans une table en fonction du model appelé
     */
    public function create()
    {
        /**
         * Récupération des informations à insérer
         */
        $fields = []; //Champs
        $values = []; //Valeurs

        if (isset($_POST['password'])) {
            $_POST['password'] = $this->model->hashPassword($_POST['password']);
        }

        //On formate le titre pour pouvoir l'utiliser dans l'URL ensuite
        if ($_POST['title']) {
            $_POST['title_href'] = self::formatName($_POST['title']);
        }

        foreach ($_POST as $key => $value) {
            array_push($fields, $key);
            array_push($values, $value);
        }

        /**
         * Insérer l'image
         */
        if (isset($_FILES['img']) and !empty($_FILES['img']['name'])) {


            $file       = $_FILES['img']['tmp_name'];
            $fileInfo   = pathinfo($_FILES['img']['name']);
            $fileName   = self::formatName($fileInfo['filename']);
            $extension  = $fileInfo['extension'];
            $repertoire = $this->repertoire;

            array_push($fields, 'img');
            array_push($values, $fileName . "." . $extension);

            $this->model->createImg($file, $fileName, $extension, $repertoire);
        }

        /**
         * On crée l'élément en fonction du model appelé
         */

        $this->model->create($fields, $values);


        /**
         * On redirige 
         */
        $_SESSION['messageType'] = 'success';
        $_SESSION['message']     = "Élément ajouté avec succès.";
        \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
    }

    /**
     * Fonction pour lire toutes les données d'une table en fonction du model appelé
     */
    public function readAll()
    {

        /**
         * Récupération des résultats
         */
        $items = $this->model->queryAll(['table' => $this->table]);
        if (!$items) {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "Les éléments n'existent pas.";
            \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
        }



        //Formatage de la date pour les articles
        for ($i = 0; $i < count($items); $i++) {
            if (isset($items[$i]['created_at'])) {
                $date = new \DateTime($items[$i]['created_at']);
                $items[$i]['created_at'] = $date->format('d-m-Y');
            }
        }

        /**
         * On affiche 
         */
        $pageTitle = self::getTitle();
        $pageName = self::getPage();

        \Renderer::render($pageName, compact('pageTitle', 'items'));
    }

    /**
     * Fonction pour lire une donnée d'une table en fonction du model appelé
     */
    public function read()
    {

        /**
         * Récupération des résultats
         */
        $id = null;
        if (isset($_GET['id']) and ctype_digit($_GET['id'])) {
            $id = $_GET['id'];
        }
        $item = $this->model->queryOneById($id);
        if (!$item) {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "l'élément n'existe pas.";
            \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
        }


        /**
         * On affiche 
         */
        if (isset($item['title'])) {
            $pageTitle = $item['title'];
        } elseif (isset($item['name'])) {
            $pageTitle = $item['name'];
        } else {
            $pageTitle = self::getTitle();
        }
        $pageName = self::getPage();

        //Formatage de la date si c'est un article
        if (isset($item['created_at'])) {
            $date = new \DateTime($item['created_at']);
            $item['created_at'] = $date->format('d-m-Y');
        }

        //Formatage des favoris si c'est un utilisateur
        if (isset($item['fav'])) {
            $favs = explode(', ', $item['fav']);
            \Renderer::render($pageName, compact('pageTitle', 'item', 'favs'));
        } else {
            $favs = '';
            \Renderer::render($pageName, compact('pageTitle', 'item', 'favs'));
        }
    }


    /**
     * Fonction pour récupérer un élément par son id dans une table en fonction du model appelé 
     */
    public function getById()
    {

        /**
         * Récupération du param "id" et vérification de celui-ci
         */

        // On déclare et on défini la variable à null
        $item_id = null;

        // On récupère l'id et on vérifie que c'est un nombre entier, on le conserve dans la variable $item_id
        if (!empty($_GET['id']) && ctype_digit($_GET['id'])) {
            $item_id = htmlspecialchars($_GET['id']);
        } else {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "Vous devez préciser un paramètre id.";
            \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
        }


        /**
         * Récupération de l'élément par son id
         */
        $item = $this->model->queryOneById($item_id);
        if (!$item) {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "l'élément n'existe pas.";
            \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
        }

        /**
         * On affiche 
         */
        $pageTitle = $item['title'];
        $pageName = self::getPage();

        \Renderer::render($pageName, compact('pageTitle', 'item', 'item_id'));
    }

    /**
     * Fonction pour récupérer un élément par son mail dans une table en fonction du model appelé
     */
    public function getByMail()
    {

        /**
         * Récupération du param "mail" et vérification de celui-ci
         */
        // On déclare et on défini la variable à null
        $mail = null;

        // On vérifie qu'on a bien un élément dans $_POST['mail'], on le conserve dans une variable
        if (!empty($_POST['mail'])) {
            $mail = htmlspecialchars($_POST['mail']);
        } else {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "Vous devez préciser un paramètre mail !";
            \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
        }


        /**
         * Récupération de l'élément
         */
        $item = $this->model->queryOneByMail($mail);
        if (!$item) {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "l'élément rechercher n'existe pas.";
            \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
        }


        /**
         * On affiche 
         */
        $pageTitle = $item['title'];
        $pageName = self::getPage();

        \Renderer::render($pageName, compact('pageTitle', 'item'));
    }

    /**
     * Fonction pour mettre à jour un élément d'une table en fonction du model appelé
     */
    public function update()
    {
        /**
         * Récupération du param "id" et vérification de celui-ci
         */

        // On déclare et on défini la variable à null
        $item_id = null;

        // On récupère l'id et on vérifie que c'est un nombre entier, on le conserve dans la variable $item_id
        if (!empty($_POST['id']) && ctype_digit($_POST['id'])) {
            $item_id = htmlspecialchars($_POST['id']);
        } else {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "Vous devez préciser un paramètre `id` dans $_POST !";
            \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
        }

        /**
         * Vérification que l'élément existe bel et bien
         */
        $item = $this->model->queryOneById($item_id);
        if (!$item) {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "L'élément $item_id n'existe pas, vous ne pouvez donc pas le supprimer !";
            \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
        }

        /**
         * Récupération des informations à mettre à jour
         */
        $fields = []; //Champs
        $values = []; //Valeurs

        if ($_POST['title']) {
            $_POST['title_href'] = self::formatName($_POST['title']);
        }

        foreach ($_POST as $key => $value) {
            array_push($fields, $key);
            array_push($values, $value);
        }


        /**
         * Si une image est transmise, la mettre à jour
         */
        if (isset($_FILES['img']) and !empty($_FILES['img']['name'])) {

            $file       = $_FILES['img']['tmp_name'];
            $fileInfo   = pathinfo($_FILES['img']['name']);
            $fileName   = self::formatName($fileInfo['filename']);
            $extension  = $fileInfo['extension'];
            $repertoire = $this->repertoire;

            $this->model->updateImg($item_id, $file, $fileName, $extension, $repertoire);
        }

        /**
         * On met à jour l'élément en fonction de son id et du model appelé
         * On le récupère une fois mis à jour
         */

        $this->model->update($item_id, $fields, $values);
        $item = $this->model->queryOneById($item_id);

        /**
         * On redirige 
         */
        $_SESSION['messageType'] = 'success';
        $_SESSION['message']     = "Élément mis à jour avec succès.";
        \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
    }

    /**
     * Fonction pour supprimer un élément d'une table en fonction du model appelé
     */
    public function delete()
    {

        /**
         * Récupération du param "id" et vérification de celui-ci
         */
        if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "Aucun id n'est précisé.";
            \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
        }

        $item_id = htmlspecialchars($_GET['id']);

        /**
         * Vérification que l'élément existe bel et bien
         */
        $item = $this->model->queryOneById($item_id);
        if (!$item) {

            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "L'élément $item_id n'existe pas, vous ne pouvez donc pas le supprimer !";
            \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
        }

        /**
         * Réelle suppression de l'élément dans la BDD
         */

        if (self::isAdmin()) {
            $this->model->delete($item_id);
        } else {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "Vous n'êtes pas admin !";
            \Http::redirect('index.php');
        }


        /**
         * Suppression de l'image si celle ci existe
         */
        if (isset($item['img'])) {
            $repertoire = $this->repertoire;
            $this->model->deleteImg($item['img'], $repertoire);
        }

        /**
         * On redirige 
         */
        $_SESSION['messageType'] = 'success';
        $_SESSION['message']     = "Élément correctement supprimé.";
        \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
    }
}
