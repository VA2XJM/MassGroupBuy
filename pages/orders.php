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

	<title><?PHP print $g_title; ?></title>
	
	<!-- PrepInventory CSS -->
	<link href="../dist/css/PrepInventory.css" rel="stylesheet">

	<!-- Bootstrap Core CSS -->
	<link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- MetisMenu CSS -->
	<link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

	<!-- Timeline CSS -->
	<link href="../dist/css/timeline.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="../dist/css/sb-admin-2.css" rel="stylesheet">

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
					<h1 class="page-header">Orders</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-12">
					<!-- CODE -->

					<div class="panel panel-default">
						<div class="panel-heading">
							Orders Listing
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<div class="row">
								<center><b>View orders:</b> <a href="orders.php?view=open">Open</a> - <a href="orders.php?view=closed">Closed</a> - <a href="orders.php?view=inprogress">In Progress</a> - <a href="orders.php?view=completed">Completed</a> - <a href="orders.php?view=bo">Back Ordered</a> - <a href="orders.php?view=canceled">Canceled</a> - <a href="orders.php?view=all">All</a> - <a href="orders.php">Default</a></center>
							</div>
							<div class="row">
								<?php
									if (!empty($_GET['view']) && $_GET['view'] == 'all') { $sql = "SELECT * FROM `orders`"; }
									elseif (!empty($_GET['view']) && $_GET['view'] == 'open') { $sql = "SELECT * FROM `orders` WHERE `status` = '1'"; }
									elseif (!empty($_GET['view']) && $_GET['view'] == 'closed') { $sql = "SELECT * FROM `orders` WHERE `status` = '2'"; }
									elseif (!empty($_GET['view']) && $_GET['view'] == 'inprogress') { $sql = "SELECT * FROM `orders` WHERE `status` > '2' AND `status` < '9'"; }
									elseif (!empty($_GET['view']) && $_GET['view'] == 'completed') { $sql = "SELECT * FROM `orders` WHERE `status` = '9'"; }
									elseif (!empty($_GET['view']) && $_GET['view'] == 'bo') { $sql = "SELECT * FROM `orders` WHERE `status` = '10'"; }
									elseif (!empty($_GET['view']) && $_GET['view'] == 'canceled') { $sql = "SELECT * FROM `orders` WHERE `status` = '11'"; }
									else { $sql = "SELECT * FROM `orders`"; } ### Filter only orders linked to user account (User is creator or item ordered)

									$data = '';
									$result = mysqli_query($link, $sql);
									if (mysqli_num_rows($result) < 1) { $data = '<tr><td colspan="5">No data to display.</td></tr>'; }
									else {
										while($row = mysqli_fetch_assoc($result)) {
											$oid = $row['oid'];
											$pid = $row['provider'];
											$uid = $row['creator'];
											$status = $row['status'];
											$closing = $row['closing'];
											$visibility = $row['visibility'];
											$visibility_group = $row['visibility_group'];

											# Status Name
											if ($status == '1') { $status = 'Open'; }
											elseif ($status == '2') { $status = 'Closed'; }
											elseif ($status == '3') { $status = 'Pending Funds'; }
											elseif ($status == '4') { $status = 'Ordered'; }
											elseif ($status == '5') { $status = 'In Transit'; }
											elseif ($status == '6') { $status = 'Arrived'; }
											elseif ($status == '7') { $status = 'Delivering'; }
											elseif ($status == '8') { $status = 'Delivered'; }
											elseif ($status == '9') { $status = 'Completed'; }
											elseif ($status == '10') { $status = 'Back Ordered'; }
											elseif ($status == '11') { $status = 'Canceled'; }
											else { $status = 'Unknown'; }

											# Provider Name
											$sql2 = "SELECT * FROM `providers` WHERE pid = '". $pid ."' LIMIT 1";
											$result2 = mysqli_query($link, $sql2);
											if (mysqli_num_rows($result2) > 0) {
												while($row2 = mysqli_fetch_assoc($result2)) {
													$provider = $row2['name'];
												}
											}

											# Creator Name
											$sql2 = "SELECT * FROM `users` WHERE uid = '". $uid ."' LIMIT 1";
											$result2 = mysqli_query($link, $sql2);
											if (mysqli_num_rows($result2) > 0) {
												while($row2 = mysqli_fetch_assoc($result2)) {
													$creator = $row2['name_first'].' '.$row2['name_last'];
													$creator_rating = $row2['rating'];
													if (empty($row2['name_first']) && empty($row2['name_last'])) { $creator = $row2['username']; }
												}
											}

											if ($visibility == '1') { $data .= '<tr><td><a href="orders_details.php?id='.$oid.'">'.sprintf("%010d", $oid).'</a></td><td>'.$provider.'</td><td>'.$status.'</td><td>'.date("Y-m-d H:i:s", $closing).'</td><td>'.$creator.' ('.$creator_rating.')</td></tr>'; }
											### Elseif visibility allowed in users permissions, show it anyway. elseif in group visibility, show it too! else, you do not deserve it!
										}
									}
								?>
								<div class="table-responsive">
									<table class="table table-bordered" width="90%" id="xdataTable" cellspacing="0">
										<thead>
											<tr>
												<th>Order ID</th>
												<th>Provider</th>
												<th>Status</th>
												<th>Order Closing</th>
												<th>Creator</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th>Order ID</th>
												<th>Provider</th>
												<th>Status</th>
												<th>Order Closing</th>
												<th>Creator</th>
											</tr>
										</tfoot>
										<tbody>
											<?PHP print $data; ?>
										</tbody>
									</table>
								</div>
							</div>
							<!-- /.row -->
						</div>
						<!-- /.panel-body -->

						<a id="myorders"></a>
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

	<!-- Bootstrap Core JavaScript -->
	<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

	<!-- Metis Menu Plugin JavaScript -->
	<script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

	<!-- Custom Theme JavaScript -->
	<script src="../dist/js/sb-admin-2.js"></script>

	<script>
		$(document).ready(function(){
			$('#xdataTable').dataTable({"lengthMenu": [[50, 100, 250, -1], [50, 100, 250, "All"]], "order": [[ 3, 'asc' ], [ 2, 'asc' ], [4, 'asc' ]]});
		});
	</script>
</body>

</html>

<?PHP include('xinc.foot.php'); ?>