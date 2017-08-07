<?php
require_once 'autoload.php';

class BiddingManager {
	// Get the minimum bid 
	function getMinBidAmt($listingid) {
		$conn = Flight::db();
		$query="SELECT minPrice FROM listings WHERE itemId = $listingid";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as $row) {
			foreach ($row as $key => $values) {
				return $values;
			}		
		}
	}
			
	// Get the current bid points of the user
	function getCurrentBidAmt($userid) {
		$conn = Flight::db();
		$sql = "SELECT bidPts FROM users WHERE userId = $userid";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as $row) {
			foreach ($row as $key => $values) {
				return $values;
			}
		}
	}
    
    // Get all bids placed for the specified listing
    function getBidsForListing($listingId)
    {
        $conn = Flight::db();

        $sql = "SELECT u.username, b.bidderId, b.listingId, b.bidAmt, b.status ";
        $sql = $sql."FROM users u INNER JOIN bids b ON u.userId = b.bidderId ";
        $sql = $sql."WHERE b.listingId = :listingId ";
        $sql = $sql."ORDER BY b.bidAmt DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':listingId', $listingId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function getTopBidsForListing($listingId, $limit)
    {
        $conn = Flight::db();

        $sql = "SELECT DISTINCT bidAmt ";
        $sql = $sql."FROM bids ";
        $sql = $sql."WHERE listingId = :listingId ";
        $sql = $sql."ORDER BY bidAmt DESC LIMIT :limit";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':listingId', $listingId);
        $stmt->bindValue(':limit', $limit);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    function getBidForListingByUser($bidderId, $listingId)
    {
        $conn = Flight::db();

        $sql = "SELECT bidAmt ";
        $sql = $sql."FROM bids ";
        $sql = $sql."WHERE listingId = :listingId ";
        $sql = $sql."AND bidderId = :bidderId";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':listingId', $listingId);
        $stmt->bindValue(':bidderId', $bidderId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    function getBidsByUserId($bidderId)
    {
        $conn = Flight::db();

        $sql = "SELECT l.name, l.itemId, b.bidAmt, b.status ";
        $sql = $sql."FROM bids b INNER JOIN listings l ON b.listingId = l.itemId ";
        $sql = $sql."WHERE b.bidderId = :bidderId";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':bidderId', $bidderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function placeBid($bidderId, $listingId)
    {
        
    }
    
    function updateBid($bidderId, $listingId)
    {
        
    }
    
    function selectBid($listingId, $selectedBidderId) {
        $conn = Flight::db();

        // close unselected bids
        $sql = "UPDATE bids SET status = :status ";
        $sql = $sql."WHERE listingId = :listingId ";
        $sql = $sql."AND bidderId <> :bidderId ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':status', "fail");
        $stmt->bindValue(':listingId', $listingId);
        $stmt->bindValue(':bidderId', $selectedBidderId);
        
        $hasClosedUnselectedBids = $stmt->execute();
        
        // close selected bid
        $sql = "UPDATE bids SET status = :status ";
        $sql = $sql."WHERE listingId = :listingId ";
        $sql = $sql."AND bidderId = :bidderId ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':status', "success");
        $stmt->bindValue(':listingId', $listingId);
        $stmt->bindValue(':bidderId', $selectedBidderId);
        
        $hasClosedSelectedBid = $stmt->execute();
        
        return $hasClosedUnselectedBids && $hasClosedSelectedBid;
    }
    
}