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
	<title>Dirty Foods, Inc. | Transaction</title>
	<style>
		.content {
			margin-top: 50px;
			margin-bottom: 50px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1><a href="index.php">Dirty Foods, Inc.</a><br><small><span class="glyphicon glyphicon-usd"></span> TRANSACTIONS</small></h1>
		<hr>
	</div>
	<div class="container text-center">
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Show <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?show=all">All</a></li>
				<li><a href="?show=promo">All Under Promo</a></li>
			</ul>	
		</div>
		<div class="btn-group">
			<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Month <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="?mon=high">With Most Transactions</a></li>
				<li><a href="?mon=top5">Top 5 With Highest Transactions</a></li>
			</ul>
		</div>
		<a href="?count" class="btn btn-primary">Count</a>
		<a href="?sum" class="btn btn-primary">Sum</a>
		<a href="?revenue" class="btn btn-primary">Revenue</a>

	</div>





<?php
/*
	SHOW
*/
	if (isset($_GET['show'])) {
		if ($_GET['show'] == 'all') {
			$result = mysqli_query($con, "SELECT transaction.*, menu.description AS menu, vendor.name AS vendor, customer.name AS customer 
											FROM transaction, menu, vendor, customer WHERE MENU_menu_id=menu_id 
											AND VENDOR_vendor_id=vendor_id AND CUSTOMER_customer_id=customer_id;");
		}
		else if ($_GET['show'] == 'promo') {
			$result = mysqli_query($con, "SELECT transaction.*, menu.description AS menu, vendor.name AS vendor, customer.name AS customer 
												FROM transaction, promo, menu, vendor, customer
												WHERE transaction.datetime >= promo.startdate 
												AND transaction.datetime < promo.enddate
												AND transaction.MENU_menu_id=menu.menu_id 
												AND transaction.VENDOR_vendor_id=vendor.vendor_id 
												AND transaction.CUSTOMER_customer_id=customer.customer_id
												GROUP BY transaction.datetime
												ORDER BY transaction.transaction_id ASC;");
		}
		else if ($_GET['show'] == 'mon') {
			$result = mysqli_query($con, "SELECT transaction. *, description FROM transaction, menu WHERE MENU_menu_id=menu_id;");
		}
		else if ($_GET['show'] == 'top5') {
			$result = mysqli_query($con, "SELECT transaction. *, description FROM transaction, menu WHERE MENU_menu_id=menu_id;");
		}
?>
	<div class="container content">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Date Time</th>
					<th>Menu</th>
					<th>Vendor</th>
					<th>Customer</th>
					<th></th>
					<th>
						<form action="?deleteall" id='delete' method="post"><button type="submit" id='deleteButton' class="btn btn-danger btn-xs">Delete all <span class="glyphicon glyphicon-remove"></span></button></form>
					</th>
				</tr>
			</thead>
		<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['datetime'] . "</td>";
				echo "<td>" . $row['menu'] . "</td>";
				echo "<td>" . $row['vendor'] . "</td>";
				echo "<td>" . $row['customer'] . "</td>";
				echo "
				<td>
					<form action='?edit' method='post'>
						<input type='hidden' name='transaction_id' value=" . $row['transaction_id']. ">
						<button type='submit' class='btn btn-default btn-xs'>Edit this entry <span class='glyphicon glyphicon-pencil'></span></button>
					</form>
				</td>
				";
				echo "
				<td>
					<form action='?delete' method='post' id='delete' >
						<input type='hidden' name='transaction_id' value=" . $row['transaction_id']. ">
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
	$vendors = mysqli_query($con, "SELECT * FROM vendor");
	$customers = mysqli_query($con, "SELECT * FROM customer");

?>
	<div class="container content">
		<form action="?insert" role="form" method="post">
			<div class="form-group">
				<label for="tr_dt">Date Time</label>
				<input type="datetime" class="form-control" id="tr_dt" name="datetime" required>
			</div>

			<div class="form-group">
				<label for="tr_des">Menu</label>
				<select class="form-control" id="tr_des" name="menu">
				<?php
					while ($row = mysqli_fetch_array($menus)) {
						echo "<option value='" . $row['menu_id'] . "'>" . $row['description'] . "</option>";
					}
				?>
				</select>
			</div>
			<div class="form-group">
				<label for="tr_ven">Vendor</label>
				<select class="form-control" id="tr_ven" name="vendor">
				<?php
					while ($row = mysqli_fetch_array($vendors)) {
						echo "<option value='" . $row['vendor_id'] . "'>" . $row['name'] . "</option>";
					}
				?>
				</select>
			</div>
			<div class="form-group">
				<label for="tr_cus">Customer</label>
				<select class="form-control" id="tr_cus" name="customer">
				<?php
					while ($row = mysqli_fetch_array($customers)) {
						echo "<option value='" . $row['customer_id'] . "'>" . $row['name'] . "</option>";
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
		$datetime = date_format(date_create($_POST['datetime']), "Y-m-d H:i:s");
		$menu = $_POST['menu'];
		$vendor = $_POST['vendor'];
		$customer = $_POST['customer'];
		$result = mysqli_query($con, "INSERT INTO transaction(datetime, MENU_menu_id, VENDOR_vendor_id, CUSTOMER_customer_id)
										VALUES('" . $datetime. "', " . $menu. ", " . $vendor. ", " . $customer. ");");
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
		$result = mysqli_query($con, "SELECT * FROM transaction WHERE transaction_id=" . $_POST['transaction_id'] .";");
		$current = mysqli_fetch_array($result);
		$tr_menu = mysqli_query($con, "SELECT * FROM menu;");
		$tr_vendor = mysqli_query($con, "SELECT * FROM vendor;");
		$tr_customer = mysqli_query($con, "SELECT * FROM customer;");
?>
	<div class="container content">
		<form action="?update" role="form" method="post">
			<input type="hidden" name="transaction_id" value="<?php echo $_POST['transaction_id'];?>">
			<div class="form-group">
				<label for="tr_dt">Date Time</label>
				<input type="datetime" class="form-control" id="tr_dt" name="datetime" value="<?php echo date_format(date_create($current['datetime']), "m/d/Y g:i A"); ?>" required>
			</div>
			<div class="form-group">
				<label for="tr_menu">Menu</label>
				<select class="form-control" id="tr_menu" name="menu">
				<?php
					while ($row = mysqli_fetch_array($tr_menu)) {
						echo "<option value='" . $row['menu_id'] . "'" . ($current['MENU_menu_id'] == $row['menu_id'] ? " selected" : "") . ">" . $row['description'] . "</option>";
					}
				?>
				</select>
			</div>
			<div class="form-group">
				<label for="tr_vendor">Vendor</label>
				<select class="form-control" id="tr_vendor" name="vendor">
				<?php
					while ($row = mysqli_fetch_array($tr_vendor)) {
						echo "<option value='" . $row['vendor_id'] . "'" . ($current['VENDOR_vendor_id'] == $row['vendor_id'] ? " selected" : "") . ">" . $row['name'] . "</option>";
					}
				?>
				</select>
			</div>
			<div class="form-group">
				<label for="tr_customer">Customer</label>
				<select class="form-control" id="tr_customer" name="customer">
				<?php
					while ($row = mysqli_fetch_array($tr_customer)) {
						echo "<option value='" . $row['customer_id'] . "'" . ($current['CUSTOMER_customer_id'] == $row['customer_id'] ? " selected" : "") . ">" . $row['name'] . "</option>";
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
		$transaction_id = $_POST['transaction_id'];
		$datetime = date_format(date_create($_POST['datetime']), "Y-m-d H:i:s");
		$menu = $_POST['menu'];
		$vendor = $_POST['vendor'];
		$customer = $_POST['customer'];
		$result = mysqli_query($con, "UPDATE transaction SET datetime='" . $datetime . "', MENU_menu_id=" . $menu . ", VENDOR_vendor_id=" . $vendor . ", CUSTOMER_customer_id=" . $customer . " WHERE transaction_id=" . $transaction_id . ";");
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
		$transaction_id = $_POST['transaction_id'];
		$result = mysqli_query($con, "DELETE from transaction WHERE transaction_id=" . $transaction_id . ";");
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
		$result = mysqli_query($con, "TRUNCATE TABLE transaction;");
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
	COUNT
*/
	if (isset($_GET['count'])) {
		$result= mysqli_query($con, "SELECT COUNT(transaction_id) FROM transaction;");
?>
<center> <div class="jumbotron">
  <h1>
 <?php 
 	while($row = mysqli_fetch_array($result)){
 		echo "<font color= #428bca>". $row[0]. " Transactions </font>";
 	}
 ?>
</h1>
  <p>
  	 <font color=#696969>This is the sum of all the transactions made. <br> To view all transactions, click the button below. </font>
  </p> 
  <br>
  <p><a class="btn btn-primary btn-lg" role="button" href="?show=all">View All</a></p>
</div>
</center>
<?php 
	}
?>

<?php
/*
	SUM
*/
	if (isset($_GET['sum']) || isset($_GET['revenue'])) {
		if (isset($_GET['sum'])) {
			$result = mysqli_query($con, "SELECT FORMAT(SUM(menu.price*menu.pax),2) FROM transaction, menu WHERE transaction.MENU_menu_id=menu.menu_id;");
		}
		else if (isset($_GET['revenue'])) {
			$result = mysqli_query($con, "SELECT FORMAT(sum((price-price*(discount/100))*pax),2) FROM transaction, promo, menu WHERE datetime>promo.startdate AND datetime<promo.enddate AND transaction.MENU_menu_id=promo.MENU_menu_id=menu.menu_id;");
		}
?>
<?php
	// Can I assume that vendor wage is the wage per day? Because CAPITALISM BWAHAHAHAHAHA
	$totalresult = mysqli_query($con, "SELECT sum((price-price*(discount/100))*pax)-(SELECT sum(wage) FROM vendor, vendor_type WHERE VENDOR_TYPE_vendortype_id=vendortype_id) FROM transaction, promo, menu WHERE datetime>promo.startdate AND datetime<promo.enddate AND transaction.MENU_menu_id=promo.MENU_menu_id=menu.menu_id;");
	$total = mysqli_fetch_array($totalresult);
	if ($total[0] > 10000) {
?>
<div class="container content">
	<div class="alert alert-info"><strong>Hooray! </strong>
	You've made more than Php 10,000.00 today!
	<button class="close" data-dismiss="alert"><span>&times;</span></button>
	</div>
</div>
<?php
	}
?>
<div class="jumbotron text-center">
  <h1>
 <?php 
 	while($row = mysqli_fetch_array($result)){
 		echo "<font color= #428bca> Php ". $row[0]. "</font>";
 	}
 ?>
</h1>
  <p>
  	 <font color=#696969> This what you've <?php echo (isset($_GET['revenue']) ? "actually " : ""); ?>earned from all the transactions made. <br> To view all transactions, click the button below. </font>
  </p> 
  <br>
  <p><a class="btn btn-primary btn-lg" role="button" href="?show=all">View All</a></p>
</div>
<?php 
	}
?>


<?php
/*
	MONTH
*/


	if (isset($_GET['mon'])) {
		if($_GET['mon'] == 'high') {
			$result = mysqli_query($con, "SELECT MONTHNAME(datetime) as month
											FROM transaction
											WHERE YEAR(datetime)=YEAR(curdate())
											GROUP BY MONTH(datetime)
											ORDER BY COUNT(transaction_id) DESC LIMIT 1;");
		}
		else if($_GET['mon'] == 'top5') {
			$result = mysqli_query($con, "SELECT MONTHNAME(datetime) as month
											FROM transaction
											WHERE YEAR(datetime)=YEAR(curdate())
											GROUP BY MONTH(datetime)
											ORDER BY COUNT(transaction_id) DESC LIMIT 5;");
		}
?>
<div class="jumbotron text-center">
	<h1>
<?php
	$count = 0;
	while ($row = mysqli_fetch_array($result)) {
		if ($count > 0) {
			echo ", ";
		}
		echo $row['month'];
		$count += 1;
	}
?>
</h1>
<p>
<?php
	if ($count > 1) {
		echo " are the months ";
	}
	else {
		echo " is the month ";
	}
?>
	
	
  		with the most transactions. <br> To view all transactions, click the button below. </font>
	</p> 
	<br>
	<p><a class="btn btn-primary btn-lg" role="button" href="?show=all">View All</a></p>
</div>
<?php
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
	jQuery('#tr_dt').datetimepicker();
});
	
</script>
</html>
