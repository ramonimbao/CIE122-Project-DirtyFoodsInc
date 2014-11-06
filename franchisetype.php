<?php
include("db_config.php");
$con = mysqli_connect(db_address,db_user,db_pass,db_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<title>Dirty Foods, Inc. | Franchise Types</title>
	<style>
		.content {
			margin-top: 50px;
			margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Dirty Foods, Inc.</a><br><small><span class="glyphicon glyphicon-tasks"></span> FRANCHISE TYPES</small></h1>
		<hr>
	</div>
	<div class="container text-center">
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?show=all">All</a></li>
				<li><a href="?show=num">Most Number of Franchise Types</a></li>
				<li><a href="?show=top3">Top 3 Highest Earning</a></li>
			</ul>
		</div>
	</div>





<?php
/*
	SHOW
*/
	if (isset($_GET['show'])) {
		if ($_GET['show'] == 'all') {
			$result = mysqli_query($con, "SELECT * FROM franchise_type;");
		}
		else if ($_GET['show'] == 'num') {
			$result = mysqli_query($con, "SELECT franchise_type.* FROM franchise_type, franchise
											WHERE franchise.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id
											GROUP BY franchise_type.franchisetype_id
											ORDER BY COUNT(franchise_type.franchisetype_id)
											DESC LIMIT 1;");
		}
		else if ($_GET['show'] == 'top3') {
			$result = mysqli_query($con, "SELECT franchise_type.* FROM franchise_type, menu, transaction, franchise
											WHERE franchise.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id
											AND transaction.VENDOR_vendor_id=franchise.VENDOR_vendor_id
											AND transaction.MENU_menu_id=menu.menu_id
											GROUP BY franchise_type.franchisetype_id
											ORDER BY SUM(menu.price*menu.pax)
											DESC LIMIT 3;");
		}
?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Logo</th>
					<th>Type</th>
					<th></th>
					<th>
						<form action="?deleteall" id='delete' method="post"><button type="submit" id='deleteButton' class="btn btn-danger btn-xs">Delete all <span class="glyphicon glyphicon-remove"></span></button></form>
					</th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>";
				if (is_null($row['logo']) && $row['logo']=="")
					echo "No Logo";
				else
					echo "<img src='" . $row['logo'] . "' width='100px' height='100px' class='img-responsive img-thumbnail'";
				echo "</td>";
				echo "<td>" . $row['type'] . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='franchisetype_id' value=" . $row['franchisetype_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete' >
						<input type='hidden' name='franchisetype_id' value=" . $row['franchisetype_id']. ">
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
				<label for="ft_logo">Logo</label>
				<input type="text" class="form-control" id="ft_logo" name="logo" placeholder="Enter logo URL">
			</div>
			<div class="form-group">
				<label for="ft_type">Type</label>
				<input type="text" class="form-control" id="ft_type" name="type" placeholder="Enter type" required>
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
		$logo = $_POST['logo'];
		$type = $_POST['type'];
		if ($logo == "") {
			$result = mysqli_query($con, "INSERT INTO franchise_type(logo, type)
											VALUES(NULL, '" . $type . "');");
		}
		else {
			$result = mysqli_query($con, "INSERT INTO franchise_type(logo, type)
											VALUES('" . $logo . "', '" . $type . "');");
		}
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
		$result = mysqli_query($con, "SELECT * FROM franchise_type WHERE franchisetype_id=" . $_POST['franchisetype_id'] .";");
		$current = mysqli_fetch_array($result);
?>
	<div class="container content">
		<form action="?update" role="form" method="post">
			<input type="hidden" name="franchisetype_id" value="<?php echo $_POST['franchisetype_id'];?>">
			<div class="form-group">
				<label for="vt_logo">Logo</label>
				<input type="text" class="form-control" id="vt_logo" name="logo" placeholder="Enter logo URL" value="<?php echo $current['logo']; ?>">
			</div>
			<div class="form-group">
				<label for="vt_type">Type</label>
				<input type="text" class="form-control" id="vt_type" name="type" placeholder="Enter type" value="<?php echo $current['type']; ?>" required>
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
		$franchisetype_id = $_POST['franchisetype_id'];
		$logo = $_POST['logo'];
		$type = $_POST['type'];
		if ($logo == "") {
			$result = mysqli_query($con, "UPDATE franchise_type SET logo=NULL , type='" . $type . "' WHERE franchisetype_id=" . $franchisetype_id . ";");
		}
		else {
			$result = mysqli_query($con, "UPDATE franchise_type SET logo='" . $logo . "', type='" . $type . "' WHERE franchisetype_id=" . $franchisetype_id . ";");
		}
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
		$franchisetype_id = $_POST['franchisetype_id'];
		$result = mysqli_query($con, "DELETE from franchise_type WHERE franchisetype_id=" . $franchisetype_id . ";");
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
		$result = mysqli_query($con, "TRUNCATE TABLE franchise_type;");
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
