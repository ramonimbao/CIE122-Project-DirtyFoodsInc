<?php
include("db_config.php");
$con = mysqli_connect(db_address,db_user,db_pass,db_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<title>Dirty Foods, Inc. | Operators</title>
	<style>
		.content {
			margin-top: 50px;
		}
		.searchbar {
			margin: 5px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Dirty Foods, Inc.</a><br><small><span class="glyphicon glyphicon-user"></span> OPERATORS</small></h1>
		<hr>
	</div>
	<div class="container text-center">
		<a href="?showall" class="btn btn-primary">Show All</a>
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show Highest Earning <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?earn=day">This Day</a></li>
				<li><a href="?earn=month">This Month</a></li>
				<li><a href="?earn=year">This Year</a></li>
			</ul>
		</div>
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show Revenue By Year <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<form class="form-inline" role="form" method="get">
				<li class="searchbar"><input type="number" class="form-control" name="revenue" autofocus></li>
				<li class="searchbar"><button type="submit" class="btn btn-default">Submit</button></li>
				</form>
			</ul>
		</div>
	</div>





<?php
/*
	SHOW ALL ALPHABETICAL
*/
	if (isset($_GET['showall'])) {
		$result = mysqli_query($con, "SELECT operator_id, name, age, date(birthdate) as birthday, city, province FROM operator, location WHERE LOCATION_location_id=location_id ORDER BY name ASC;");
		$celebrants = mysqli_query($con, "SELECT name, date(birthdate) as birthday FROM operator WHERE date_format(birthdate, '%m-%d')='" . date('m-d') . "' ORDER BY name ASC;");
?>
<?php
	if (mysqli_num_rows($celebrants) > 0) {
?>
	<div class="container content">
		<div class="alert alert-info">
			<strong>Hey! </strong> It's 

			<?php
			$count = 0;
				while ($row = mysqli_fetch_array($celebrants)) {
					if ($count > 0) {
						echo " and ";
					}
					echo $row['name'] . "'s ";
					$count += 1;
				}
				echo "birthday";
				if ($count > 1) {
						echo "s.";
					}
					else {
						echo ".";
					}
			?>
			<button class="close" data-dismiss="alert"><span>&times;</span></button></div>
	</div>
<?php
	}
?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Age</th>
					<th>Birthdate</th>
					<th>Location</th>
					<th></th>
					<th>
						<form action="?deleteall" id='delete' method="post"><button type="submit" class="btn btn-danger btn-xs">Delete all <span class="glyphicon glyphicon-remove"></span></button></form>
					</th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['name'] . "</td>";
				echo "<td>" . $row['age'] . "</td>";
				echo "<td>" . $row['birthday'] . "</td>";
				echo "<td>" . $row['city'] . ", " . $row['province'] . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='operator_id' value=" . $row['operator_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete'>
						<input type='hidden' name='operator_id' value=" . $row['operator_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Delete this entry <span class='glyphicon glyphicon-remove'></span></button>
					</form>
				</td>
				";

				echo "</tr>";
			}
		?>
		<tr><td colspan=6 class="text-center"><a href="?add" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plus"></span> Add new entry</a></td></tr>
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
		$locations = mysqli_query($con, "SELECT * FROM location");
?>
	<div class="container content">
		<form action="?insert" role="form" method="post">
			<div class="form-group">
				<label for="op_name">Name</label>
				<input type="text" class="form-control" id="op_name" name="name" placeholder="Enter name" required>
			</div>
			<div class="form-group">
				<label for="op_age">Age</label>
				<input type="number" class="form-control" id="op_age" name="age" placeholder="Enter age" required>
			</div>
			<div class="form-group">
				<label for="op_bday">Birthdate</label>
				<input type="date" class="form-control" id="op_bday" name="birthday" required>
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
		$name= $_POST['name'];
		$age = $_POST['age'];
		$birthdate = $_POST['birthday']	;
		$LOCATION_location_id = $_POST['location'];
		$result = mysqli_query($con, "INSERT INTO operator(name, age, birthdate, LOCATION_location_id) VALUES('" . $name . "'," . $age . ",'" . $birthdate . "'," . $LOCATION_location_id . ");");
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
		if (isset($_POST['operator_id'])) {
			$result = mysqli_query($con, "SELECT operator_id, name, age, date(birthdate) as birthday, LOCATION_location_id FROM operator WHERE operator_id=" . $_POST['operator_id']);
			$locations = mysqli_query($con, "SELECT * FROM location");
			$current = mysqli_fetch_array($result);
?>
	<div class="container content">
		<form action="?update" role="form" method="post">
			<input type="hidden" name="operator_id" value="<?php echo $_POST['operator_id']; ?>">
			<div class="form-group">
				<label for="op_name">Name</label>
				<input type="text" class="form-control" id="op_name" name="name" value="<?php echo $current['name']; ?>">
			</div>
			<div class="form-group">
				<label for="op_age">Age</label>
				<input type="number" class="form-control" id="op_age" name="age" value="<?php echo $current['age']; ?>">
			</div>
			<div class="form-group">
				<label for="op_bday">Birthdate</label>
				<input type="date" class="form-control" id="op_bday" name="birthday" value="<?php echo $current['birthday']; ?>">
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
		$operator_id = $_POST['operator_id'];
		$name= $_POST['name'];
		$age = $_POST['age'];
		$birthdate = $_POST['birthday'];
		$LOCATION_location_id = $_POST['location'];
		$result = mysqli_query($con, "UPDATE operator SET name='" . $name . "', age=" . $age . ", birthdate='" . $birthdate . "', LOCATION_location_id=" . $LOCATION_location_id . " WHERE operator_id=" . $operator_id . ";");
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
		$operator_id = $_POST['operator_id'];
		$result = mysqli_query($con, "DELETE from operator WHERE operator_id=" . $operator_id . ";");
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
		$result = mysqli_query($con, "TRUNCATE TABLE operator;");
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
	SHOW ALL WITH BIRTHDAY TODAY
*/
	if (isset($_GET['birthday'])) {
		$result = mysqli_query($con, "SELECT operator_id, name, age, date(birthdate) as birthday, city, province FROM operator, location WHERE LOCATION_location_id=location_id AND date_format(birthdate, '%m-%d')='" . date('m-d') . "' ORDER BY name ASC;");
?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Age</th>
					<th>Birthday</th>
					<th>Location</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['name'] . "</td>";
				echo "<td>" . $row['age'] . "</td>";
				echo "<td>" . $row['birthday'] . "</td>";
				echo "<td>" . $row['city'] . ", " . $row['province'] . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='operator_id' value=" . $row['operator_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post'>
						<input type='hidden' name='operator_id' value=" . $row['operator_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Delete this entry <span class='glyphicon glyphicon-remove'></span></button>
					</form>
				</td>
				";

				echo "</tr>";
			}
		?>
		</table>
	</div>
<?php
	}	
?>



<?php
/*
	SHOW HIGHEST EARNING 
*/
	if (isset($_GET['earn'])) {
		if($_GET['earn'] == 'day'){
		$result = mysqli_query($con, "SELECT operator.*, location.city AS city, location.province AS province FROM operator, franchise, menu, transaction, location 
										WHERE operator.LOCATION_location_id=location_id AND transaction.MENU_menu_id = menu.menu_id AND franchise.VENDOR_vendor_id = transaction.VENDOR_vendor_id 
										AND DATE(transaction.datetime)=CURDATE() GROUP BY transaction.VENDOR_vendor_id ORDER BY SUM(menu.price*menu.pax) DESC LIMIT 1;");
		}
		else if ($_GET['earn'] == 'month') {
			$result = mysqli_query($con, "SELECT operator.*, location.city AS city, location.province AS province FROM operator, franchise, menu, transaction, location 
										WHERE operator.LOCATION_location_id=location_id AND transaction.MENU_menu_id = menu.menu_id AND franchise.VENDOR_vendor_id = transaction.VENDOR_vendor_id 
										AND MONTH(transaction.datetime)=MONTH(CURDATE()) GROUP BY transaction.VENDOR_vendor_id ORDER BY SUM(menu.price*menu.pax) DESC LIMIT 1;");		
		}
		else if ($_GET['earn'] == 'year') {
			$result = mysqli_query($con, "SELECT operator.*, location.city AS city, location.province AS province FROM operator, franchise, menu, transaction, location 
										WHERE operator.LOCATION_location_id=location_id AND transaction.MENU_menu_id = menu.menu_id AND franchise.VENDOR_vendor_id = transaction.VENDOR_vendor_id 
										AND YEAR(transaction.datetime)=YEAR(CURDATE()) GROUP BY transaction.VENDOR_vendor_id ORDER BY SUM(menu.price*menu.pax) DESC LIMIT 1;");		
		}
?>		
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Age</th>
					<th>Birthday</th>
					<th>Location</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['name'] . "</td>";
				echo "<td>" . $row['age'] . "</td>";
				echo "<td>" . $row['birthdate'] . "</td>";
				echo "<td>" . $row['city'] . ", " . $row['province'] . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='operator_id' value=" . $row['operator_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post'>
						<input type='hidden' name='operator_id' value=" . $row['operator_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Delete this entry <span class='glyphicon glyphicon-remove'></span></button>
					</form>
				</td>
				";

				echo "</tr>";
			}
		?>
		</table>
	</div>
<?php
}
?>

<?php
if (isset($_GET['revenue'])) {
	$year = $_GET['revenue'];
	$daysInTheYear = date("z", mktime(0,0,0,12,31,$year)) + 1;

	$result = mysqli_query($con, "SELECT * FROM
		(
			SELECT q.operator_id, q.name, FORMAT(sum(q.price*q.pax)-sum(q.price*q.pax)*q.discount/100-q.wage*" . $daysInTheYear . ",2) AS Revenue FROM (SELECT operator.operator_id, operator.name, menu.price, menu.pax, promo.discount, vendor_type.wage FROM operator, franchise, menu, transaction, promo, vendor, vendor_type WHERE transaction.MENU_menu_id=menu.menu_id AND menu.FRANCHISE_TYPE_franchisetype_id=franchise.FRANCHISE_TYPE_franchisetype_id AND franchise.OPERATOR_operator_id=operator.operator_id AND transaction.VENDOR_vendor_id=franchise.VENDOR_vendor_id AND transaction.VENDOR_vendor_id=vendor.vendor_id AND vendor.VENDOR_TYPE_vendortype_id=vendor_type.vendortype_id AND transaction.MENU_menu_id=promo.MENU_menu_id AND (transaction.datetime>promo.startdate AND transaction.datetime<promo.enddate) AND YEAR(transaction.datetime)=" . $year . ") AS q GROUP BY q.operator_id
		UNION
		SELECT operator.operator_id, operator.name, FORMAT(sum(menu.price*menu.pax)-vendor_type.wage*" . $daysInTheYear . ",2) AS Revenue FROM operator, franchise, menu, transaction, vendor, vendor_type WHERE transaction.MENU_menu_id=menu.menu_id AND menu.FRANCHISE_TYPE_franchisetype_id=franchise.FRANCHISE_TYPE_franchisetype_id AND franchise.OPERATOR_operator_id=operator.operator_id AND transaction.VENDOR_vendor_id=franchise.VENDOR_vendor_id AND transaction.VENDOR_vendor_id=vendor.vendor_id AND vendor.VENDOR_TYPE_vendortype_id=vendor_type.vendortype_id AND YEAR(transaction.datetime)=" . $year . " GROUP BY operator_id
		) AS BigQ GROUP BY BigQ.operator_id;");

?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Revenue</th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['name'] . "</td>";
				echo "<td>Php " . $row['Revenue'] . "</td>";
				echo "</tr>";
			}
		?>
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
		$('form#delete').submit(function() {
			var c = confirm("Are you sure you want to continue?");
			return c;
		});
	});
</script>
</html>
