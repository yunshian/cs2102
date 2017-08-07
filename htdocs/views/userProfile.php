<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title"><?=$user['username'] ?></h3>
                </div>
            <div class="panel-body">
            <div class="row">
                <div class=" col-md-9 col-lg-9 "> 
                    <table class="table table-user-information">
                        <tbody>
                            <tr>
                                <td>Bid Points:</td>
                                <td><?=$user['bidpts'] ?></td>
                            </tr>
                            <tr>
                                <td>User Type</td>
                                <td><?=$user['usertype'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
            <div class="panel-footer">
                <a data-original-title="Broadcast Message" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-envelope"></i></a>
                <span class="pull-right">
                    <a data-original-title="Edit this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                    <a data-original-title="Remove this user" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
                </span>
            </div>
            </div>
        </div>
    </div>

<h1>Listings</h1>
<div class="row">
<?php foreach($listings as $listing): ?>
<div class="listing-col col-sm-6 col-md-4">
    <div class="listing">
    <h1><a href="/cs2102/listing/<?=$listing['itemid'] ?>"><?=$listing['name']?></a></h1>
    <p><?=$listing['description']?></p>
    <div>
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
    <?php if ($canEdit): ?>
        <div class="left">
            <a class="button deleteBtn" href="/cs2102/deleteListing/<?=$listing['itemid']?>">Delete</a>
        </div>
        <div class="right">
            <a class="button editBtn" href="/cs2102/editListing/<?=$listing['itemid']?>">Edit</a>
        </div>
    <?php else: ?>
        <div class="right">
            <a class="button bidBtn" href="/cs2102/bidding/<?=$listing['itemid']?>">Bid</a>
        </div>
    <?php endif; ?>
    </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- Start bids -->
<h1>Bids</h1>
<div class="row">
<?php foreach($bids as $bid): ?>
    <div class="bidding-col col-sm-6 col-md-4">
        <div class="bid <?=$bid['status'] ?>">
            <div class="detail">
                <span class="field">Listing</span>
                <span class="value"><a href="/cs2102/listing/<?=$bid['itemid'] ?>"><?=$bid['name'] ?></a></span>
            </div>
            <div class="detail">
                <span class="field">Bid Amount</span>
                <span class="value"><?=$bid['bidamt'] ?></span>
            </div>
            <div class="detail">
                <span class="field">Bid Status</span>
                <span class="value"><?=$bid['status'] ?></span>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

</div>