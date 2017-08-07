<?php
require_once 'autoload.php';

class UserManager {

    function getUserByUserId($userId) {
		$conn = Flight::db();
        $sql = "SELECT userId, username, userType, bidPts
                FROM users WHERE userId = :userId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":userId", $userId);
        $stmt->execute();
        
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
	}
    
}