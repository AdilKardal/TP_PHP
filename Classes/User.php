<?php

include_once 'connectDB.php';

class User
{
    private ?int $id = null;
    private string $mail;
    private string $password;
    private string $name;
    private string $firstname;
    private string $photo =  '../image/default_user.jpg';

    public function __construct(?string $mail = '', ?string $password = '', ?string $name = '', ?string $firstname = '')
    {
        $this->mail = $mail;
        $this->password = $password;
        $this->name = $name;
        $this->firstname = $firstname;
    }

    #region SETTER & GETTER

    #region MAIL

    /**
     * Get the value of mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set the value of mail
     *
     * @return  self
     */
    public function setMail(string $mail)
    {
        $this->mail = $mail;

        return $this;
    }
    #endregion

    #region PASSWORD
    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }
    #endregion

    #region NAME
    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
    #endregion

    #region FIRSTNAME
    /**
     * Get the value of firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @return  self
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }
    #endregion

    #region PHOTO
    /**
     * Get the value of photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set the value of photo
     *
     * @return  self
     */
    public function setPhoto(string $photo)
    {
        $this->photo = $photo;

        return $this;
    }
    #endregion

    #region ID

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
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    #endregion

    #endregion

    public function __toString()
    {
        $fiche = 'Nom = ' . $this->getName() . PHP_EOL;
        $fiche .= 'Prenom = ' . $this->getFirstname() . PHP_EOL;
        $fiche .= 'Mail = ' . $this->getMail() . PHP_EOL;
        return $fiche;
    }

    /**
     * Vérifie si le mot de passe existe dans la base de données
     *
     * @param string $email vérifie si l'email existe
     * @param string $password vérifie si le password couplé à l'email existe
     * @return boolean Vrai si existe sinon faux
     */
    public function checkUser(string $mail, ?string $password = null): bool
    {
        try {
            $dbh = new PDO(DSN, LOGIN, PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $query = 'select * from utilisateur where upper(email) = :email';
            $param['email'] = strtoupper($mail);

            if (isset($password)) {
                $query .= ' and password = :password';
                $param['password'] = $password;
            }

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

    /**
     * Fonction permettant de charger un utilisateur depuis la base de données pour afficher dans les paramètres
     *
     * @param string $email Email qu'on compare
     * @return boolean
     */
    public function load(string $mail): bool
    {
        try {
            $dbh = new PDO(DSN, LOGIN, PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $statement = $dbh->prepare("select * from utilisateur where upper(email) = :email");
            $mail = strtoupper($mail);

            if (!$statement->execute(['email' => $mail])) {
                // Si $statement->execute() == false, on affiche le code d'erreur
                print '<p>Erreur de récupération des données : ' . $statement->errorCode() . '</p>';
            } else if ($statement->rowCount()) {
                $row = $statement->fetch();
                $this->setId($row['id']);
                $this->setMail($row['email']);
                $this->setPassword($row['password']);
                $this->setName($row['nom']);
                $this->setFirstname($row['prenom']);
                $this->setPhoto($row['photo']);
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

    public function save(): bool
    {
        try {
            $dbh = new PDO(DSN, LOGIN, PASSWORD, array(PDO::ATTR_PERSISTENT => true));
            $param = [
                'email' => $this->getMail(), 'password' => $this->getPassword(),
                'name' => $this->getName(), 'firstname' => $this->getFirstname(),
                'photo' => $this->getPhoto()
            ];
            if ($this->getId()) {
                $statement = $dbh->prepare('UPDATE utilisateur set
                     email = :email,
                     password = :password,
                     nom = :name,
                     prenom = :firstname,
                     photo = :photo 
                     where id =' . $this->getId());
                echo "Mis à jour effectué";
            } else {
                if ($this->checkUser($this->getMail())) {
                    print "Cet email a déjà été utilisé" . PHP_EOL;
                    exit;
                }
                $statement = $dbh->prepare("INSERT INTO utilisateur (id, email, password, nom, prenom, photo) VALUES (NULL, :email, :password, :name, :firstname, :photo)");
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
}