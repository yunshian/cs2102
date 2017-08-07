<?php
require_once 'autoload.php';

class CategoriesManager {
    
    function getAllCategories()
    {
        $conn = Flight::db();

        // retrieve listing
        $sql = "SELECT c.name FROM categories c";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}