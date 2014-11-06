<?php
include("db_config.php");
$con = mysqli_connect(db_address,db_user,db_pass,db_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
	<title>Dirty Foods, Inc. | Promos</title>
	<style>
		.content {
			margin-top: 50px;
			margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Dirty Foods, Inc.</a><br><small><span class="glyphicon glyphicon-exclamation-sign"></span> PROMOS</small></h1>
		<hr>
	</div>
	<div class="container text-center">
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?show=all">All</a></li>
				<li><a href="?show=sell">Highest Selling</a></li>
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
			$result = mysqli_query($con, "SELECT promo. *, description FROM promo, menu WHERE MENU_menu_id=menu_id;");
		}
		else if ($_GET['show'] == 'sell') {
			$result = mysqli_query($con, "SELECT promo.*,description
											FROM transaction, menu, promo
											WHERE promo.MENU_menu_id=menu.menu_id
											AND transaction.MENU_menu_id=menu.menu_id
											AND transaction.datetime >= promo.startdate 
											AND transaction.datetime < promo.enddate
											GROUP BY promo.promo_id
											ORDER BY SUM(menu.price*menu.pax)
											DESC LIMIT 1;");

		}
		else if ($_GET['show'] == 'top10') {
			$result = mysqli_query($con, "SELECT promo.*,description
												FROM transaction, menu, promo
												WHERE promo.MENU_menu_id=menu.menu_id
												AND transaction.MENU_menu_id=menu.menu_id
												AND transaction.datetime >= promo.startdate 
												AND transaction.datetime < promo.enddate
												GROUP BY promo.promo_id
												ORDER BY SUM(menu.price*menu.pax)
												DESC LIMIT 10;");
		}
		$expiringtoday = mysqli_query($con, "SELECT promo.*, description, discount FROM promo, menu WHERE MENU_menu_id=menu_id AND enddate=curdate();");
		if (mysqli_num_rows($expiringtoday) > 0) {
?>
		<div class="container content">
			<div class="alert alert-warning">
				<strong>Hurry! </strong> Some promos are expiring today:

				<?php
				$count = 0;
					while ($row = mysqli_fetch_array($expiringtoday)) {
						echo "<br>" . $row['description'] . " at " . $row['discount'] . "% off.";
					}
				?>
				<button class="close" data-dismiss="alert"><span>&times;</span></button>
			</div>
		</div>
<?php
		}
?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Menu</th>
					<th>Discount</th>
					<th>Start Date</th>
					<th>End Date</th>
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
				echo "<td>" . $row['discount'] . "%</td>";
				echo "<td>" . $row['startdate'] . "</td>";
				echo "<td>" . $row['enddate'] . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='promo_id' value=" . $row['promo_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete' >
						<input type='hidden' name='promo_id' value=" . $row['promo_id']. ">
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
	$menus = mysqli_query($con, "SELECT * FROM menu");
?>
	<div class="container content">
		<form action="?insert" role="form" method="post">
			<div class="form-group">
				<label for="description">Menu</label>
				<select class="form-control" id="description" name="menu">
				<?php
					while ($row = mysqli_fetch_array($menus)) {
						echo "<option value='" . $row['menu_id'] . "'>" . $row['description'] . "</option>";
					}
				?>
				</select>
			</div>
			<div class="form-group">
				<label for="discount">Discount</label>
				<input type="number" step="any" class="form-control" id="discount" placeholder="Enter Discount" name="discount" required>
			</div>
			<div class="form-group">
				<label for="startdate">Start Date</label>
				<input type="datetime" class="form-control" id="startdate" name="start" required>
			</div>
			<div class="form-group">
				<label for="enddate">End Date</label>
				<input type="datetime" class="form-control" id="enddate" name="end" required>
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
		$menu = $_POST['menu'];
		$discount = $_POST['discount'];
		$startdate = date_format(date_create($_POST['start']), "Y-m-d H:i:s");
		$enddate = date_format(date_create($_POST['end']), "Y-m-d H:i:s");
		$result = mysqli_query($con, "INSERT INTO promo(startdate, enddate, discount, MENU_menu_id)
										VALUES('" . $startdate. "', '" . $enddate. "', " . $discount. ", " . $menu . ");");
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
		$result = mysqli_query($con, "SELECT * FROM promo WHERE promo_id=" . $_POST['promo_id'] .";");
		$current = mysqli_fetch_array($result);
		$p_menu = mysqli_query($con, "SELECT * FROM menu;");
?>
	<div class="container content">
		<form action="?update" role="form" method="post">
			<input type="hidden" name="promo_id" value="<?php echo $_POST['promo_id'];?>">
			<div class="form-group">
				<label for="p_menu">Menu</label>
				<select class="form-control" id="p_menu" name="menu">
				<?php
					while ($row = mysqli_fetch_array($p_menu)) {
						echo "<option value='" . $row['menu_id'] . "'" . ($current['MENU_menu_id'] == $row['menu_id'] ? " selected" : "") . ">" . $row['description'] . "</option>";
					}
				?>
				</select>
			</div>
			<div class="form-group">
				<label for="discount">Discount</label>
				<input type="number" step="any" class="form-control" id="discount" name="discount" placeholder="Enter discount" value="<?php echo $current['discount']; ?>" required>
			</div>
			<div class="form-group">
				<label for="startdate">Start Date</label>
				<input type="datetime" class="form-control" id="startdate" name="startdate" value="<?php echo date_format(date_create($current['startdate']), "m/d/Y g:i A"); ?>" required>
			</div>
			<div class="form-group">
				<label for="enddate">End Date</label>
				<input type="datetime" class="form-control" id="enddate" name="enddate" value="<?php echo date_format(date_create($current['enddate']), "m/d/Y g:i A"); ?>" required>
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
		$promo_id = $_POST['promo_id'];
		$menu = $_POST['menu'];
		$discount = $_POST['discount'];
		$startdate = date_format(date_create($_POST['startdate']), "Y-m-d H:i:s");
		$enddate = date_format(date_create($_POST['enddate']), "Y-m-d H:i:s");
		$result = mysqli_query($con, "UPDATE promo SET startdate='" . $startdate . "', enddate='" . $enddate . "', discount=" . $discount . ", MENU_menu_id=" . $menu . " WHERE promo_id=" . $promo_id . ";");
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
		$promo_id = $_POST['promo_id'];
		$result = mysqli_query($con, "DELETE from promo WHERE promo_id=" . $promo_id . ";");
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
		$result = mysqli_query($con, "TRUNCATE TABLE promo;");
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
<script src="js/moment.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/bootstrap-datetimepicker.min.js"></script>
<script>
	$(".dropdown-toggle").dropdown();
$(document).ready(function() {
	//$('#discount').val($('#discount').val() + '%');
	$("form#delete").submit(function() {
		var c = confirm("Are you sure you want to continue?");
		return c;
	});
});
jQuery(function () {
	jQuery('#startdate').datetimepicker();
	jQuery('#enddate').datetimepicker();
	jQuery("#startdate").on("dp.change",function (e) {
        jQuery('#enddate').data("DateTimePicker").setMinDate(e.date);
	});
	jQuery("#enddate").on("dp.change",function (e) {
        jQuery('#startdate').data("DateTimePicker").setMaxDate(e.date);
	});
});
	
</script>
</html>
