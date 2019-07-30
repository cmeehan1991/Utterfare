<div class="container-fluid">
	<div class="row">
		<div class="col">
			<h2>Menu Items</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-md-2 ml-auto">
			<a class="btn btn-outline-primary" href="#!/items/edit/">Add New Item</a>
		</div>
	</div>
	<div class="row">
		<table class="items-table">
			<thead>
				<tr>
					<th scope="col">Primary Image</th>
					<th scope="col">Item Name</th>
					<th scope="col">Item Description</th>
					<th scope="col"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><img class="items-table--image" src="" alt=""></td>
					<td><span class="items-table--name"></span></td>
					<td><span class="items-table--description"></span></td>
					<td align="center"><a class="btn btn-outline-info" href="" data-item-id="">Edit</a> <a class="btn btn-outline-danger delete-item-button" href="" data-item-id="" onclick="confirmDelete();">Delete</a></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="row">
		<div class="col-sm-push-12 col-md-4">
			Showing <span class="results-count--range">1 to 10</span> out of <span class="resuls-count--total">25</span> item(s).
		</div>
		<div class="col-sm-pull-12 col-md-7 mr-auto">			
			<nav class="d-flex justify-content-end" aria-label="Items table navigation">
				<ul class="pagination">
					<li class="page-item"><a class="page-link">Previous</a></li>
					<li class="page-item active"><a class="page-link">1</a></li>
					<li class="page-item"><a class="page-link">2</a></li>
					<li class="page-item"><a class="page-link">3</a></li>
					<li class="page-item"><a class="page-link">Next</a></li>
				</ul>
			</nav>
		</div>
	</div>
</div>