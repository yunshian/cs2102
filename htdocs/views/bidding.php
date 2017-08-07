<div class="top5Bids">
<h2>Top 5 bids of this item</h2>
<?php if (sizeof($top5Bids) > 0): ?>
<ol>
    <?php foreach ($top5Bids as $bid): ?>
    <li><?=$bid['bidamt'] ?></li>
    <?php endforeach; ?>
</ol>
<?php else: ?>
    <span class="errorMessage">No bids have been placed.</span>
<?php endif; ?>
</div>
<br>

<div>

<h2>Place Your Bids</h2>
<?php if ($isAuthenticated): ?>
<?php if ($hasBid): ?>
<form action="/cs2102/bidding/<?=$listingId ?>" method="POST">
    Current Bid Amount: <?=$currentBidAmt ?>
    <input name="deleteBtn" type="submit" value="Delete Current Bid" />
</form>
<?php endif; ?>
<br><br>
<form action="/cs2102/bidding/<?=$listingId ?>" method="POST">
    Bid Amount: <input type="text" name="bidAmt" required>
<?php if ($hasBid): ?>
    <input name="updateBtn" type="submit" value="Update Bid" />
<?php else: ?>
    <input name="submitBtn" type="submit" value="Bid" />
<?php endif; ?>
</form>

<?php if(!$isSuccess): ?>
    <span class="errorMessage">
		<?=$message ?>
	</span>
<?php else: ?>
    <span class="successfulMessage">
		<?=$message ?>
	</span>
<?php endif; ?>
<?php else: ?>
    <span class="errorMessage">
		Please log in to bid.
	</span>
<?php endif; ?>
<div class="controls">
    <a class="button" href="/cs2102/listing/<?=$listingId ?>">Return to listing</a>
    <a class="button" href="/cs2102/main">Return to main page</a>
</div>
</div>