<?php
include("db_config.php");
$con = mysqli_connect(db_address,db_user,db_pass,db_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<title>Dirty Foods, Inc. | Locations</title>
	<style>
		.content {
			margin-top: 50px;
			margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Dirty Foods, Inc.</a><br><small><span class="glyphicon glyphicon-globe"></span> LOCATIONS</small></h1>
		<hr>
	</div>
	<div class="container text-center">
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?show=az">All Alphabetically</a></li>
				<li><a href="?show=num">All Arranged By Number of Franchises</a></li>
				<li><a href="?show=top3">Top 3 Most Profitable</a></li>
			</ul>
		</div>
	</div>





<?php
/*
	SHOW
*/
	if (isset($_GET['show'])) {
		if ($_GET['show'] == 'az') {
			$result = mysqli_query($con, "SELECT * FROM location ORDER BY city");
		}
		else if ($_GET['show'] == 'num') {
			$result = mysqli_query($con, "SELECT location.*, LocationCount FROM (SELECT LOCATION_location_id, count(LOCATION_location_id) AS LocationCount FROM franchise GROUP BY LOCATION_location_id) AS q, location WHERE LOCATION_location_id=location_id ORDER BY LocationCount DESC;");
		}
		else if ($_GET['show'] == 'top3') {
			$result = mysqli_query($con, "SELECT location.* FROM location, franchise, menu, transaction WHERE franchise.LOCATION_location_id=location.location_id
											AND transaction.VENDOR_vendor_id= franchise.VENDOR_vendor_id AND transaction.MENU_menu_id=menu.menu_id GROUP BY location.location_id
											ORDER BY SUM(menu.price*menu.pax) DESC LIMIT 3;");
		}
?>
<?php
	if ($_GET['show'] == 'az') {
		$morethanten = mysqli_query($con, "SELECT * FROM (SELECT location.*,  count(DISTINCT FRANCHISE_TYPE_franchisetype_id) AS FTCount FROM franchise, location WHERE LOCATION_location_id=location_id GROUP BY LOCATION_location_id ORDER BY FTCount DESC) AS q WHERE q.FTCount>10;");
		if (mysqli_num_rows($morethanten) > 0) {
?>
		<div class="container content">
			<div class="alert alert-info">
				<strong>Looks! </strong> Some locations have more than ten franchise types:

				<?php
					while ($row = mysqli_fetch_array($morethanten)) {
						echo "<br>" . $row['district'] . " " . $row['city'] . ", " . $row['province'];
					}
				?>
				<button class="close" data-dismiss="alert"><span>&times;</span></button>
			</div>
		</div>
<?php
		}
	}
?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>District</th>
					<th>City</th>
					<th>Province</th>
					<th>Population</th>
					<?php
					if ($_GET['show'] == 'num') {
					?>
					<th>No. of Franchises</th>
					<?php
					}
					?>
					<th></th>
					<th>
						<?php
						if ($_GET['show'] == 'az') {
						?>
						<form action="?deleteall" id='delete' method="post"><button type="submit" id='deleteButton' class="btn btn-danger btn-xs">Delete all <span class="glyphicon glyphicon-remove"></span></button></form>
						<?php
						}
						?>
					</th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['district'] . "</td>";
				echo "<td>" . $row['city'] . "</td>";
				echo "<td>" . $row['province'] . "</td>";
				echo "<td>" . $row['population'] . "</td>";
				if ($_GET['show'] == 'num') {
					echo "<td>" . $row['LocationCount'] . "</td>";
				}
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='location_id' value=" . $row['location_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete' >
						<input type='hidden' name='location_id' value=" . $row['location_id']. ">
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
				<label for="loc_district">District</label>
				<input type="text" class="form-control" id="loc_district" name="district" placeholder="Enter district" required>
			</div>
			<div class="form-group">
				<label for="loc_city">City</label>
				<input type="text" class="form-control" id="loc_city" name="city" placeholder="Enter city" required>
			</div>
			<div class="form-group">
				<label for="loc_province">Province</label>
				<input type="text" class="form-control" id="loc_province" name="province" placeholder="Enter province" required>
			</div>
			<div class="form-group">
				<label for="loc_population">Population</label>
				<input type="number" class="form-control" id="loc_population" name="population" placeholder="Enter population" required>
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
		$district = $_POST['district'];
		$city = $_POST['city'];
		$province = $_POST['province'];
		$population = $_POST['population'];
		$result = mysqli_query($con, "INSERT INTO location(district, city, province, population)
										VALUES('" . $district . "', '" . $city . "', '" . $province . "', " . $population . ");");
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
		$result = mysqli_query($con, "SELECT * FROM location WHERE location_id=" . $_POST['location_id'] .";");
		$current = mysqli_fetch_array($result);
?>
	<div class="container content">
		<form action="?update" role="form" method="post">
			<input type="hidden" name="location_id" value="<?php echo $_POST['location_id']; ?>">
			<div class="form-group">
				<label for="loc_district">District</label>
				<input type="text" class="form-control" id="loc_district" name="district" value="<?php echo $current['district']; ?>" required>
			</div>
			<div class="form-group">
				<label for="loc_city">City</label>
				<input type="text" class="form-control" id="loc_city" name="city" value="<?php echo $current['city']; ?>" required>
			</div>
			<div class="form-group">
				<label for="loc_province">Province</label>
				<input type="text" class="form-control" id="loc_province" name="province" value="<?php echo $current['province']; ?>" required>
			</div>
			<div class="form-group">
				<label for="loc_population">Population</label>
				<input type="number" class="form-control" id="loc_population" name="population" value="<?php echo $current['population']; ?>" required>
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
		$location_id = $_POST['location_id'];
		$district = $_POST['district'];
		$city = $_POST['city'];
		$province = $_POST['province'];
		$population = $_POST['population'];
		$result = mysqli_query($con, "UPDATE location SET district='" . $district . "', city='" . $city . "', province='" . $province . "', population=" . $population . " WHERE location_id=" . $location_id . ";");
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
		$location_id = $_POST['location_id'];
		$result = mysqli_query($con, "DELETE from location WHERE location_id=" . $location_id . ";");
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
		$result = mysqli_query($con, "TRUNCATE TABLE location;");
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
