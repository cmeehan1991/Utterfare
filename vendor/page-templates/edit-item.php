<div class="container-fluid">
	<div class="row">
		<div class="col">
			<h2>Add/Edit Item</h2>
		</div>
	</div>
	<form name="edit-item-form" id="addEditItemForm" onsubmit="return addEditItem(this);" data-item-id="" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-4 order-last">
				<img src="" alt="..." class="edit-item--item-image">
				<small class="form-text text-muted">This is a preview.</small>
				<div class="form-group">
					<label for="item-image">Featured Image</label>
					<input type="file" class="form-control-file" name="item-image" onchange="setPreview(this);">
					<medium class="form-text text-muted">Your image should either be a 16:9 or 1:1 ratio for the best resolution.</medium>
				</div>
			</div>
			<div class="col-md-6 order-first">
				<div class="form-group">
					<label for="item-name">Item Name</label>
					<input type="text" class="form-control" name="item-name">
				</div>				
				<div class="form-group">
					<label for="item-description">Item Description</label>
					<textarea type="text" class="form-control" name="item-description" rows="4"></textarea>
				</div>
				<div class="form-group">
					<label for="keywords">Keywords</label>
					<textarea type="text" class="form-control" rows="2" readonly></textarea>
				    <small class="form-text text-muted">This feature is not yet available.</small>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<button type="submit" class="btn btn-primary">Save Changes</button>
				<button class="btn btn-danger" onclick="deleteItem()">Delete Item</button>
			</div>
		</div>
	</form>
</div>