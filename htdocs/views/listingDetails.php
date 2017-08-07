<!-- Start display listing -->
<h1>Listing Details</h1>
<div class="row">
<?php if (sizeof($listing['images'])): ?>
    <div class="col-md-6">
<?php foreach($listing['images'] as $image): ?>
        <img class="imgs" src="<?="/cs2102/imgs/".$image['imgpath'] ?>" >
        <br><br>
<?php endforeach; ?>
    </div>
<?php endif; ?>
    <div class="col-md-6">
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
    </div>
    <!-- Start button controls -->
    <div class="controls">
        <div class="left">
    <?php if ($canEdit): ?>        
            <a href="/cs2102/deleteListing/<?=$listing['itemid'] ?>" class="button deleteBtn" >Delete</a>
        <?php if ($listing['status'] === "open"): ?>
            <a href="/cs2102/editListing/<?=$listing['itemid'] ?>" class="button editBtn" >Edit</a>
            <?php if (!$isOwner): ?>
            <a href="/cs2102/bidding/<?=$listing['itemid'] ?>" class="button bidBtn" >Bid</a>
            <?php endif; ?>
        <?php endif; ?>
    <?php else: ?>
            <a href="/cs2102/bidding/<?=$listing['itemid'] ?>" class="button bidBtn" >Bid</a>
    <?php endif ?>
        </div>
    </div>
    <!-- End button controls -->
    </div>    
</div>
<!-- End display listings -->

<!-- Start bids -->
<br>
<h1>Bids</h1>
<div class="row">
<?php foreach($bids as $bid): ?>
    <div class="bidding-col col-sm-6 col-md-4">
        <div class="bid <?=$bid['status'] ?>">
            <div class="detail">
                <span class="field">Username</span>
                <span class="value"><?=$bid['username'] ?></span>
            </div>
            <div class="detail">
                <span class="field">Bid</span>
                <span class="value"><?=$bid['bidamt'] ?></span>
            </div>
<?php if ($canEdit && $listing['status'] === "open"): ?>
            <div class="controls">
                <div class="left">
                    <a href="/cs2102/selectBid/<?=$bid['listingid'] ?>/<?=$bid['bidderid'] ?>" class="button" >Select bid</a>
                </div>
            </div>
<?php endif ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
<!-- End bids -->