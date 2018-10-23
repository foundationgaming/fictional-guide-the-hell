<?php

namespace quotemaker\dao;

use \PDO;

class UserDAO {

    private $db = NULL;

    function __construct($theDB)
    {
        $this->db = $theDB;
    }

    public function loadUserByUsername($username)
    {
        $sql = "SELECT username, password, roles FROM users WHERE username = :username AND deleted = 0";
        $statement = $this->db->prepare($sql);
        $statement->bindParam("username", $username);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    public function getAllUsers()
    {
        $sql = "SELECT id, username, real_name, email_address FROM users WHERE deleted = 0";
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id, $c_args)
    {
        $sql = "SELECT id, username, real_name as realName, email_address as emailAddress, roles FROM users where id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindParam("id", $id);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\User", $c_args);
        return $result;
    }


    public function getUserByUsername($username)
    {
        $sql = "SELECT id, username, real_name as realName, email_address as emailAddress, roles FROM users where username = :username AND deleted = 0";
        $statement = $this->db->prepare($sql);
        $statement->bindParam("username", $username);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\User");
        return $result;
    }

    public function insertUser($user)
    {
        $sql = <<<SQL
        INSERT INTO users(username, password, roles, real_name, email_address)
        VALUES (:username, :password, :roles, :realName, :emailAddress)
        ON DUPLICATE KEY UPDATE
            username = :username2,
            password = :password2,
            roles = :roles2,
            real_name = :realName2,
            email_address = :emailAddress2
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("username", $user->getUsername());
        $statement->bindValue("username2", $user->getUsername());
        $statement->bindValue("password", $user->getEncryptedPassword());
        $statement->bindValue("password2", $user->getEncryptedPassword());
        $statement->bindValue("roles", $user->getRoles());
        $statement->bindValue("roles2", $user->getRoles());
        $statement->bindValue("realName", $user->getRealName());
        $statement->bindValue("realName2", $user->getRealName());
        $statement->bindValue("emailAddress", $user->getEmailAddress());
        $statement->bindValue("emailAddress2", $user->getEmailAddress());

        $statement->execute();
        return $statement->rowCount();
    }

    public function updateUser($user)
    {
        $sql = <<<SQL
        update users
        set real_name = :realName,
        email_address = :emailAddress
        where id = :userId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("userId", $user->getId());
        $statement->bindValue("realName", $user->getRealName());
        $statement->bindValue("emailAddress", $user->getEmailAddress());
        $statement->execute();
        return $statement->rowCount();
    }

    public function updatePassword($user)
    {
        $sql = <<<SQL
        update users
        set password = :password
        where id = :userId
SQL;

        $statement = $this->db->prepare($sql);
        $statement->bindValue("password", $user->getEncryptedPassword());
        $statement->bindValue("userId", $user->getId());
        $statement->execute();
        return $statement->rowCount();
    }

    /*
    public function updateUserTokens($username, $accessToken, $refreshToken, $tokenExpires)
    {
        
        $sql = <<<SQL
        update users
        set access_token = :accessToken,
        refresh_token = :refreshToken,
        token_expires = :tokenExpires
        where username = :username
SQL;

        $statement = $this->db->prepare($sql);
        $statement->bindValue("accessToken", $accessToken);
        $statement->bindValue("refreshToken", $refreshToken);
        $statement->bindValue("tokenExpires", $tokenExpires);
        $statement->bindValue("username", $username);
        $statement->execute();
        return $statement->rowCount();
    }
    */
    
    public function deleteUser($userId)
    {
        $sql = <<<SQL
        update users
        set deleted = 1
        where id = :userId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("userId", $userId);
        $statement->execute();
        return $statement->rowCount();
    }

}
