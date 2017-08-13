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
					<h1 class="page-header">Price Tracker - Item Details</h1>
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
								if (!empty($_GET['id'])) {
									$iid = $_GET['id'];
									$sql = "SELECT * FROM `items` WHERE `iid` = '$iid'";
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
										}
										
										$data = '';
										$sql2 = "SELECT * FROM `items_price` WHERE iid = '$iid ' ORDER BY `unit_price` ASC";
										$result2 = mysqli_query($link, $sql2);
										if (mysqli_num_rows($result2) > 0) {
											while($row2 = mysqli_fetch_assoc($result2)) {
												$eid = $row2['id'];
												$price = $row2['price'];
												$pricedesc = $row2['price_desc'];
												$unitprice = $row2['unit_price'];
												$unitdesc = $row2['unit_desc'];
												$unit = $row2['unit'];
												$pid = $row2['pid'];
												$url = $row2['url'];
												$lastupdate = $row2['last_update'];
												
												if (empty($url)) { $url = '#'; }
												if (!empty($pid)) {
													$sql3 = "SELECT * FROM `providers` WHERE pid = '". $pid ."'";
													$result3 = mysqli_query($link, $sql3);
													if (mysqli_num_rows($result3) > 0) {
														while($row3 = mysqli_fetch_assoc($result3)) {
															$prov = $row3['name'];
														}
													}
												}
												else { $prov = "No provider"; }
												
												$data .= '<tr><td>'.$prov.'</td><td>'.$price.'$ '.$pricedesc.'</td><td>&nbsp;</td><td>'.$unit.'</td><td>'.$unitprice.'$ '.$unitdesc.'</td><td>'.$lastupdate.'</td><td><a href="'.$url.'" title="Visit provider\'s item page"><i class="fa fa-globe fa-2x"></i></a> &nbsp; <a href="pricetracker_item_update.php?id='.$eid.'" title="Update this provider\'s data"><i class="fa fa-edit fa-2x"></i></a></td></tr>';
											}
										}
									}
								}
								else {
									$item = '-';
									$details = '-';
									$cat = '-';
								}
							?>
							<div class="row">
								<div class="col-lg-12 col-md-6">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-info fa-5x"></i>
												</div>
												<div class="col-xs-9 text-right">
													<div class="huge"><?PHP print $item; ?></div>
													<div><?PHP print $cat; ?></div>
												</div>
											</div>
										</div>
										<div class="panel-footer">
											<span class="pull-left"><?PHP print $details; ?></span>
											<div class="clearfix"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="table-responsive">
								<table class="table table-bordered" width="100%" id="dataTable" cellspacing="0">
									<thead>
										<tr>
											<th>Provider</th>
											<th>Price</th>
											<th>&nbsp;</th>
											<th>Unit Qty</th>
											<th>Unit Price</th>
											<th>Last Update</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>Provider</th>
											<th>Price</th>
											<th>&nbsp;</th>
											<th>Unit Qty</th>
											<th>Unit Price</th>
											<th>Last Update</th>
											<th>Actions</th>
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