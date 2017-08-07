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

<p>This listing has closed.</p>
<a class="button" href="/cs2102/main">Return to main page</a>
</div>