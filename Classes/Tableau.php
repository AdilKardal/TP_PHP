<?php

include_once 'connectDB.php';
require_once 'src/core/security.php';


class Tableau
{
    private ?int $id = null;
    private ?int $userID = null;
    private string $tableName;

    public function __construct(?string $tableName = null)
    {
        $this->tableName = $tableName;
    }

    #region SET & GET
    /**
     * Get the value of id
     */
    public function getId(): ?int
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
    public function getUserID(): ?int
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
     * Get the value of nomTableau
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Set the value of nomTableau
     *
     * @return  self
     */
    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    #endregion

    //Save
    public function save(): bool
    {
        try {
            $dbh = new PDO(DSN, LOGIN, PASSWORD, array(PDO::ATTR_PERSISTENT => true));

            if ($this->getUserID() === null) {
                $this->setUserID();
            }

            $param = ['tableName' => $this->getTableName()];

            var_dump($param);
            if ($this->getId()) {
                $statement = $dbh->prepare('UPDATE tableau set
                      nom_tableau = :tableName
                     where id = ' . $this->getId());
                echo "Mis à jour effectué" . PHP_EOL;
            } else {
                $param['userID'] = $this->getUserID();
                $statement = $dbh->prepare("INSERT INTO tableau (id_utilisateur, nom_tableau) VALUES (:userID, :tableName)");
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

    //Load
    public function load(int $idTable): bool
    {
        try {
            $user = getUser();
            $dbh = new PDO(DSN, LOGIN, PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $statement = $dbh->prepare("select * from tableau where id = :idTable and id_utilisateur = " . $user->getId());

            if (!$statement->execute(['idTable' => $idTable])) {
                // Si $statement->execute() == false, on affiche le code d'erreur
                print '<p>Erreur de récupération des données : ' . $statement->errorCode() . '</p>';
            } else if ($statement->rowCount()) {
                $row = $statement->fetch();
                $this->setId($row['id']);
                $this->setUserID($row['id_utilisateur']);
                $this->setTableName($row['nom_tableau']);
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

    public function checkTable(int $idTab): bool
    {
        try {
            $dbh = new PDO(DSN, LOGIN, PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $user = getUser();
            $query = 'select * from tableau where id = :idTab and id_utilisateur = ' . $user->getId();
            $param['idTab'] = $idTab;

            echo $idTab;

            $statement = $dbh->prepare($query);

            if (!$statement->execute($param)) {
                print '<p>Erreur de récupération des données : ' . $statement->errorCode() . '</p>';
            } else if ($statement->rowCount()) {
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

    public function __toString()
    {
        return 'Nom du tableau = ' . $this->getTableName() . PHP_EOL;
    }

    //Sortir un tableau avec toutes les taches
}