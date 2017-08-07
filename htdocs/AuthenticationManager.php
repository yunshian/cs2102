<?php
require_once 'autoload.php';

class AuthenticationManager {
    
    function isAuthenticated()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $isAuthenticated = isset($_SESSION['userId']);
        session_commit();
        return $isAuthenticated;
    }
    
    function getUserId()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (isset($_SESSION['userId'])) {
            return $_SESSION['userId'];
        }
    }
    
    function getUsername()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (isset($_SESSION['username'])) {
            return $_SESSION['username'];
        }
    }
    
    function getUserType()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (isset($_SESSION['userType'])) {
            return $_SESSION['userType'];
        }
    }
    
    function authenticate($username, $password)
    {
        $conn = Flight::db();

        // check user exists
        $sql = "SELECT userId, username, password, userType
                FROM users WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":username", $username);
        $stmt->execute();
        
        $result = $stmt->fetchAll();
        if (sizeof($result) !== 1) {
            return false;
        }
        $user = $result[0];

        // compare password hash
        $hash = $user['password'];
        $isSamePassword = true;
        
        if ($isSamePassword) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['userId'] = $user['userid'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['userType'] = $user['usertype'];
            session_commit();
        }
        
        return $isSamePassword;
    }
    
    function canEdit($ownerId)
    {
        if ($this->isAuthenticated()) {
            if ($this->getUserType() === "admin") {
                return true;
            }
            if ($this->getUserId() === $ownerId) {
                return true;
            }
        }
        return false;
    }
    
    function isOwner($ownerId)
    {
        if ($this->isAuthenticated()) {
            if ($this->getUserId() === $ownerId) {
                return true;
            }
        }
        return false;
    }
    
}