<?php

include_once 'connectDB.php';
require_once 'src/core/security.php';

class Categorie
{

    private $id;
    private $userID;
    private $category_name;

    public function __construct(string $category_name)
    {
        $this->category_name = $category_name;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of userID
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Set the value of userID
     *
     * @return  self
     */
    public function setUserID(): void
    {
        if ($user = getUser()) {

            $dbh = new PDO(DSN, LOGIN, PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $statement = $dbh->prepare("SELECT * FROM utilisateur WHERE upper(email) = :mail");

            if (!$statement->execute(['mail' => $user->getMail()])) {
                // Si $statement->execute() == false, on affiche le code d'erreur
                print '<p>Erreur de récupération des données : ' . $statement->errorCode() . '</p>';
            }

            $row = $statement->fetch();
            $this->userID = $row['id'];
        }
    }

    /**
     * Get the value of category_name
     */
    public function getCategory_name()
    {
        return $this->category_name;
    }

    /**
     * Set the value of category_name
     *
     * @return  self
     */
    public function setCategory_name(string $category_name)
    {
        $this->category_name = $category_name;

        return $this;
    }

    /**
     * Sauvegarde dans la base de données ou mets à jour si déjà existant
     *
     * @return boolean
     */
    public function save(): bool
    {
        try {
            $dbh = new PDO(DSN, LOGIN, PASSWORD, array(PDO::ATTR_PERSISTENT => true));

            if ($this->getUserID() === null) {
                $this->setUserID();
            }

            $param = [
                'userID' => $this->getUserID(), 'categoryName' => $this->getCategory_name(),
            ];
            if ($this->getId()) {
                $statement = $dbh->prepare('UPDATE categorie set
                      nom_categorie = :categoryName
                     where id =' . $this->getId());
                echo "Mis à jour effectué";
            } else {
                $statement = $dbh->prepare("INSERT INTO categorie (id, id_utilisateur, nom_categorie) VALUES (NULL, :userID, :categoryName)");
                echo "Insertion effectuée";
            }

            if (!$statement->execute($param)) {
                // Si $statement->execute() == false, on affiche le code d'erreur
                print '<p>Erreur de récupération des données : ' . $statement->errorCode() . '</p>';
                return false;
            }

            return true;
        } catch (PDOException $e) {
            print "Erreur!:" . $e->getMessage() . "<br/>";
            die();
        }
    }

    /**
     * Charge depuis la base de données
     *
     * @param string $CategoryName
     * @return boolean
     */
    public function load(string $CategoryName): bool
    {
        try {
            $user = getUser();
            $dbh = new PDO(DSN, LOGIN, PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $statement = $dbh->prepare("select * from tableau where upper(nom_tableau) = :tableName and id_utilisateur = " . $user->getId());
            $CategoryName = strtoupper($CategoryName);

            if (!$statement->execute(['tableName' => $CategoryName])) {
                // Si $statement->execute() == false, on affiche le code d'erreur
                print '<p>Erreur de récupération des données : ' . $statement->errorCode() . '</p>';
            } else if ($statement->rowCount()) {
                echo 'test';
                $row = $statement->fetch();
                $this->setId($row['id']);
                $this->setUserID($row['id_utilisateur']);
                $this->setCategory_name($row['nom_categorie']);
                $dbh = null;
                return true;
            }
            $dbh = null;
            return false;
        } catch (PDOException $e) {
            print "Erreur!:" . $e->getMessage() . "<br/>";
            die();
        }
    }
}