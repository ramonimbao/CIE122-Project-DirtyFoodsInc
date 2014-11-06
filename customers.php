<?php
include("db_config.php");
$con = mysqli_connect(db_address,db_user,db_pass,db_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<title>Dirty Foods, Inc. | Customers</title>
	<style>
		.content {
			margin-top: 50px;
			margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Dirty Foods, Inc.</a><br><small><span class="glyphicon glyphicon-heart-empty"></span> CUSTOMERS</small></h1>
		<hr>
	</div>
	<div class="container text-center">
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?show=all">All</a></li>
				<li><a href="?show=freq">Most Frequent</a></li>
				<li><a href="?show=top10">Top 10 Most Frequent</a></li>
			</ul>
		</div>
	</div>





<?php
/*
	SHOW
*/
	if (isset($_GET['show'])) {
		if ($_GET['show'] == 'all') {
			$result = mysqli_query($con, "SELECT * FROM customer;");
		}
		else if ($_GET['show'] == 'freq') {
			$result = mysqli_query($con, "SELECT customer.* FROM customer, transaction
											WHERE transaction.CUSTOMER_customer_id=customer.customer_id
											GROUP BY customer_id ORDER BY COUNT(transaction.CUSTOMER_customer_id)
											DESC LIMIT 1;");
		}
		else if ($_GET['show'] == 'top10') {
			$result = mysqli_query($con, "SELECT customer.* FROM customer, transaction
											WHERE transaction.CUSTOMER_customer_id=customer.customer_id
											GROUP BY customer_id ORDER BY COUNT(transaction.CUSTOMER_customer_id)
											DESC LIMIT 10;");
		}
?>

<?php
		if ($_GET['show'] == 'all') {
			if (mysqli_num_rows($result) > 100) {
?>
		<div class="container content">
			<div class="alert alert-warning">
				<strong>Warning! </strong> There are more than 100 customers in the database.
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
					<th>Name</th>
					<th>Age</th>
					<th>Birthdate</th>
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
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='customer_id' value=" . $row['customer_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete' >
						<input type='hidden' name='customer_id' value=" . $row['customer_id']. ">
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
				<label for="c_name">Name</label>
				<input type="text" class="form-control" id="c_name" name="name" placeholder="Enter name" required>
			</div>
			<div class="form-group">
				<label for="c_age">Age</label>
				<input type="number" step="any" class="form-control" id="c_age" name="age" placeholder="Enter age" required>
			</div>
			<div class="form-group">
				<label for="c_birthdate">Birthdate</label>
				<input type="date" class="form-control" id="c_birthdate" name="birthday" required>
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
		$result = mysqli_query($con, "INSERT INTO customer(name, age, birthdate)
										VALUES('" . $name . "', " . $age . ", '" . $birthdate . "');");
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
		$result = mysqli_query($con, "SELECT * FROM customer WHERE customer_id=" . $_POST['customer_id'] .";");
		$current = mysqli_fetch_array($result);
?>
	<div class="container content">
		<form action="?update" role="form" method="post">
			<input type="hidden" name="customer_id" value="<?php echo $_POST['customer_id'];?>">
			<div class="form-group">
				<label for="c_name">Name</label>
				<input type="text" class="form-control" id="c_name" name="name" placeholder="Enter name" value="<?php echo $current['name']; ?>" required>
			</div>
			<div class="form-group">
				<label for="c_age">Age</label>
				<input type="number" step="any" class="form-control" id="c_age" name="age" placeholder="Enter age" value="<?php echo $current['age']; ?>" required>
			</div>
			<div class="form-group">
				<label for="c_birthdate">Birthdate</label>
				<input type="date" class="form-control" id="c_birthdate" name="birthday"  value="<?php echo $current['birthdate']; ?>" required>
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
		$customer_id = $_POST['customer_id'];
		$name = $_POST['name'];
		$age = $_POST['age'];
		$birthdate = $_POST['birthday'];
		$result = mysqli_query($con, "UPDATE customer SET name='" . $name . "', age=" . $age . ", birthdate='" . $birthdate . "' WHERE customer_id=" . $customer_id . ";");
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
		$customer_id = $_POST['customer_id'];
		$result = mysqli_query($con, "DELETE from customer WHERE customer_id=" . $customer_id . ";");
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
		$result = mysqli_query($con, "TRUNCATE TABLE customer;");
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
