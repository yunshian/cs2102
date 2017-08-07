<?php
require_once 'autoload.php';
require_once 'ListingsManager.php';
require_once 'CategoriesManager.php';
require_once 'AuthenticationManager.php';
require_once 'UserManager.php';
require_once 'BiddingManager.php';

class Controller
{

    function displayBooks()
    {
        $title = Flight::request()->data['title'];
        if ($title == NULL) {
            $searchtitle = "%%";
        } else {
            $searchtitle = "%".strtolower($title)."%";
        }
        $title = htmlspecialchars($title);
        $conn = Flight::db();
        $sql = "SELECT * FROM book WHERE lower(title) LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array($searchtitle));
        $data = $stmt->fetchAll();
        Flight::render('demo',array('books'=>$data, 'title'=>$title));
    }

function displayMainPage()
{
    Flight::register('db', 'MyPDO');
    $conn = Flight::db();

    // retrieve users
    $sql = "SELECT u.username, u.bidPts, u.userId, u.userType
            FROM users u";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll();

    // retrieve listings
    $sql = "SELECT u.username, l.itemId,
            l.name, l.description, l.category,
            l.pickupLocation, l.returnLocation,
            l.pickupDate, l.returnDate, l.minPrice
            FROM listings l INNER JOIN users u ON l.ownerId = u.userId";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $listings = $stmt->fetchAll();
    
    // retrieve bids
    $sql = "SELECT u2.username, l.name, b.bidAmt, l.itemId
            FROM bids b INNER JOIN listings l ON l.itemId = b.listingId
            INNER JOIN users u1 ON l.ownerId = u1.userId
            INNER JOIN users u2 ON b.bidderId = u2.userId
            ORDER BY l.itemId";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $bids = $stmt->fetchAll();

    Flight::render('admin', array('users'=>$users, 'listings'=>$listings, 'bids'=>$bids), 'body_content');
    Flight::render('layout', array('title' => 'Main'));
}

    function displayAllListings($page, $lastId)
    {
        $lm = new ListingsManager();
        
        $search = array();
        $params = array('keyword', 'category', 'pickupDate', 'returnDate');
        foreach ($params as $param) {
            if (isset($_GET[$param])) {
                $search[$param] = $_GET[$param];
            } else {
                $search[$param] = '';
            }
        }
        
        if (!ctype_digit($page)) {
            $page = 1;
        }
        
        $listings = $lm->getListingsFromPage($page, $lastId, $search);
        
        $sanitizedListings = array();
        foreach ($listings as $listing) {
            $sanitizedListing = array();
            foreach ($listing as $key => $unsanitizedField) {
                $sanitizedListing[$key] = htmlspecialchars($unsanitizedField);
            }
            $sanitizedListings[] = $sanitizedListing;
        }
        
        if (!empty($listings)) {
            $lastIndex = sizeof($listings) - 1;
            $lastListing = $listings[$lastIndex];
            $pagination = $lm->getPagination($page, $lastListing['itemid'], $search);
        } else {
            $pagination = array('hasPrevPage'=>false,'hasNextPage'=>false);
        }
        
        $cm = new CategoriesManager();
        $categories = $cm->getAllCategories();
        
        $params = array('keyword'=>FILTER_SANITIZE_FULL_SPECIAL_CHARS, 'category'=>FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        foreach ($params as $key=>$value) {
            $search[$key] = filter_var(trim($search[$key]), $value);
        }
        
        Flight::render('main', array('listings'=>$sanitizedListings,'pagination'=>$pagination,'categories'=>$categories,'search'=>$search), 'body_content');
        Flight::render('layout', array('title' => 'Listings'));
    }

    function displayListing($itemId)
    {
        $lm = new ListingsManager();
        $listing = $lm->getListingById($itemId);
        
        $sanitizedListing = array();
        foreach ($listing as $key => $unsanitizedField) {
            $sanitizedListing[$key] = htmlspecialchars($unsanitizedField);
        }
        
        $images = $lm->getListingImagesById($itemId);
        $sanitizedListing['images'] = $images;
        
        if ($listing) {
            $am = new AuthenticationManager();
            $canEdit = $am->canEdit($listing['ownerid']);
            $isOwner = $am->isOwner($listing['ownerid']);
            $bm = new BiddingManager();
            $bids = $bm->getBidsForListing($itemId);
            Flight::render('listingDetails', array('listing'=>$sanitizedListing, 'canEdit'=>$canEdit, 'isOwner' => $isOwner, 'bids'=>$bids), 'body_content');
            Flight::render('layout', array('title' => 'Listing Details'));
        } else {
            Flight::redirect('/error');
        }
    }

    function displayCreateListing()
    {
        $am = new AuthenticationManager();
        if (!$am->isAuthenticated()) {
            Flight::redirect('/error');
            return;
        }
        
        // retrieve input
        $input = array();
        $params = array('name', 'description', 'category', 
                        'pickupLocation', 'returnLocation',
                        'pickupDate', 'returnDate', 'minPrice');
        foreach ($params as $param) {
            if (isset($_POST[$param])) {
                $input[$param] = $_POST[$param];
            } else {
                $input[$param] = null;
            }
        }
        
        $sanitizedInput = array();
        foreach ($input as $key => $unsanitizedInput) {
            $sanitizedInput[$key] = htmlspecialchars($unsanitizedInput);
        }
        
        $cm = new CategoriesManager();
        $categories = $cm->getAllCategories();
        
        if (!isset($_POST['submit'])) {
            Flight::render('createListing', array('input'=>$sanitizedInput,'categories'=>$categories), 'body_content');
            Flight::render('layout', array('title' => 'Create Listing'));
            return;
        }
        
        $lm = new ListingsManager();
        $listingId = $lm->createListing($input);
        if ($listingId) {
            $isSuccessfulImgUpload = $lm->uploadListingImages($listingId, $_FILES);
            if ($isSuccessfulImgUpload) {
                Flight::redirect('/listing/'.$listingId);
                return;
            }
        }
        
        // listing creation or image upload failed
        Flight::render('createListing', 
                       array('isSuccess' => false, 'errMsg' => 'Listing creation failed', 
                             'input' => $sanitizedInput, 'categories' => $categories), 
                       'body_content');
        Flight::render('layout', array('title' => 'Create Listing'));
        return;
    }

    function selectBid($listingId, $bidderId)
    {
        $am = new AuthenticationManager();
        
        if ($am->isAuthenticated()) {
            $lm = new ListingsManager();
            $owner = $lm->getListingOwner($listingId);
            if ($am->getUserId() === $owner['userid']) {
                $bm = new BiddingManager();
                $isSuccessful = $bm->selectBid($listingId, $bidderId);
                $isSuccessful = $isSuccessful && $lm->closeListing($listingId);
                if ($isSuccessful) {
                    Flight::redirect('/listing/'.$listingId);
                    return;
                }
            }
        }
        
        Flight::redirect('/error');
        return;
    }
    
    function addBid($listingId, $bidderId)
    {
        $am = new AuthenticationManager();
        
        if ($am->isAuthenticated()) {
            $lm = new ListingsManager();
            $owner = $lm->getListingOwner($listingId);
            if ($am->getUserId() !== $owner['userid']) {
                $bm = new BiddingManager();
                $isSuccessful = $bm->placeBid($listingId, $bidderId);
                if ($isSuccessful) {
                    Flight::redirect('/listing/'.$listingId);
                    return;
                }
            }
        }
        
        Flight::redirect('/error');
        return;
    }
    
    function login()
    {
        $am = new AuthenticationManager();
        
        if ($am->isAuthenticated()) {
            Flight::redirect('/main');
        } else {
            Flight::render('login', array(), 'body_content');
            Flight::render('layout', array('title' => 'Log in'));
        }
    }
    
    function validateLogin()
    {
        Flight::register('db', 'MyPDO');
        $conn = Flight::db();

        $username = Flight::request()->data['username'];
        $password = Flight::request()->data['password'];

        $am = new AuthenticationManager();
        if ($am->authenticate($username, $password)) {
            Flight::redirect('/main');
        } else {
            Flight::redirect('/login');
        }
    }
    
    function logout()
    {
        session_start();
        if (isset($_SESSION['userId'])) {
            session_destroy();
        }
        Flight::redirect('/main');
    }

    function displaySignUpPage()
    {
        $am = new AuthenticationManager();
        
        if ($am->isAuthenticated()) {
            Flight::redirect('/main');
        } else {
            $isSuccess = false;
            $usernameTaken = false;
            
            if (isset($_POST['submit'])){
                Flight::register('db', 'MyPDO');
                $conn = Flight::db();
                $sql = "SELECT username FROM users";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll();

                // Check if username exist in the database
                foreach ($result as $row) {
                    foreach ($row as $key => $values) {
                        $username = htmlspecialchars($_POST["username"]);
                        if (strcmp($key, 'username') == 0 && strcmp($values, $username) == 0) {
                            $usernameTaken = true;
                            break;
                        }
                    }
                }
                
                $hashedPassword = password_hash(htmlspecialchars($_POST["password"]), PASSWORD_DEFAULT);
                $username = htmlspecialchars($_POST["username"]);
                $query = "INSERT INTO users (username, password, usertype, bidpts) VALUES ('$username', '$hashedPassword', 'normal', 1000)";
                $stmt = $conn->prepare($query);

                $isSuccess = $stmt->execute();
            }
            
            Flight::render('signUp', array('usernameTaken'=>$usernameTaken,'success'=>$isSuccess), 'body_content');
            Flight::render('layout', array('title' => 'Sign up'));
        }
    }

    function displayBiddingPage($itemId)
    {
		$am = new AuthenticationManager();
		$bm = new BiddingManager();
        $lm = new ListingsManager();
		
		$increaseBid = false;
		$updateBidAmtNegative = false;
		$success = false;
        $successDelete = false;
		
        $conn = Flight::db();
		$userid = $am->getUserId();
		$listingid = $itemId;
      
        // get top five bids
        $conn = Flight::db();
		$query="SELECT bidAmt FROM bids WHERE listingId = $listingid ORDER BY bidAmt DESC LIMIT 5";
		$stmt = $conn->prepare($query);
        if ($stmt->execute()) {
            $top5Bids = $stmt->fetchAll();
        } else {
            $top5Bids = [];
        }
      
        // check if listing is closed
        $listing = $lm->getListingById($itemId);
        $isClosed = $listing['status'] === "closed";
        if ($isClosed) {
          Flight::render('biddingClosed', array('listingId' => $itemId, 'top5Bids' => $top5Bids), 'body_content');
          Flight::render('layout', array('title' => 'Place a bid'));
          return;
        }
      
        $isAuthenticated = $am->isAuthenticated();
        $isSuccess = false;
        $message = "";

        // bid validation
		if(isset($_POST['updateBtn'])) {
			$increaseBid = false;
			$updateBidAmtNegative = false;
			$success = false;
			$bidAmt =  $_POST['bidAmt'];
			$minBid = $bm->getMinBidAmt($listingid);
			
			// Check if user has already bid for this item
			$sql = "SELECT bidAmt FROM bids WHERE listingId = $listingid AND bidderid = $userid";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll();
			$currentBidAmt = 0;
			foreach ($result as $row) {
				foreach ($row as $key => $values) {
					$currentBidAmt = $values;
					break;
				}
			}

			if ($bidAmt < $minBid) {
				$isSuccess = false;
                $message = "Please increase your bid";
			} else {
				$currentAccountBid = $bm->getCurrentBidAmt($userid);
				$updateBidAmt = 0;
				if ($bidAmt > $currentBidAmt) {
					$updateBidAmt = $currentAccountBid - ($bidAmt - $currentBidAmt);
				} else {
					$updateBidAmt = $currentAccountBid + ($currentBidAmt - $bidAmt);
				}
				
				if ($updateBidAmt < 0) {
					$isSuccess = false;
                    $message = "Insufficient bid points";	
				} else {
					// Update user and bids table accordingly
					$sql = "UPDATE users SET bidPts = $updateBidAmt WHERE userId = $userid";
					$stmt = $conn->prepare($sql);
					$stmt->execute();
					
					$query="UPDATE bids SET bidAmt = $bidAmt WHERE listingId = $listingid AND bidderid = $userid";
					$stmt = $conn->prepare($query);
					$stmt->execute();
					
					$isSuccess = true;
                    $message = "You have updated your bid successfully";
				}
			}
		}
		
		if(isset($_POST['submitBtn'])) {
			$increaseBid = false;
			$updateBidAmtNegative = false;
			$success = false;
			$bidAmt =  $_POST['bidAmt'];
			$minBid = $bm->getMinBidAmt($listingid);
			
			if ($bidAmt < $minBid) {
				$isSuccess = false;
                $message = "Please increase your bid";
			} else {
				$currentBidAmt = $bm->getCurrentBidAmt($userid);
				$updateBidAmt = $currentBidAmt - $bidAmt;
				if ($updateBidAmt < 0) {
					$updateBidAmtNegative = true;
				} else {
					// Update user table accordingly
					$sql = "UPDATE users SET bidPts = $updateBidAmt WHERE userId = $userid";
					$stmt = $conn->prepare($sql);
					$stmt->execute();
							
					//Insert into bids table
					$query="INSERT INTO bids (bidderId, listingId, bidAmt, status) VALUES ('$userid','$listingid','$bidAmt','pending')";
					$stmt = $conn->prepare($query);
					$stmt->execute();
					$isSuccess = true;
                    $message = "You have placed your bid successfully";
				}
			}
		}

        if(isset($_POST['deleteBtn'])) {
			$successDelete = false;
            
            // Check if user has already bid for this item
			$sql = "SELECT bidAmt FROM bids WHERE listingId = $listingid AND bidderid = $userid";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll();
			$currentBidAmt = 0;
			foreach ($result as $row) {
				foreach ($row as $key => $values) {
					$currentBidAmt = $values;
					break;
				}
			}

            //delete from bids table
            $sql = "DELETE FROM bids WHERE bidderId = $userid AND listingId = $listingid";
            $stmt = $conn->prepare($sql);
			$stmt->execute();

            //add back points to user
            $currentAccountBid = $bm->getCurrentBidAmt($userid);
            $updateBidAmt = $currentAccountBid + $currentBidAmt;

            // Update user table 
            $sql = "UPDATE users SET bidPts = $updateBidAmt WHERE userId = $userid";
			$stmt = $conn->prepare($sql);
			$stmt->execute();

            $isSuccess = true;
            $message = "You have deleted your previous bid successfully";
		}
		
        // check if user has already bid for this item
        $sql = "SELECT bidAmt FROM bids WHERE listingId = $listingid AND bidderid = $userid";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result) {
            $currentBidAmt = $result['bidamt'];
            $hasBid  = true;
        } else {
            $currentBidAmt = 0;
            $hasBid = false;
        }

		Flight::render('bidding', array('listingId' => $itemId, 'top5Bids' => $top5Bids, 'currentBidAmt' => $currentBidAmt, 'isAuthenticated' => $isAuthenticated, 'hasBid' => $hasBid, 'isSuccess' => $isSuccess, 'message' => $message), 'body_content');
        Flight::render('layout', array('title' => 'Place a bid'));
    }
	
    function displayEditListingPage($itemId)
    {
        $am = new AuthenticationManager();
        if (!$am->isAuthenticated()) {
            Flight::redirect('/error');
            return;
        }
        
        // retrieve listing details
        $lm = new ListingsManager();
        $listing = $lm->getListingById($itemId);
        
        $canEdit = $am->canEdit($listing['ownerid']);
        if (!$canEdit || $listing['status'] === "closed") {
            Flight::redirect('/error');
            return;
        }
        
        $sanitizedListing = array();
        foreach ($listing as $key => $unsanitizedField) {
            $sanitizedListing[$key] = htmlspecialchars($unsanitizedField);
        }
        
        $images = $lm->getListingImagesById($itemId);
        $sanitizedListing['images'] = $images;
        
        $sanitizedListing = array();
        foreach ($listing as $key => $unsanitizedField) {
            $sanitizedListing[$key] = htmlspecialchars($unsanitizedField);
        }
        
        $images = $lm->getListingImagesById($itemId);
        $sanitizedListing['images'] = $images;
        
        $cm = new CategoriesManager();
        $categories = $cm->getAllCategories();
        
        if (!isset($_POST['submit'])) {
            Flight::render('editListing',
                           array('input'=>$sanitizedListing,'categories'=>$categories), 'body_content');
            Flight::render('layout', array('title' => 'Edit Listing'));
            return;
        }
        
        // retrieve input
        $input = array();
        $params = array('name', 'description', 'category', 
                        'pickupLocation', 'returnLocation',
                        'pickupDate', 'returnDate', 'minPrice');
        foreach ($params as $param) {
            if (isset($_POST[$param])) {
                $input[$param] = $_POST[$param];
            } else {
                $input[$param] = null;
            }
        }
        
        $lm = new ListingsManager();
        $isUpdated = $lm->updateListing($itemId, $input);
        if ($isUpdated) {
            Flight::redirect('/listing/'.$itemId);
            return;
        }
        
        $sanitizedInput = array();
        foreach ($input as $key => $unsanitizedInput) {
            $sanitizedInput[strtolower($key)] = htmlspecialchars($unsanitizedInput);
        }
        
        // listing edit or image upload failed
        Flight::render('editListing', 
                       array('isSuccess' => false, 'errMsg' => 'Listing edit failed', 
                             'input' => $sanitizedInput, 'categories' => $categories), 
                             'body_content');
        Flight::render('layout', array('title' => 'Edit Listing'));
        return;
    }

    function deleteListing($itemId)
    {
        $am = new AuthenticationManager();
        $lm = new ListingsManager();
        $listing = $lm->getListingById($itemId);
        $canEdit = $am->canEdit($listing['ownerid']);
        
        if ($canEdit) {
            $conn = Flight::db(); 
            $query = "DELETE FROM listings WHERE itemId = :itemId";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":itemId", $itemId);
            if ($stmt->execute()) {
                Flight::redirect('/main');
                return;
            }
        }
        
        Flight::redirect('/error');
    }
	
	function userProfile($userId)
	{
        $um = new UserManager();
        $user = $um->getUserByUserId($userId);
        
        $sanitizedUser = array();
        foreach ($user as $key => $unsanitizedField) {
            $sanitizedUser[$key] = htmlspecialchars($unsanitizedField);
        }
        
        $lm = new ListingsManager();
        $listings = $lm->getListingsByOwnerId($userId);
        
        $sanitizedListings = array();
        foreach ($listings as $listing) {
            $sanitizedListing = array();
            foreach ($listing as $key => $unsanitizedField) {
                $sanitizedListing[$key] = htmlspecialchars($unsanitizedField);
            }
            $sanitizedListings[] = $sanitizedListing;
        }
        
        $am = new AuthenticationManager();
        $canEdit = false;
        if (sizeof($listings) > 0) {
            $canEdit = $am->canEdit($listings[0]['ownerid']);
        }
        
        $bm = new BiddingManager();
        $bids = $bm->getBidsByUserId($userId);
        
		Flight::render('userProfile',
                      array('user' => $sanitizedUser, 'canEdit' => $canEdit,
                            'listings' => $sanitizedListings, 'bids' => $bids),
                            'body_content');
        Flight::render('layout', array('title' => 'User Profile'));
	}

}
?>
