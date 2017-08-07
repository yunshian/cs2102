<?php
require_once 'autoload.php';
require_once 'AuthenticationManager.php';

use Valitron\Validator as V;

class ListingsManager {
    
    const NUM_LISTINGS_PER_PAGE = 12;
    
    function getListingById($listingId)
    {
        $conn = Flight::db();

        // retrieve listing
        $sql = "SELECT u.username, l.itemId, l.ownerId,
                l.name, l.description, l.category,
                l.pickupLocation, l.returnLocation,
                l.pickupDate, l.returnDate, l.minPrice, l.status
                FROM listings l INNER JOIN users u ON l.ownerId = u.userId ";
        $sql = $sql."WHERE l.itemId = :itemId";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':itemId', $listingId);
        $stmt->execute();
        return $stmt->fetch();
    }
        
    function getListingsByOwnerId($ownerId)
    {
        $conn = Flight::db();

        // retrieve listing
        $sql = "SELECT u.username, l.itemId, l.ownerId,
                l.name, l.description, l.category,
                l.pickupLocation, l.returnLocation,
                l.pickupDate, l.returnDate, l.minPrice, l.status
                FROM listings l INNER JOIN users u ON l.ownerId = u.userId ";
        $sql = $sql."WHERE l.ownerId = :ownerId";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':ownerId', $ownerId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    function getListingOwner($listingId)
    {
        $conn = Flight::db();

        // retrieve listing
        $sql = "SELECT u.username, u.userId
                FROM listings l INNER JOIN users u ON l.ownerId = u.userId ";
        $sql = $sql."WHERE l.itemId = :itemId";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':itemId', $listingId);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    function getListingsFromPage($page, $lastId, $search)
    {
        $conn = Flight::db();

        // retrieve listings
        $sql = "SELECT u.username, l.itemId,
                l.name, l.description, l.category,
                l.pickupLocation, l.returnLocation,
                l.pickupDate, l.returnDate, l.minPrice
                FROM listings l INNER JOIN users u ON l.ownerId = u.userId";
        if (ctype_digit($lastId)) {
            $sql = $sql." WHERE l.itemId > ".$lastId;
        }
        
        $hasKeyword = !empty(trim($search['keyword']));
        $hasCategory = !empty(trim($search['category']));
        $hasPickupDate = !empty(trim($search['pickupDate']));
        $hasReturnDate = !empty(trim($search['returnDate']));
        
        // generate WHERE clause
        if ($hasKeyword) {
            $sql = $sql." AND lower(l.name) LIKE :keyword";
        }
        if ($hasCategory) {
            $sql = $sql." AND l.category LIKE :category";
        }
        if ($hasPickupDate) {
            $sql = $sql." AND l.pickupDate >= :pickupDate";
        }
        if ($hasReturnDate) {
            $sql = $sql." AND l.returnDate <= :returnDate";
        }
        
        $sql = $sql." ORDER BY l.itemId
                LIMIT ".self::NUM_LISTINGS_PER_PAGE;
        if (!isset($lastId)) {
            $sql = $sql." OFFSET ".(($page - 1) * self::NUM_LISTINGS_PER_PAGE);
        }
        
        $stmt = $conn->prepare($sql);
        
        if ($hasKeyword) {
            $stmt->bindValue(':keyword', '%'.strtolower(trim($search['keyword'])).'%', PDO::PARAM_STR);
        }
        if ($hasCategory) {
            $stmt->bindValue(':category', '%'.strtolower(trim($search['category'])).'%', PDO::PARAM_STR);
        }
        if ($hasPickupDate) {
            $stmt->bindValue(':pickupDate', trim($search['pickupDate']), PDO::PARAM_STR);
        }
        if ($hasReturnDate) {
            $stmt->bindValue(':returnDate', trim($search['returnDate']), PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    function getPagination($page, $lastId, $search)
    {
        $conn = Flight::db();

        $pagination = array();
        $sql = "SELECT COUNT(*) AS c FROM listings";
        $result = $conn->query($sql)->fetchAll();
        $numPages = ceil($result[0]['c'] / (float) self::NUM_LISTINGS_PER_PAGE );
        
        $hasNextPage = $numPages > ($page + 1);
        $pagination['hasNextPage'] = $hasNextPage;
        if ($hasNextPage) {
            $params = http_build_query($search);
            $pagination['nextPage'] = "/cs2102/main/".($page + 1)."/".$lastId
                ."?".$params;
        }
        
        $hasPrevPage = ($page - 1) > 0;
        $pagination['hasPrevPage'] = $hasPrevPage;
        if ($hasPrevPage) {
            $params = http_build_query($search);
            $pagination['prevPage'] = "/cs2102/main/".($page - 1)
                ."?".$params;
        }
        
        return $pagination;
    }
    
    function getListingImagesById($listingId)
    {
        $conn = Flight::db();

        // retrieve listing
        $sql = "SELECT imgPath FROM images ";
        $sql = $sql."WHERE listingId = :listingId ";
        $sql = $sql."ORDER BY position";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':listingId', $listingId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    function createListing($fields)
    {   
        if (!$this->isValidInput($fields)) {
            return false;
        }
        
        $am = new AuthenticationManager();
        $ownerId = $am->getUserId();
        $status = 'open';
        
        // save to db
        $conn = Flight::db();
        
        $sql = "INSERT INTO listings ";
		$sql = $sql."(ownerId, name, description,
                    category, pickupLocation, returnLocation, pickupDate,
                    returnDate, minPrice, status) ";
        $sql = $sql."VALUES (:ownerId, :name, :description,
                    :category, :pickupLocation, :returnLocation, :pickupDate,
                    :returnDate, :minPrice, :status) ";
        $sql = $sql."RETURNING itemId";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":ownerId", $ownerId);
        $stmt->bindValue(":name", $fields['name']);
        $stmt->bindValue(":description", $fields['description']);
        $stmt->bindValue(":category", $fields['category']);
        $stmt->bindValue(":pickupLocation", $fields['pickupLocation']);
        $stmt->bindValue(":returnLocation", $fields['returnLocation']);
        $stmt->bindValue(":pickupDate", $fields['pickupDate']);
        $stmt->bindValue(":returnDate", $fields['returnDate']);
        $stmt->bindValue(":minPrice", $fields['minPrice']);
        $stmt->bindValue(":status", $status);
        
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
			return $result['itemid'];
		}
		
		return false;
    }
    
    function updateListing($itemId, $fields)
    {   
        if (!$this->isValidInput($fields)) {
            return false;
        }
        
        // save to db
        $conn = Flight::db();
        
        $sql = "UPDATE listings SET ";
        $sql = $sql."name = :name, description = :description, category = :category,
                     pickupLocation = :pickupLocation, returnLocation = :returnLocation,
                     pickupDate = :pickupDate, returnDate = :returnDate, minPrice = :minPrice ";
        $sql = $sql."WHERE itemId = :itemId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":name", $fields['name']);
        $stmt->bindValue(":description", $fields['description']);
        $stmt->bindValue(":category", $fields['category']);
        $stmt->bindValue(":pickupLocation", $fields['pickupLocation']);
        $stmt->bindValue(":returnLocation", $fields['returnLocation']);
        $stmt->bindValue(":pickupDate", $fields['pickupDate']);
        $stmt->bindValue(":returnDate", $fields['returnDate']);
        $stmt->bindValue(":minPrice", $fields['minPrice']);
        $stmt->bindValue(":itemId", $itemId);
        
        return $stmt->execute();
    }
    
    function uploadListingImages($listingId, $files) {
        $images = array(); // position, imgPath
        $position = 1;
        foreach ($files as $file) {
            if ($this->isValidJpegOrPng($file)) {
                $info = pathinfo($file['name']);
                $ext = $info['extension']; // get the extension of the file
                
                $tempFilename = $file['tmp_name'];
                $newFilename = $listingId."-img-".$position.".".$ext;
                $targetDir = './imgs/'.$newFilename;
                if (move_uploaded_file($tempFilename, $targetDir)) {
                    $images[] = ['position' => $position, 'imgPath' => $newFilename];
                    $position++;
                }
            }
        }
        
        if (empty($images)) {
            return true; // no images were uploaded
        }
        
        $conn = Flight::db();
        
        // delete any existing images for this listing
        $sql = "DELETE FROM images WHERE listingId = :listingId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":listingId", $listingId);
        $stmt->execute();
        
        $tuples = array(); // (listingId, position, imgPath)
        foreach ($images as $image) {
            $tuples[] = "(".$listingId.", ".$image['position'].", '".$image['imgPath']."')";
        }
        $values = implode(",", $tuples);
        
        // insert images for this listing
        $sql = "INSERT INTO images VALUES ".$values;
        $stmt = $conn->prepare($sql);
        return $stmt->execute();
    }
    
    function closeListing($listingId)
    {
        $conn = Flight::db();

        $sql = "UPDATE listings SET status = :status ";
        $sql = $sql."WHERE itemId = :itemId";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':status', "closed");
        $stmt->bindValue(':itemId', $listingId);
        return $stmt->execute();
    }
    
    function isValidInput($input) {
        $v = new V($input);
        $rules = [
            'name' => ['required'],
            'description' => ['required'],
            'category' => ['required'],
            'pickupLocation'=> ['required'],
            'returnLocation'=> ['required'],
            'pickupDate'=> ['required'],
            'returnDate'=> ['required'],
            'minPrice'=> ['required', 'integer', ['min', 0]]
        ];
        $v->mapFieldsRules($rules);
        return $v->validate();
    }
    
    function isValidJpegOrPng($file) {
        if ($file['size'] > 0) {
            $size = getimagesize($file['tmp_name']);
            return $size[2] === IMAGETYPE_JPEG  || $size[2] === IMAGETYPE_PNG;
        }
        return false;
    }
    
}