<?php

require_once 'Classes/User.php';
require_once 'Classes/Tableau.php';
include_once 'Classes/connectDB.php';

function connexion(string $mail, string $password): bool
{
    $user = new User();
    if ($user->checkUser($mail, $password)) {
        $_SESSION['user'] = $mail;
        return true;
    } else {
        session_destroy();
        return false;
    }
}

function deconnexion(): bool
{
    session_destroy();
    return true;
}

function isConnected(): bool
{
    if (isset($_SESSION['user'])) {
        return true;
    } else {
        return false;
    }
}

function getUser(): ?User
{
    if (isConnected()) {
        $user = new User();
        $user->load($_SESSION['user']);
        return $user;
    } else {
        return null;
    }
}

function selectTable(): bool
{
    try {
        $idtab = $_POST['table'];

        $dbh = new PDO(DSN, LOGIN, PASSWORD, array(PDO::ATTR_PERSISTENT => true));
        $statement = $dbh->prepare('select * from tableau where id = :idTab ');

        if (!$statement->execute(['idTab' => $idtab])) {
            print '<p>Erreur de récupération des données : ' . $statement->errorCode() . '</p>';
        } else if ($statement->rowCount()) {

            $row = $statement->fetch();
            $_SESSION["idTableau"] = $row['id'];

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

function isSelected(): bool
{
    if (isset($_SESSION['idTableau'])) {
        return true;
    } else {
        return false;
    }
}

function getTable(): ?Tableau
{
    if (isSelected()) {
        $table = new Tableau();
        $table->load($_SESSION['idTableau']);
        return $table;
    } else {
        return null;
    }
}