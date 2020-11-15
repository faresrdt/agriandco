<?php

namespace Controllers;

use Models\Admin;

class User extends Controller
{

    protected $modelName = \Models\User::class;
    public $adminModel = \Models\Admin::class;
    protected $pageName = "userSpace";
    protected $ctrlName = "user";
    protected $table = "users";


    /**
     * Fonction pour que l'utilisateur se connecte
     */
    public function logIn()
    {
        $mail = htmlspecialchars($_POST['mail']);
        $password = htmlspecialchars($_POST['password']);
        $user = $this->model->queryOneByMail($mail);

        if (!$user) {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = "Adresse e-mail inexistante";
            \Http::redirect('index.php?ctrl=user&task=render&page=connexion&pageTitle=Connexion/Inscription');
        } else {
            $pass = password_verify($password, $user['password']);

            if ($pass === true) {

                session_start();
                $_SESSION['status']     = "connected";
                $_SESSION['role']       = $user['role'];
                $_SESSION['name']       = $user['name'];
                $_SESSION['firstname']  = $user['firstname'];
                $_SESSION['mail']       = $user['mail'];
                $_SESSION['id']         = $user['id'];
                $id         = $user['id'];
                $userName   = $user['firstname'];

                if (self::isAdmin()) {

                    setcookie("isAdmin", true);

                    $_SESSION['messageType'] = 'success';
                    $_SESSION['message']     = "Bienvenue Administrateur $userName";
                    \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
                } else {

                    setcookie("isUser", true);

                    $_SESSION['messageType'] = 'success';
                    $_SESSION['message']     = "Bienvenue $userName";
                    \Http::redirect("index.php?ctrl=user&task=read&page=userSpace&pageTitle=Mon Espace&id=$id");
                }
            } else {
                $_SESSION['messageType'] = 'error';
                $_SESSION['message']     = "Erreur dans la saisie du mot de passe.";

                var_dump($_SESSION);
                \Http::redirect('index.php?ctrl=user&task=render&page=connexion&pageTitle=Connexion/Inscription');
            }
        }
    }

    /**
     * Fonction pour que l'utilisateur se déconnecte
     */
    public function logOut()
    {
        session_start();
        $_SESSION = [];
        // Destruction de la session
        session_destroy();
        // Destruction du tableau de session
        unset($_SESSION);
        // Destruction des cookies
        setcookie("isAdmin");
        setcookie("isUser");
        unset($_COOKIE['isAdmin']);
        unset($_COOKIE['isUser']);

        \Http::redirect('index.php');
    }

    /**
     * Fonction pour inserer des données dans une table en fonction du model appelé
     */
    public function create()
    {

        /**
         * Vérification si compte déjà existant avec le même mail
         */
        if (isset($_POST['mail'])) {
            $mailBdd = $this->model->queryOneByMail($_POST['mail']);

            if ($_POST['mail'] === $mailBdd['mail']) {
                if (isset($_SESSION['role']) and $_SESSION['role'] === 'admin') {
                    $_SESSION['messageType'] = 'error';
                    $_SESSION['message']     = "Un compte existe déjà avec cette adresse e-mail. Veuillez vous connecter.";

                    \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
                } else {
                    $_SESSION['messageType'] = 'error';
                    $_SESSION['message']     = "Un compte existe déjà avec cette adresse e-mail. Veuillez vous connecter.";

                    \Http::redirect('index.php?ctrl=user&task=render&page=connexion&pageTitle=Connexion');
                }
            } else {

                /**
                 * Récupération des informations à insérer
                 */
                $fields = []; //Champs
                $values = []; //Valeurs

                if (isset($_POST['password'])) {
                    $_POST['password'] = htmlspecialchars($this->model->hashPassword($_POST['password']));
                }

                foreach ($_POST as $key => $value) {
                    array_push($fields, $key);
                    array_push($values, htmlspecialchars($value));
                }


                /**
                 * On crée l'élément en fonction du model appelé
                 */

                $this->model->create($fields, $values);


                /**
                 * On redirige 
                 */
                if (isset($_SESSION['role']) and $_SESSION['role'] === 'admin') {
                    $_SESSION['messageType'] = 'success';
                    $_SESSION['message']     = "Compte crée avec succès.";

                    \Http::redirect('index.php?ctrl=user&task=render&page=adminSpace&pageTitle=Administration');
                } else {
                    \Http::redirect('index.php?ctrl=user&task=render&page=sign&pageTitle=Félicitation');
                }
            }
        }
    }


    /**
     * Fonction pour supprimer un favoris d'un utilisateur
     */
    public function addFav()
    {
        $productId = self::getId();
        $pageTitle = self::getTitle();
        $favToAdd = $_GET['favName'];
        $user = $this->model->queryOneByMail($_SESSION['mail']);
        $userID = $user['id'];


        //On vérifie que le favoris n'est pas déja dans la liste
        $favExist = strstr($user['fav'], $favToAdd);

        if (!empty($favExist)) {
            $_SESSION['messageType'] = 'error';
            $_SESSION['message']     = 'Vous avez déjà ajouté ce produit à vos favoris. Retourner à la liste des produits en cliquant <a href="index.php?ctrl=produits&task=separateItem&page=produits" alt="Liste des produits">ici</a>.';
            \Http::redirect("index.php?ctrl=produits&task=read&page=produit&pageTitle=$pageTitle&id=$productId");
        } else {
            //On ajoute le favoris
            $favs = explode(', ', $user['fav']);
            array_push($favs, $favToAdd);

            //On met à jour la BDD
            $this->model->updateFav($favs, $userID);


            //On redirige
            $_SESSION['messageType'] = 'success';
            $_SESSION['message']     = 'Le produit a été ajouté à vos favoris. Retourner à la liste des produits en cliquant <a href="index.php?ctrl=produits&task=separateItem&page=produits" alt="Liste des produits">ici</a>.';
            \Http::redirect("index.php?ctrl=produits&task=read&page=produit&pageTitle=$pageTitle&id=$productId");
        }
    }

    /**
     * Fonction pour supprimer un favoris d'un utilisateur
     */
    public function deleteFav()
    {
        $id = self::getId();
        $favToDelete = $_GET['favName'];
        $item = $this->model->queryOneById($id);

        //On supprime le favoris
        $favs = explode(', ', $item['fav']);
        unset($favs[array_search($favToDelete, $favs)]);

        //On met à jour la BDD
        $this->model->updateFav($favs, $id);

        //On redirige
        $_SESSION['messageType'] = 'success';
        $_SESSION['message']     = 'Le produit a bien été supprimer de vos favoris';
        \Http::redirect("index.php?ctrl=user&task=read&page=userSpace&pageTitle=Mon Espace&id=$id");
    }

    /**
     * Fonction pour envoyer un mail de demande de contact
     */
    public function mailContact()
    {
        /**
         * On récupère les informations du contact 
         */
        
        $contactName        = htmlspecialchars($_POST['name']);
        $contactFirstname   = htmlspecialchars($_POST['firstname']);
        $contactmail        = htmlspecialchars($_POST['mail']);
        $contactMessage     = htmlspecialchars($_POST['message']);

        /**
         * On défini les variables pour l'envoi du message 
         */
        
        $to         = 'fares.alib@gmail.com';
        $subject    = 'Contact';

        //Message en syntaxe heredoc
        $message    = <<<HTML
            <h1>Nouvelle demande de contact</h1>
            <h2>Information du contact</h2>
            <p>
                <ul>
                    <li>Nom : $contactName</li>
                    <li>Prénom : $contactFirstname</li>
                    <li>Mail : $contactmail</li>
                </ul>
            </p>
            <h2>Message : $contactMessage</h2>
            <p>
            
            </p>
HTML;

        /**
         * On insère les en-têtes 
         */
        
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        $headers[] = 'From: admin@agriandco.com';

        /**
         * On envoi le mail
         */
        mail($to, $subject, $message, implode("\r\n", $headers));

        /**
         * On redirige
         */
        $_SESSION['messageType'] = 'success';
        $_SESSION['message']     = 'Votre message a bien été envoyé. Merci.';

        \Http::redirect("index.php?ctrl=user&task=render&page=contact&pageTitle=Contact");
    }
}
