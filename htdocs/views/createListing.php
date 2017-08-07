<div class="form">
<form action="/cs2102/createListing" method="POST" enctype="multipart/form-data">
<?php for($i = 0; $i < 5; $i++): ?>    
    <label class="uploadImg">
        <img class="uploadImgThumbnail">
        <input name="img<?=$i ?>" class="uploadImgInput" type="file" accept="image/*" onchange="previewFile(this)">
        <div class="uploadImgBtn" onclick="removeFile(this,event)"><span>&times;</span></div>
    </label>
<?php endfor; ?>
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
            <option name="category" value=""></option>
<?php foreach($categories as $category): ?>
            <option value="<?=$category['name'] ?>"><?=$category['name'] ?></option>
<?php endforeach; ?>
        </select>
    </label>
    <label>
        Pickup location
        <input type="text" name="pickupLocation" value="<?=$input['pickupLocation'] ?>" required>
    </label>
    <label>
        Return location
        <input type="text" name="returnLocation" value="<?=$input['returnLocation'] ?>" required>
    </label>
    <label>
        Pickup date
        <input type="text" name="pickupDate" value="<?=$input['pickupDate'] ?>" required>
    </label>
    <label>
        Return date
        <input type="text" name="returnDate" value="<?=$input['returnDate'] ?>" required>
    </label>
    <label>
        Minimum price
        <input type="text" name="minPrice" value="<?=$input['minPrice'] ?>" required>
    </label>

    <input type="submit" name="submit">
    
    <span class="errorMessage">
<?php  if (isset($isSuccess) && !$isSuccess): ?>
    <?=$errMsg ?>
<?php endif; ?>
    </span>
    
</form>
</div>

<a href="http://localhost/cs2102/main" class="button">Return to main page</a>

<script>
function previewFile(el) {
    var preview = el.previousElementSibling;
    var file = el.files[0];
    var reader = new FileReader();

    reader.onloadend = function() {
        preview.src = reader.result;
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = "";
    }
}
    
function removeFile(el, ev) {
    var input = el.previousElementSibling;
    var preview = input.previousElementSibling;
    
    input.value = "";
    preview.src = "";
    ev.preventDefault();
}
</script>