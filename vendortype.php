<?php
include("db_config.php");
$con = mysqli_connect(db_address,db_user,db_pass,db_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<title>Dirty Foods, Inc. | Vendor Types</title>
	<style>
		.content {
			margin-top: 50px;
			margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Dirty Foods, Inc.</a><br><small><span class="glyphicon glyphicon-road"></span> VENDOR TYPES</small></h1>
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
			$result = mysqli_query($con, "SELECT * FROM vendor_type;");
		}
		else if ($_GET['show'] == 'earn') {
			$result = mysqli_query($con, "SELECT vendor_type.* FROM vendor_type, vendor
											WHERE vendor.VENDOR_TYPE_vendortype_id=vendor_type.vendortype_id
											GROUP BY vendor_type.vendortype_id
											ORDER BY COUNT(vendor_type.vendortype_id)
											DESC LIMIT 1;");
		}
		else if ($_GET['show'] == 'top10') {
			$result = mysqli_query($con, "SELECT vendor_type.*
											FROM vendor_type, menu, transaction, vendor
											WHERE vendor.VENDOR_TYPE_vendortype_id=vendor_type.vendortype_id
											AND transaction.VENDOR_vendor_id=vendor.vendor_id
											AND transaction.MENU_menu_id=menu.menu_id
											GROUP BY vendor_type.vendortype_id
											ORDER BY SUM(menu.price*menu.pax)
											DESC LIMIT 10;");
		}
?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Type</th>
					<th>Wage</th>
					<th></th>
					<th>
						<form action="?deleteall" id='delete' method="post"><button type="submit" id='deleteButton' class="btn btn-danger btn-xs">Delete all <span class="glyphicon glyphicon-remove"></span></button></form>
					</th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['type'] . "</td>";
				echo "<td>" . $row['wage'] . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='vendortype_id' value=" . $row['vendortype_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete' >
						<input type='hidden' name='vendortype_id' value=" . $row['vendortype_id']. ">
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

?>
	<div class="container content">
		<form action="?insert" role="form" method="post">
			<div class="form-group">
				<label for="vt_type">Type</label>
				<input type="text" class="form-control" id="vt_type" name="type" placeholder="Enter type" required>
			</div>
			<div class="form-group">
				<label for="vt_wage">Wage</label>
				<input type="number" step="any" class="form-control" id="vt_wage" name="wage" placeholder="Enter wage" required>
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
		$type = $_POST['type'];
		$wage = $_POST['wage'];
		$result = mysqli_query($con, "INSERT INTO vendor_type(type, wage)
										VALUES('" . $type . "', " . $wage . ");");
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
		$result = mysqli_query($con, "SELECT * FROM vendor_type WHERE vendortype_id=" . $_POST['vendortype_id'] .";");
		$current = mysqli_fetch_array($result);
?>
	<div class="container content">
		<form action="?update" role="form" method="post">
			<input type="hidden" name="vendortype_id" value="<?php echo $_POST['vendortype_id'];?>">
			<div class="form-group">
				<label for="vt_type">Type</label>
				<input type="text" class="form-control" id="vt_type" name="type" placeholder="Enter description" value="<?php echo $current['type']; ?>" required>
			</div>
			<div class="form-group">
				<label for="vt_wage">Wage</label>
				<input type="number" step="any" class="form-control" id="vt_wage" name="wage" placeholder="Enter price" value="<?php echo $current['wage']; ?>" required>
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
		$vendortype_id = $_POST['vendortype_id'];
		$type = $_POST['type'];
		$wage = $_POST['wage'];
		$result = mysqli_query($con, "UPDATE vendor_type SET type='" . $type . "', wage=" . $wage . " WHERE vendortype_id=" . $vendortype_id . ";");
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
		$vendortype_id = $_POST['vendortype_id'];
		$result = mysqli_query($con, "DELETE from vendor_type WHERE vendortype_id=" . $vendortype_id . ";");
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
		$result = mysqli_query($con, "TRUNCATE TABLE vendor_type;");
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
