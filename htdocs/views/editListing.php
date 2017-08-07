<div class="form">
<form method="POST">
    <label>
        Listing name
        <input type="text" name="name" value="<?=$input['name'] ?>" required>
    </label>
    <label>
        Description
        <input type="text" name="description" value="<?=$input['description'] ?>" required>
    </label>
    <label>
        Category
        <select name="category">
            <option value=""></option>
<?php foreach($categories as $category): ?>
    <?php if ($input['category'] === $category['name']): ?>
            <option value="<?=$category['name'] ?>" selected><?=$category['name'] ?></option>
    <?php else: ?>
            <option value="<?=$category['name'] ?>"><?=$category['name'] ?></option>
    <?php endif; ?>
<?php endforeach; ?>
        </select>
    </label>
    <label>
        Pickup location
        <input type="text" name="pickupLocation" value="<?=$input['pickuplocation'] ?>" required>
    </label>
    <label>
        Return location
        <input type="text" name="returnLocation" value="<?=$input['returnlocation'] ?>" required>
    </label>
    <label>
        Pickup date
        <input type="text" name="pickupDate" value="<?=$input['pickupdate'] ?>" required>
    </label>
    <label>
        Return date
        <input type="text" name="returnDate" value="<?=$input['returndate'] ?>" required>
    </label>
    <label>
        Minimum price
        <input type="text" name="minPrice" value="<?=$input['minprice'] ?>" required>
    </label>

    <input type="submit" name="submit">
    
    <span class="errorMessage">
<?php  if (isset($isSuccess) && !$isSuccess): ?>
    <?=$errMsg ?>
<?php endif; ?>
    </span>
</form>
</div>

<a href="/cs2102/main" class="button">Return to main page</a>
