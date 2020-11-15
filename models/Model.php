<?php

namespace Models;

use Exception;

abstract class Model
{

    protected $pdo;
    protected $table;

    public function __construct()
    {
        $this->pdo = \Database::getPdo();
    }

    /**
     * Requete pour inserer des données dans une table
     * @param array @fields
     * @param array @values
     * @return void
     */
    public function create(array $fields, array $values): void
    {
        //On regarde combien de champs nous avons, on retire 2 pour le champs 0 et le champ id qu'on ne souhaite pas compter
        $nbrFields  = count($fields) - 1;
        $v          = "?";
        for ($i = 0; $i < $nbrFields; $i++) {
            $v .= ",?";
        }

        //On assemble les clés et les valeurs pour les transmettre à la requête
        $fieldAndValue = [];

        for ($i = 1; $i < count($fields); $i++) {
            array_push($fieldAndValue, $fields[$i]);
        }
        $realFields = implode(', ', $fields);


        //On prépare la requête et on la lance
        $sql = "INSERT INTO {$this->table} ($realFields) VALUES ($v)";
        $this->pdo->prepare($sql)->execute($values);
    }

    /**
     * Requete pour inserer une imaged dans un dossier en fonction 
     * @param string $file
     * @param string $fileName
     * @param string $extension
     * @param string $repertoire
     * @return void
     */
    public function createImg(string $file, string $fileName, string $extension, string $repertoire): void
    {
        //Répertoire de destination 
        $dest = 'library/img/' . $repertoire . '/';

        //Formatage du nom
        $finalName = $fileName . "." . $extension; //Nom final

        //Déplacement du fichier du répertoire temporaire vers repertoire de destination
        move_uploaded_file($file, $dest . $finalName);
    }
    /**
     * Requete pour obtenir tous les resultats d'une table
     * @param array $options
     * @return array
     */
    public function queryAll(?array $options): array
    {
        if (isset($options['table'])) {
            $table = $options['table'];
            $sql = "SELECT * FROM $table";
        } else {
            $sql = "SELECT * FROM {$this->table}";
        }

        if (isset($options['order'])) {
            $order = $options['order'];
            $sql .= " ORDER BY " . $order;
        }
        $resultats = $this->pdo->query($sql);
        $items = $resultats->fetchAll();

        return $items;
    }

    /**
     * Requete pour obtenir un resultat d'une table par id
     * @param integer $id
     * @return array or bool
     */
    public function queryOneById(int $id)
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $query->execute(['id' => $id]);
        $item = $query->fetch();

        return $item;
    }

    /**
     * Requete pour obtenir un resultat d'une table par mail
     * @param string $mail
     * @return array or bool
     */
    public function queryOneByMail(string $mail)
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE mail = :mail");
        $query->execute(['mail' => $mail]);
        $item = $query->fetch();

        return $item;
    }

    /**
     * Requete pour mettre à jour un élément d'une table
     * @param integer $id
     * @param array $fields
     * @param array $values
     * @return void
     */
    public function update(int $id, array $fields, array $values): void
    {
        //On regarde combien de champs nous avons, on retire 2 pour le champs 0 et le champ id qu'on ne souhaite pas compter
        $nbrFields  = count($fields) - 2;
        $v          = "?";


        //On assemble les clés et les valeurs pour les transmettre à la requête
        $fieldAndValue = [];

        for ($i = 1; $i < count($fields); $i++) {
            $f = $fields[$i] . "=" . $v;
            array_push($fieldAndValue, $f);
        }
        $realFields = implode(', ', $fieldAndValue);
        array_splice($values, 0, 1);
        array_push($values, $id);


        //On prépare la requête et on la lance
        $sql = "UPDATE {$this->table} SET $realFields WHERE id = ?";

        $this->pdo->prepare($sql)->execute($values);
       
    }

    /**
     * Requete pour mettre à jour les favoris d'un user
     * @param array $favs
     * @param int $id
     * @return void
     */
    public function updateFav(array $favs, int $id): void
    {
        $item = self::queryOneById($id);


        //On prépare la requête et on la lance
        $sql = "UPDATE {$this->table} SET fav=? WHERE id = ?";

        if($item['fav'] == ''){
            $values = implode($favs);
        }else{
            $values = implode(', ', $favs);
        }

            $this->pdo->prepare($sql)->execute([$values, $id]);
        
    }

    /**
     * Requete pour mettre à jour l'image d'un élément d'une table
     * @param integer $id
     * @param string $file
     * @param string $fileName
     * @param string $extension
     * @param string $repertoire
     * @return void
     */
    public function updateImg(int $id, string $file, string $fileName, string $extension, string $repertoire): void
    {
        //Répertoire de destination 
        $dest = 'library/img/' . $repertoire . '/';

        //Formatage du nom
        $finalName = $fileName . date("YmdHis") . "." . $extension; //Nom final

        //Déplacement du fichier du répertoire temporaire vers repertoire de destination
        move_uploaded_file($file, $dest . $finalName);

        //On met à jour le nom du fichier dans la BBD
        $sql = "UPDATE {$this->table} SET img = ? WHERE id = ?";
        $this->pdo->prepare($sql)->execute([$finalName, $id]);
    }

    /**
     * Requete pour supprimer un élément d'une table
     * 
     * @param integer $id
     * @return void
     */
    public function delete(int $id): void
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $query->execute(['id' => $id]);
    }

    /**
     * Requete pour supprimer l'image d'un élément d'une table
     * 
     * @param string $img
     * @param string $repertoire
     * @return void
     */
    public function deleteImg($img, $repertoire)
    {
        $img = 'library/img/' . $repertoire . '/' . $img;
        if (file_exists($img)) {
            unlink($img);
        }
    }

    /**
     * Crypte le mot de passe transmis
     * 
     * @param $password
     * return $hashedPassword
     */
    public function hashPassword($password)
    {
        $hashedPassword = password_hash(htmlspecialchars($password), PASSWORD_DEFAULT);
        return $hashedPassword;
    }
}
