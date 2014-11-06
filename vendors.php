<?php
include("db_config.php");
$con = mysqli_connect(db_address,db_user,db_pass,db_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<title>Dirty Foods, Inc. | Vendors</title>
	<style>
		.content {
			margin-top: 50px;
			margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Dirty Foods, Inc.</a><br><small><span class="glyphicon glyphicon-shopping-cart"></span> VENDORS</small></h1>
		<hr>
	</div>
	<div class="container text-center">
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?show=all">All</a></li>
				<li><a href="?show=earn">Highest Earning</a></li>
				<li><a href="?show=top10">Top 10 Highest Earning</a></li>
			</ul>
		</div>
	</div>





<?php
/*
	SHOW
*/
	if (isset($_GET['show'])) {
		if ($_GET['show'] == 'all') {
			$result = mysqli_query($con, "SELECT vendor.*, type FROM vendor, vendor_type WHERE VENDOR_TYPE_vendortype_id=vendortype_id;");
		}
		else if ($_GET['show'] == 'earn') {
			$result = mysqli_query($con, "SELECT vendor.*, type FROM vendor, vendor_type, transaction, menu
												WHERE vendor.VENDOR_TYPE_vendortype_id=vendor_type.vendortype_id
												AND transaction.VENDOR_vendor_id=vendor.vendor_id
												AND transaction.MENU_menu_id=menu.menu_id
												GROUP BY vendor.vendor_id ORDER BY SUM(menu.price*menu.pax) DESC LIMIT 1;");
		}
		else if ($_GET['show'] == 'top10') {
			$result = mysqli_query($con, "SELECT vendor.*, type FROM vendor, vendor_type, transaction, menu
												WHERE vendor.VENDOR_TYPE_vendortype_id=vendor_type.vendortype_id
												AND transaction.VENDOR_vendor_id=vendor.vendor_id
												AND transaction.MENU_menu_id=menu.menu_id
												GROUP BY vendor.vendor_id ORDER BY SUM(menu.price*menu.pax) DESC LIMIT 10;");
		}
?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Age</th>
					<th>Birthdate</th>
					<th>Vendor Type</th>
					<th></th>
					<th>
						<form action="?deleteall" id='delete' method="post"><button type="submit" id='deleteButton' class="btn btn-danger btn-xs">Delete all <span class="glyphicon glyphicon-remove"></span></button></form>
					</th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['name'] . "</td>";
				echo "<td>" . $row['age'] . "</td>";
				echo "<td>" . $row['birthdate'] . "</td>";
				echo "<td>" . $row['type'] . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='vendor_id' value=" . $row['vendor_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete' >
						<input type='hidden' name='vendor_id' value=" . $row['vendor_id']. ">
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
	$vendortypes = mysqli_query($con, "SELECT * FROM vendor_type");
?>
	<div class="container content">
		<form action="?insert" role="form" method="post">
			<div class="form-group">
				<label for="v_name">Name</label>
				<input type="text" class="form-control" id="v_name" name="name" placeholder="Enter name" required>
			</div>
			<div class="form-group">
				<label for="v_age">Age</label>
				<input type="number" step="any" class="form-control" id="v_age" name="age" placeholder="Enter age" required>
			</div>
			<div class="form-group">
				<label for="v_birthdate">Birthdate</label>
				<input type="date" class="form-control" id="v_birthdate" name="birthday" required>
			</div>
			<div class="form-group">
				<label for="v_type">Vendor Type</label>
				<select class="form-control" id="v_type" name="type">
<?php
	while ($row = mysqli_fetch_array($vendortypes)) {
		echo "<option value='" . $row['vendortype_id'] . "'>" . $row['type'] . "</option>";
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
		$name = $_POST['name'];
		$age = $_POST['age'];
		$birthdate = $_POST['birthday'];
		$type = $_POST['type'];
		$result = mysqli_query($con, "INSERT INTO vendor(name, age, birthdate, VENDOR_TYPE_vendortype_id)
										VALUES('" . $name . "', " . $age . ", '" . $birthdate . "', " . $type . ");");
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
		$result = mysqli_query($con, "SELECT vendor.* FROM vendor WHERE vendor_id=" . $_POST['vendor_id'] .";");
		$current = mysqli_fetch_array($result);
		$vendortypes = mysqli_query($con, "SELECT * FROM vendor_type;");
?>
	<div class="container content">
		<form action="?update" role="form" method="post">
			<input type="hidden" name="vendor_id" value="<?php echo $_POST['vendor_id'];?>">
			<div class="form-group">
				<label for="v_name">Name</label>
				<input type="text" class="form-control" id="v_name" name="name" placeholder="Enter name" value="<?php echo $current['name']; ?>" required>
			</div>
			<div class="form-group">
				<label for="v_age">Age</label>
				<input type="number" step="any" class="form-control" id="v_age" name="age" placeholder="Enter age" value="<?php echo $current['age']; ?>" required>
			</div>
			<div class="form-group">
				<label for="v_birthdate">Birthdate</label>
				<input type="date" class="form-control" id="v_birthdate" name="birthday"  value="<?php echo $current['birthdate']; ?>" required>
			</div>
			<div class="form-group">
				<label for="v_type">Vendor Type</label>
				<select class="form-control" id="v_type" name="type">
<?php
	while ($row = mysqli_fetch_array($vendortypes)) {
		echo "<option value='" . $row['vendortype_id'] . "'" . ($current['VENDOR_TYPE_vendortype_id'] == $row['vendortype_id'] ? " selected" : "") . ">" . $row['type'] . "</option>";
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
		$vendor_id = $_POST['vendor_id'];
		$name = $_POST['name'];
		$age = $_POST['age'];
		$birthdate = $_POST['birthday'];
		$type = $_POST['type'];
		$result = mysqli_query($con, "UPDATE vendor SET name='" . $name . "', age=" . $age . ", birthdate='" . $birthdate . "', VENDOR_TYPE_vendortype_id=" . $type . " WHERE vendor_id=" . $vendor_id . ";");
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
		$vendor_id = $_POST['vendor_id'];
		$result = mysqli_query($con, "DELETE from vendor WHERE vendor_id=" . $vendor_id . ";");
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
		$result = mysqli_query($con, "TRUNCATE TABLE vendor;");
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
