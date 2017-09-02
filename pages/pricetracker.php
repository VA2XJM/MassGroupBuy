<?PHP
	session_start();
	include('xinc.config.php');

	# Check if session exists.
	#  If Session (UID) is not existing, redirect to login.php
	#  Else show the page.
	if (empty($_SESSION['username'])) {
		header('location:login.php');
		die();
	}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>MassGroupBuy</title>
	
	<!-- PrepInventory CSS -->
	<link href="../dist/css/PrepInventory.css" rel="stylesheet">

	<!-- Bootstrap Core CSS -->
	<link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- MetisMenu CSS -->
	<link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

	<!-- Timeline CSS -->
	<link href="../dist/css/timeline.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="../dist/css/sb-admin.css" rel="stylesheet">
	<link href="../dist/css/sb-admin-2.css" rel="stylesheet">
	<link href="../dist/css/dataTables.bootstrap4.css" rel="stylesheet">

	<!-- Morris Charts CSS -->
	<link href="../bower_components/morrisjs/morris.css" rel="stylesheet">

	<!-- Custom Fonts -->
	<link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>

<body>

	<div id="wrapper">

		<!-- Navigation -->
		<?PHP include('xinc.nav.php'); ?>

		<div id="page-wrapper">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Price Tracker - Items</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-12">
					<!-- CODE -->

					<div class="panel panel-default">
						<div class="panel-heading">
							Items listing.
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<?php
								$data = '';
								$sql = "SELECT * FROM `items` ORDER BY `name` ASC";
								$result = mysqli_query($link, $sql);
								if (mysqli_num_rows($result) < 1) { $data = '<tr><td colspan="6">No data to display.</td></tr>'; }
								else {
									while($row = mysqli_fetch_assoc($result)) {
										$iid = $row['iid'];
										$item = $row['name'];
										$details = isset($row['details']) ? $row['details'] : '';
										$cid = isset($row['cat']) ? $row['cat'] : '';

										if (!empty($cid)) {
											$sql2 = "SELECT * FROM `category` WHERE cid = '". $cid ."'";
											$result2 = mysqli_query($link, $sql2);
											if (mysqli_num_rows($result2) > 0) {
												while($row2 = mysqli_fetch_assoc($result2)) {
													$cat = $row2['name'];
												}
											}
										}
										else { $cat = ""; }

										# Get best price
										$sql2 = "SELECT * FROM `items_price` WHERE iid = '". $iid ."' ORDER BY `unit_price` ASC LIMIT 1";
										$result2 = mysqli_query($link, $sql2);
										if (mysqli_num_rows($result2) > 0) {
											while($row2 = mysqli_fetch_assoc($result2)) {
												$bestprice = $row2['unit_price'];
												$bestunit = $row2['unit_desc'];
											}
										}
										else { $bestprice = '-'; $bestunit = ''; $bestpriceprov = '-'; $bppid = ''; }

										#If there is a price, find all providers with this unit price.
										if (!empty($bestprice)) {
											$bestpriceprov = '';
											$sql2 = "SELECT DISTINCT(pid) FROM `items_price` WHERE `iid` = '".$iid."' AND unit_price = '". $bestprice ."'";
											$result2 = mysqli_query($link, $sql2);
											if (mysqli_num_rows($result2) > 0) {
												while($row2 = mysqli_fetch_assoc($result2)) {
													$bppid = $row2['pid'];
													$sql3 = "SELECT * FROM `providers` WHERE pid = '". $bppid ."'";
													$result3 = mysqli_query($link, $sql3);
													if (mysqli_num_rows($result3) > 0) {
														while($row3 = mysqli_fetch_assoc($result3)) {
															$provname = $row3['name'];
															if (!empty($bestpriceprov)) { $bestpriceprov .= ', '; }
															$bestpriceprov .= $provname; 
														}
													}
												}
											}
										}
										else { $bestpriceprov = "-"; }

										$sql2 = "SELECT AVG(unit_price) FROM `items_price` WHERE iid = '". $iid ."'";
										$result2 = mysqli_query($link, $sql2);
										if (mysqli_num_rows($result2) > 0) { while($row2 = mysqli_fetch_assoc($result2)) { $avgprice = $row2['AVG(unit_price)']; } }
										if (empty($avgprice)) { $avgprice = "-"; }

										$data .= '<tr><td><a href="pricetracker_item.php?id='.$iid.'">'.$item.'</a></td><td>'.$details.'</td><td>'.$cat.'</td><td>'.$bestprice.'$ '.$bestunit.'</td><td>'.$bestpriceprov.'</td><td>'.$avgprice.'$</td></tr>';
									}
								}
							?>
							<div class="table-responsive">
								<table class="table table-bordered" width="100%" id="dataTable" cellspacing="0">
									<thead>
										<tr>
											<th>Items</th>
											<th>Details</th>
											<th>Categories</th>
											<th>Best Price</th>
											<th>B. P. Provider</th>
											<th>Average Price</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>Items</th>
											<th>Details</th>
											<th>Categories</th>
											<th>Best Price</th>
											<th>B. P. Provider</th>
											<th>Average Price</th>
										</tr>
									</tfoot>
									<tbody>
										<?PHP print $data; ?>
									</tbody>
								</table>
							</div>
						</div>
						<!-- /.panel-body -->
					</div>
					<!-- /.panel -->

					<!-- /CODE -->
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
		</div>
		<!-- /#page-wrapper -->

	</div>
	<!-- /#wrapper -->

	<!-- jQuery -->
	<script src="../bower_components/jquery/dist/jquery.min.js"></script>
	<script src="../bower_components/datatables-responsive/media/js/dataTables.responssive.js"></script>
	<script src="../bower_components/jquery-easing/jquery.easing.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

	<!-- Metis Menu Plugin JavaScript -->
	<script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

	<!-- Custom Theme JavaScript -->
	<script src="../dist/js/sb-admin.min.js"></script>
	<script src="../dist/js/sb-admin-2.js"></script>
	<script src="../dist/js/dataTables.bootstrap4.js"></script>
	<script src="../dist/js/jquery.dataTables.js"></script>

</body>

</html>

<?PHP include('xinc.foot.php'); ?>