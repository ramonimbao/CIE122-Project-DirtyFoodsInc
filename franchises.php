<?php
include("db_config.php");
$con = mysqli_connect(db_address,db_user,db_pass,db_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<title>Dirty Foods, Inc. | Franchises</title>
	<style>
		.content {
			margin-top: 50px;
			margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Dirty Foods, Inc.</a><br><small><span class="glyphicon glyphicon-briefcase"></span> FRANCHISES</small></h1>
		<hr>
	</div>
	<div class="container text-center">
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?show=all">All</a></li>
				<li><a href="?show=active">Active</a></li>
				<li><a href="?show=inactive">Inactive</a></li>
			</ul>
		</div>
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show Highest Earning <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?earn=day">This Day</a></li>
				<li><a href="?earn=month">This Month</a></li>
				<li><a href="?earn=year">This Year</a></li>
			</ul>
		</div>
	</div>





<?php
/*
	SHOW
*/
	if (isset($_GET['show'])) {
		if ($_GET['show'] == 'all') {
			$result = mysqli_query($con, "SELECT franchise_id, type, date(startdate) AS Start, date(enddate) AS End, annual_fee, operator.name AS OperatorName, vendor.name AS VendorName, city, province, status FROM franchise, franchise_type, operator, vendor, location WHERE franchise.OPERATOR_operator_id=operator.operator_id AND franchise.VENDOR_vendor_id=vendor.vendor_id AND franchise.LOCATION_location_id=location.location_id AND franchise.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id;");
		}
		else if ($_GET['show'] == 'active') {
			$result = mysqli_query($con, "SELECT franchise_id, type, date(startdate) AS Start, date(enddate) AS End, annual_fee, operator.name AS OperatorName, vendor.name AS VendorName, city, province, status FROM franchise, franchise_type, operator, vendor, location WHERE franchise.OPERATOR_operator_id=operator.operator_id AND franchise.VENDOR_vendor_id=vendor.vendor_id AND franchise.LOCATION_location_id=location.location_id AND franchise.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id AND status=1;");
		}
		else if ($_GET['show'] == 'inactive') {
			$result = mysqli_query($con, "SELECT franchise_id, type, date(startdate) AS Start, date(enddate) AS End, annual_fee, operator.name AS OperatorName, vendor.name AS VendorName, city, province, status FROM franchise, franchise_type, operator, vendor, location WHERE franchise.OPERATOR_operator_id=operator.operator_id AND franchise.VENDOR_vendor_id=vendor.vendor_id AND franchise.LOCATION_location_id=location.location_id AND franchise.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id AND status=0;");
		}
?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Type</th>
					<th>Start to End Date</th>
					<th>Annual Fee</th>
					<th>Operator</th>
					<th>Vendor</th>
					<th>Location</th>
					<th>Active?</th>
					<th></th>
					<th>
						<?php
						if ($_GET['show'] == 'all') {
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
				echo "<td>" . $row['type'] . "</td>";
				echo "<td>" . $row['Start'] . " to " . $row['End'] . "</td>";
				echo "<td>" . $row['annual_fee'] . "</td>";
				echo "<td>" . $row['OperatorName'] . "</td>";
				echo "<td>" . $row['VendorName'] . "</td>";
				echo "<td>" . $row['city'] . ", " . $row['province'] . "</td>";
				echo "<td>" . ($row['status'] == 1 ? "<span class='glyphicon glyphicon-ok'></span>" : "") . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='franchise_id' value=" . $row['franchise_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete' >
						<input type='hidden' name='franchise_id' value=" . $row['franchise_id']. ">
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
		$franchisetypes = mysqli_query($con, "SELECT * FROM franchise_type;");
		$operators = mysqli_query($con, "SELECT * FROM operator;");
		$vendors = mysqli_query($con, "SELECT * FROM vendor;");
		$locations = mysqli_query($con, "SELECT * FROM location;");

?>
	<div class="container content">
		<form action="?insert" role="form" method="post">
			<div class="form-group">
				<label for="ft_type">Franchise Type</label>
				<select name="type" id="ft_type" class="form-control">
					<?php   
					while ($row = mysqli_fetch_array($franchisetypes)) {
						echo "<option value='" . $row['franchisetype_id'] . "'>" . $row['type'] . "</option>";
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="ft_start">Start Date</label>
				<input type="date" class="form-control" id="ft_start" name="Start" required>
			</div>
			<div class="form-group">
				<label for="ft_end">End Date</label>
				<input type="date" class="form-control" id="ft_end" name="End" required>
			</div>
			<div class="form-group">
				<label for="ft_annualfee">Annual Fee</label>
				<input type="number" class="form-control" id="ft_annualfee" name="annual_fee" required>
			</div>
			<div class="form-group">
				<label for="ft_operator">Operator</label>
				<select name="operator" id="ft_operator" class="form-control">
					<?php   
					while ($row = mysqli_fetch_array($operators)) {
						echo "<option value='" . $row['operator_id'] . "'>" . $row['name'] . "</option>";
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="ft_vendor">Vendor</label>
				<select name="vendor" id="ft_vendor" class="form-control">
					<?php   
					while ($row = mysqli_fetch_array($vendors)) {
						echo "<option value='" . $row['vendor_id'] . "'>" . $row['name'] . "</option>";
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="op_location">Location</label>
				<select name="location" class="form-control" id="op_location">
		<?php
				while ($row = mysqli_fetch_array($locations)) {
					echo "<option value='" . $row['location_id'] . "'>" . $row['city'] . ", " . $row['province'] . "</option>";
				}
		?>
				</select>
			</div>
			<div class="form-group">
					<label for="ft_active">Active?</label>
					<div class="row">
						<div class="col-sm-1">
							<input type="hidden" id="ft_active" name="active" value="0">
							<input type="checkbox" id="ft_active" name="active" class="form-control" value=1>
						</div>
					</div>
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
		$Start = $_POST['Start'];
		$End = $_POST['End'];
		$annual_fee = $_POST['annual_fee'];
		$operator = $_POST['operator'];
		$vendor = $_POST['vendor'];
		$location = $_POST['location'];
		$active = $_POST['active'];
		$result = mysqli_query($con, "INSERT INTO franchise(FRANCHISE_TYPE_franchisetype_id, startdate, enddate, annual_fee, OPERATOR_operator_id, VENDOR_vendor_id, LOCATION_location_id, status)
										VALUES(" . $type . ", '" . $Start . "', '" . $End . "', " . $annual_fee . ", " . $operator . ", " . $vendor . ", " . $location . ", " . $active . ");");
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
		if (isset($_POST['franchise_id'])) {
			$result = mysqli_query($con, "SELECT franchise_id, type, date(startdate) AS Start, date(enddate) AS End, annual_fee, operator.name AS OperatorName, vendor.name AS VendorName, city, province, status, franchise.FRANCHISE_TYPE_franchisetype_id, franchise.LOCATION_location_id, franchise.VENDOR_vendor_id, franchise.OPERATOR_operator_id FROM franchise, franchise_type, operator, vendor, location WHERE franchise.OPERATOR_operator_id=operator.operator_id AND franchise.VENDOR_vendor_id=vendor.vendor_id AND franchise.LOCATION_location_id=location.location_id AND franchise.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id AND franchise_id=" . $_POST['franchise_id'] .";");
			$franchisetypes = mysqli_query($con, "SELECT * FROM franchise_type;");
			$operators = mysqli_query($con, "SELECT * FROM operator;");
			$vendors = mysqli_query($con, "SELECT * FROM vendor;");
			$locations = mysqli_query($con, "SELECT * FROM location;");
			$current = mysqli_fetch_array($result);

	?>
		<div class="container content">
			<form action="?update" role="form" method="post">
				<input type="hidden" name="franchise_id" value="<?php echo $_POST['franchise_id']; ?>">
				<div class="form-group">
					<label for="ft_type">Franchise Type</label>
					<select name="type" id="ft_type" class="form-control">
						<?php   
						while ($row = mysqli_fetch_array($franchisetypes)) {
							echo "<option value='" . $row['franchisetype_id'] . "'" . ($row['franchisetype_id'] == $current['FRANCHISE_TYPE_franchisetype_id'] ? " selected" : "") . ">" . $row['type'] . "</option>";
						}
						?>
					</select>
				</div>
				<div class="form-group">
					<label for="ft_start">Start Date</label>
					<input type="date" class="form-control" id="ft_start" name="Start" value="<?php echo $current['Start']; ?>" required>
				</div>
				<div class="form-group">
					<label for="ft_end">End Date</label>
					<input type="date" class="form-control" id="ft_end" name="End" value="<?php echo $current['End']; ?>" required>
				</div>
				<div class="form-group">
					<label for="ft_annualfee">Annual Fee</label>
					<input type="number" class="form-control" id="ft_annualfee" name="annual_fee" value="<?php echo $current['annual_fee']; ?>">
				</div>
				<div class="form-group">
					<label for="ft_operator">Operator</label>
					<select name="operator" id="ft_operator" class="form-control">
						<?php   
						while ($row = mysqli_fetch_array($operators)) {
							echo "<option value='" . $row['operator_id'] . "'" . ($row['operator_id'] == $current['OPERATOR_operator_id'] ? " selected" : "") . ">" . $row['name'] . "</option>";
						}
						?>
					</select>
				</div>
				<div class="form-group">
					<label for="ft_vendor">Vendor</label>
					<select name="vendor" id="ft_vendor" class="form-control">
						<?php   
						while ($row = mysqli_fetch_array($vendors)) {
							echo "<option value='" . $row['vendor_id'] . "'" . ($row['vendor_id'] == $current['VENDOR_vendor_id'] ? " selected" : "") . ">" . $row['name'] . "</option>";
						}
						?>
					</select>
				</div>
				<div class="form-group">
					<label for="op_location">Location</label>
					<select name="location" class="form-control" id="op_location">
			<?php
					while ($row = mysqli_fetch_array($locations)) {
						echo "<option value='" . $row['location_id'] . "'" . ($row['location_id'] == $current['LOCATION_location_id'] ? " selected" : "") . ">" . $row['city'] . ", " . $row['province'] . "</option>";
					}
			?>
					</select>
				</div>
				<div class="form-group">
						<label for="ft_active">Active?</label>
						<div class="row">
							<div class="col-sm-1">
								<input type="hidden" id="ft_active" name="active" value="0">
								<input type="checkbox" id="ft_active" name="active" class="form-control" value=1 <?php echo ($current['status'] == 1 ? "checked" : ""); ?>>
							</div>
						</div>
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
<?php
		}
	}
?>





<?php
/*
	UPDATE
*/
	if (isset($_GET['update'])) {
		$franchise_id = $_POST['franchise_id'];
		$type = $_POST['type'];
		$Start = $_POST['Start'];
		$End = $_POST['End'];
		$annual_fee = $_POST['annual_fee'];
		$operator = $_POST['operator'];
		$vendor = $_POST['vendor'];
		$location = $_POST['location'];
		$active = $_POST['active'];
		$result = mysqli_query($con, "UPDATE franchise SET FRANCHISE_TYPE_franchisetype_id='" . $type . "', startdate='" . $Start . "', enddate='" . $End . "', annual_fee=" . $annual_fee . ", OPERATOR_operator_id=" . $operator . ", VENDOR_vendor_id=" . $vendor . ", LOCATION_location_id=" . $location . ", status=" . $active . " WHERE franchise_id=" . $franchise_id . ";");
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
		$franchise_id = $_POST['franchise_id'];
		$result = mysqli_query($con, "DELETE from franchise WHERE franchise_id=" . $franchise_id . ";");
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
		$result = mysqli_query($con, "TRUNCATE TABLE franchise;");
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





<?php
/*
	HIGHEST EARNING
*/
	if (isset($_GET['earn'])) {
		if ($_GET['earn'] == 'day') {
			$result = mysqli_query($con, "SELECT franchise_id, type, date(startdate) AS Start, date(enddate) AS End, annual_fee, operator.name AS OperatorName, vendor.name AS VendorName, city, province, status 
											FROM franchise, franchise_type, operator, vendor, location, transaction, menu
											WHERE franchise.OPERATOR_operator_id=operator.operator_id AND franchise.VENDOR_vendor_id=vendor.vendor_id 
											AND franchise.LOCATION_location_id=location.location_id 
											AND franchise.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id
											AND transaction.MENU_menu_id= menu.menu_id AND franchise.VENDOR_vendor_id=transaction.VENDOR_vendor_id
											AND DATE(transaction.datetime)=DATE(CURDATE()) GROUP BY transaction.VENDOR_vendor_id ORDER BY SUM(menu.price*menu.pax)
											DESC LIMIT 1;");

		}									
		if ($_GET['earn'] == 'month') {
			$result = mysqli_query($con, "SELECT franchise_id, type, date(startdate) AS Start, date(enddate) AS End, annual_fee, operator.name AS OperatorName, vendor.name AS VendorName, city, province, status 
											FROM franchise, franchise_type, operator, vendor, location, transaction, menu 
											WHERE franchise.OPERATOR_operator_id=operator.operator_id AND franchise.VENDOR_vendor_id=vendor.vendor_id 
											AND franchise.LOCATION_location_id=location.location_id 
											AND franchise.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id
											AND transaction.MENU_menu_id= menu.menu_id AND franchise.VENDOR_vendor_id=transaction.VENDOR_vendor_id
											AND MONTH(transaction.datetime)=MONTH(CURDATE()) GROUP BY transaction.VENDOR_vendor_id ORDER BY SUM(menu.price*menu.pax)
											DESC LIMIT 1;");

		}									
		if ($_GET['earn'] == 'year') {
			$result = mysqli_query($con, "SELECT franchise_id, type, date(startdate) AS Start, date(enddate) AS End, annual_fee, operator.name AS OperatorName, vendor.name AS VendorName, city, province, status 
											FROM franchise, franchise_type, operator, vendor, location, transaction , menu
											WHERE franchise.OPERATOR_operator_id=operator.operator_id AND franchise.VENDOR_vendor_id=vendor.vendor_id 
											AND franchise.LOCATION_location_id=location.location_id 
											AND franchise.FRANCHISE_TYPE_franchisetype_id=franchise_type.franchisetype_id
											AND transaction.MENU_menu_id= menu.menu_id AND franchise.VENDOR_vendor_id=transaction.VENDOR_vendor_id
											AND YEAR(transaction.datetime)=YEAR(CURDATE()) GROUP BY transaction.VENDOR_vendor_id ORDER BY SUM(menu.price*menu.pax)
											DESC LIMIT 1;");

		}									
?>

	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Type</th>
					<th>Start to End Date</th>
					<th>Annual Fee</th>
					<th>Operator</th>
					<th>Vendor</th>
					<th>Location</th>
					<th>Active?</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['type'] . "</td>";
				echo "<td>" . $row['Start'] . " to " . $row['End'] . "</td>";
				echo "<td>" . $row['annual_fee'] . "</td>";
				echo "<td>" . $row['OperatorName'] . "</td>";
				echo "<td>" . $row['VendorName'] . "</td>";
				echo "<td>" . $row['city'] . ", " . $row['province'] . "</td>";
				echo "<td>" . ($row['status'] == 1 ? "<span class='glyphicon glyphicon-ok'></span>" : "") . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='franchise_id' value=" . $row['franchise_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete' >
						<input type='hidden' name='franchise_id' value=" . $row['franchise_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Delete this entry <span class='glyphicon glyphicon-remove'></span></button>
					</form>
				</td>
				";

				echo "</tr>";
			}
		?>
		<tr></tr>
		</table>
	</div>
<?php
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
