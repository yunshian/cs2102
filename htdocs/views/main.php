<!-- Search bar -->
<div class="row">
    <form action="/cs2102/main" method='GET'>
        <div class="col-sm-6">
            <label>Keyword
            <input type="text" name="keyword" class="form-control" value="<?=$search['keyword'] ?>"></label>
        </div>
        <div class="col-sm-5">
            <label>Category
            <select name="category" class="form-control">
                <option name="category" value=""></option>
<?php foreach($categories as $category): ?>
    <?php if ($category['name'] === $search['category']): ?>
                <option value="<?=$category['name'] ?>" selected><?=$category['name'] ?></option>
    <?php else: ?>
                <option value="<?=$category['name'] ?>"><?=$category['name'] ?></option>
    <?php endif; ?>
<?php endforeach; ?>
            </select></label>
        </div>
        <div class="col-sm-6">
            <label>Available from
            <input type="text" name="pickupDate" class="form-control" value="<?=$search['pickupDate'] ?>"></label>
        </div>
        <div class="col-sm-6">
            <label>Available until
            <input type="text" name="returnDate" class="form-control" value="<?=$search['returnDate'] ?>"></label>
        </div>
        <div class="col-sm-2">
            <button type="submit">Search</button>
        </div>
    </form>
</div>

<!-- Display listings -->
<h1>Listings</h1>
<div class="row">
<?php foreach($listings as $listing): ?>
<div class="listing-col col-sm-6 col-md-4">
    <div class="listing">
    <h1><a href="/cs2102/listing/<?=$listing['itemid'] ?>"><?=$listing['name']?></a></h1>
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
        <div class="left">
            <a class="button deleteBtn" href="/cs2102/deleteListing/<?=$listing['itemid']?>">Delete</a>
        </div>
        <div class="right">
            <a class="button editBtn" href="/cs2102/editListing/<?=$listing['itemid']?>">Edit</a>
            <a class="button bidBtn" href="/cs2102/bidding/<?=$listing['itemid']?>">Bid</a>
        </div>
    </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<ul class="pager">
    <?php if($pagination['hasPrevPage']): ?>
    <li class="previous"><a href=<?=$pagination['prevPage']?>><span aria-hidden="true">&larr;</span> Prev</a></li>
    <?php else: ?>
    <li class="previous disabled"><a><span aria-hidden="true">&larr;</span> Prev</a></li>
    <?php endif ?>
    <?php if($pagination['hasNextPage']): ?>
    <li class="next"><a href=<?=$pagination['nextPage']?>>Next <span aria-hidden="true">&rarr;</span></a></li>
    <?php else: ?>
    <li class="next disabled"><a>Next <span aria-hidden="true">&rarr;</span></a></li>
    <?php endif ?>
</ul>
<!-- End listings -->