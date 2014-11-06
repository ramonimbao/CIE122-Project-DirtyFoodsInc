<?php
include("db_config.php");
$con = mysqli_connect(db_address,db_user,db_pass,db_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<title>Dirty Foods, Inc. | Menus</title>
	<style>
		.content {
			margin-top: 50px;
			margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Dirty Foods, Inc.</a><br><small><span class="glyphicon glyphicon-cutlery"></span> MENUS</small></h1>
		<hr>
	</div>
	<div class="container text-center">
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?show=all">All</a></li>
				<li><a href="?show=earn">Highest Earning</a></li>
				<li><a href="?show=top10">Top 10 Highest Selling</a></li>
			</ul>
		</div>
	</div>





<?php
/*
	SHOW
*/
	if (isset($_GET['show'])) {
		if ($_GET['show'] == 'all') {
			$result = mysqli_query($con, "SELECT menu.*, type FROM menu, franchise_type WHERE FRANCHISE_TYPE_franchisetype_id=franchisetype_id;");
		}
		else if ($_GET['show'] == 'earn') {
			$result = mysqli_query($con, "SELECT menu.*, type FROM menu, transaction, franchise_type
											WHERE transaction.MENU_menu_id=menu.menu_id
											AND menu.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id
											GROUP BY menu.menu_id
											ORDER BY sum(menu.price*menu.pax)
											DESC LIMIT 1;");
		}
		else if ($_GET['show'] == 'top10') {
			$result = mysqli_query($con, "SELECT menu.*, type FROM menu, transaction, franchise_type
											WHERE transaction.MENU_menu_id=menu.menu_id
											AND menu.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id
											GROUP BY menu.menu_id
											ORDER BY COUNT(transaction.MENU_menu_id)
											DESC LIMIT 10;");
		}
?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Description</th>
					<th>Price</th>
					<th>Pax</th>
					<th>Franchise</th>
					<th></th>
					<th>
						<form action="?deleteall" id='delete' method="post"><button type="submit" id='deleteButton' class="btn btn-danger btn-xs">Delete all <span class="glyphicon glyphicon-remove"></span></button></form>
					</th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['description'] . "</td>";
				echo "<td>" . $row['price'] . "</td>";
				echo "<td>" . $row['pax'] . "</td>";
				echo "<td>" . $row['type'] . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='menu_id' value=" . $row['menu_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete' >
						<input type='hidden' name='menu_id' value=" . $row['menu_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Delete this entry <span class='glyphicon glyphicon-remove'></span></button>
					</form>
				</td>
				";

				echo "</tr>";
			}
		?>
		<tr><td colspan=9 class="text-center"><a href="?add" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plus"></span> Add new entry</a></td></tr>
		</table>
	</div>
<?php
	}	
?>





<?php
/*
	ADD
*/
	if (isset($_GET['add'])) {
	$franchisetypes = mysqli_query($con, "SELECT * FROM franchise_type");
?>
	<div class="container content">
		<form action="?insert" role="form" method="post">
			<div class="form-group">
				<label for="menu_desc">Description</label>
				<input type="text" class="form-control" id="menu_desc" name="description" placeholder="Enter description" required>
			</div>
			<div class="form-group">
				<label for="menu_price">Price</label>
				<input type="number" step="any" class="form-control" id="menu_price" name="price" placeholder="Enter price" required>
			</div>
			<div class="form-group">
				<label for="menu_pax">Pax</label>
				<input type="number" class="form-control" id="menu_pax" name="pax" placeholder="Enter pax" required>
			</div>
			<div class="form-group">
				<label for="menu_ft">Franchise Type</label>
				<select class="form-control" id="menu_ft" name="type">
<?php
	while ($row = mysqli_fetch_array($franchisetypes)) {
		echo "<option value='" . $row['franchisetype_id'] . "'>" . $row['type'] . "</option>";
	}
?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
	</div>
<?php
	}
?>





<?php
/*
	INSERT
*/
	if (isset($_GET['insert'])) {
		$description = $_POST['description'];
		$price = $_POST['price'];
		$pax = $_POST['pax'];
		$type = $_POST['type'];
		$result = mysqli_query($con, "INSERT INTO menu(description, price, pax, FRANCHISE_TYPE_franchisetype_id)
										VALUES('" . $description . "', " . $price . ", " . $pax . ", " . $type . ");");
?>
	<div class="container content">
<?php
		if (!$result) {
?>
		<div class="alert alert-danger"><strong>Oops!</strong> The data wasn't added successfully.</div>
<?php
		}
		else {
?>
		<div class="alert alert-success"><strong>Yay!</strong> The data was added successfully.</div>
<?php
		}
	}
?>



<?php
/*
	EDIT
*/
	if (isset($_GET['edit'])) {
		$result = mysqli_query($con, "SELECT * FROM menu WHERE menu_id=" . $_POST['menu_id'] .";");
		$current = mysqli_fetch_array($result);
		$franchisetypes = mysqli_query($con, "SELECT * FROM franchise_type;");
?>
	<div class="container content">
		<form action="?update" role="form" method="post">
			<input type="hidden" name="menu_id" value="<?php echo $_POST['menu_id'];?>">
			<div class="form-group">
				<label for="menu_desc">Description</label>
				<input type="text" class="form-control" id="menu_desc" name="description" placeholder="Enter description" value="<?php echo $current['description']; ?>" required>
			</div>
			<div class="form-group">
				<label for="menu_price">Price</label>
				<input type="number" step="any" class="form-control" id="menu_price" name="price" placeholder="Enter price" value="<?php echo $current['price']; ?>" required>
			</div>
			<div class="form-group">
				<label for="menu_pax">Pax</label>
				<input type="number" class="form-control" id="menu_pax" name="pax" placeholder="Enter pax" value="<?php echo $current['pax']; ?>" required>
			</div>
			<div class="form-group">
				<label for="menu_ft">Franchise Type</label>
				<select class="form-control" id="menu_ft" name="type">
<?php
	while ($row = mysqli_fetch_array($franchisetypes)) {
		echo "<option value='" . $row['franchisetype_id'] . "'" . ($current['FRANCHISE_TYPE_franchisetype_id'] == $row['franchisetype_id'] ? " selected" : "") . ">" . $row['type'] . "</option>";
	}
?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
	</div>
<?php
	}
?>






<?php
/*
	UPDATE
*/
	if (isset($_GET['update'])) {
		$menu_id = $_POST['menu_id'];
		$description = $_POST['description'];
		$price = $_POST['price'];
		$pax = $_POST['pax'];
		$type = $_POST['type'];
		$result = mysqli_query($con, "UPDATE menu SET description='" . $description . "', price=" . $price . ", pax=" . $pax . ", FRANCHISE_TYPE_franchisetype_id=" . $type . " WHERE menu_id=" . $menu_id . ";");
?>
	<div class="container content">
<?php
		if (!$result) {
?>
		<div class="alert alert-danger"><strong>Oops!</strong> The data wasn't updated successfully.</div>
<?php
		}
		else {
?>
		<div class="alert alert-success"><strong>Yay!</strong> The data was updated successfully.</div>
<?php
		}
	}
?>





<?php
/*
	DELETE
*/
	if (isset($_GET['delete'])) {
		$menu_id = $_POST['menu_id'];
		$result = mysqli_query($con, "DELETE from menu WHERE menu_id=" . $menu_id . ";");
?>
	<div class="container content">
<?php
		if (!$result) {
?>
		<div class="alert alert-danger"><strong>Oops!</strong> The data wasn't deleted successfully.</div>
<?php
		}
		else {
?>
		<div class="alert alert-success"><strong>Yay!</strong> The data was deleted successfully.</div>
<?php
		}
	}
?>





<?php
/*
	DELETE ALL
*/
	if (isset($_GET['deleteall'])) {
		$result = mysqli_query($con, "TRUNCATE TABLE menu;");
?>
	<div class="container content">
<?php
		if (!$result) {
?>
		<div class="alert alert-danger"><strong>Oops!</strong> All the data wasn't deleted successfully.</div>
<?php
		}
		else {
?>
		<div class="alert alert-success"><strong>Yay!</strong> All the data was deleted successfully.</div>
<?php
		}
	}
?>




</body>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
	$(".dropdown-toggle").dropdown();
$(document).ready(function() {
	$("form#delete").submit(function() {
		var c = confirm("Are you sure you want to continue?");
		return c;
	});
});
	
</script>
</html>
