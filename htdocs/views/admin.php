        <!-- Display users -->
        <h1>Users</h1>
<?php foreach($users as $user): ?>
        <div class="user">
            <div class="detail">
                <span class="field">Username</span>
                <span class="value"><?=$user['username'] ?></span>
            </div>
            <div class="detail">
                <span class="field">Bid points</span>
                <span class="value"><?=$user['bidpts'] ?></span>
            </div>
			<div class="detail">
                <span class="field">User Type</span>
                <span class="value"><?=$user['userid'] ?></span>
            </div>
			<div class="controls">
				<form method="post">
					<input type="hidden" name="username" value="<?=$user['username']?>">
					<input type="hidden" name="bidpts" value="<?=$user['bidpts']?>">
					<input type="hidden" name="userid" value="<?=$user['userid']?>">
					<input type="hidden" name="usertype" value="<?=$user['usertype']?>">
					
                    <button type="submit" class="profileBtn" formaction="/cs2102/user/<?=$user['userid']?>">Profile</button>
                </form>
			</div>
        </div>
<?php endforeach; ?>
        <!-- End display users -->
        
        <!-- Display listings -->
        <h1>Listings</h1>
<?php foreach($listings as $listing): ?>
        <div class="listing">
            <h1><?=$listing['name']?></h1>
            <p><?=$listing['description']?></p>
            <div>
                <div class="detail">
                    <span class="field">Username</span>
                    <span class="value"><?=$listing['username'] ?></span>
                </div>
                <div class="detail">
                    <span class="field">Available from</span>
                    <span class="value"><?=$listing['pickupdate'] ?></span>
                </div>
                <div class="detail">
                    <span class="field">Available until</span>
                    <span class="value"><?=$listing['returndate'] ?></span>
                </div>
                <div class="detail">
                    <span class="field">Pick up location</span>
                    <span class="value"><?=$listing['pickuplocation'] ?></span>
                </div>
                <div class="detail">
                    <span class="field">Return location</span>
                    <span class="value"><?=$listing['returnlocation'] ?></span>
                </div>
                <div class="detail">
                    <span class="field">Minimum bid</span>
                    <span class="value">$<?=$listing['minprice'] ?></span>
                </div>
            </div>
            <div class="controls">
                <form method="post">
                    <input type="hidden" name="listingId" value="<?=$listing['itemid']?>">
                    <div class="left">
                        <button type="submit" class="deleteBtn" formaction="./deleteListing">Delete</button>
                    </div>
                    <div class="right">
                        <button type="submit" class="editBtn" formaction="./editListing">Edit</button>
                        <button type="submit" class="bidBtn" formaction="./bidding">Bid</button>
                    </div>
                </form>
            </div>
        </div>
<?php endforeach; ?>
        <!-- End listings -->
        
        <!-- Display bids -->
        <h1>Bids</h1>
<?php $itemId = 0;
        $isFirstDiv = true;
?>
        <div class="bid">
<?php
foreach ($bids as $bid) {
    if($bid['itemid'] != $itemId) {
        if ($isFirstDiv) {
            $isFirstDiv = false;
        } else {
            echo '</div>';
            echo '<div class="bid">';
        }
        echo "<h1>{$bid['name']}</h1>";
        $itemId = $bid['itemid'];
    }
?>
            <div class="detail">
                <span class="field">Username</span>
                <span class="value"><?=$bid['username'] ?></span>
            </div>
            <div class="detail">
                <span class="field">Bid</span>
                <span class="value">$<?=$bid['bidamt'] ?></span>
            </div>
<?php } ?>
        
        </div>

        <!-- End display bids -->