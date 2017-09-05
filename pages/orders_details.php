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

	$action = $_GET['action'];
	$g_oid = $_GET['id'];
	$s_uid = $_SESSION['uid'];

	if (empty($g_oid)) { header('location:orders.php'); }
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (empty($_POST['newqty'])) { $notice = '<div class="panel panel-red"><div class="panel-heading">Error: Quantity is required.</div></div>'; }
		elseif (!is_numeric($_POST['newqty'])) { $notice = '<div class="panel panel-red"><div class="panel-heading">Error: Quantity must be numerical</div></div>'; }
		else {
			$p_qty = $_POST['newqty'];
			$p_item = $_POST['newitem'];
			$p_note = $_POST['newnote'];
			$buydate = time();

			#Order Details
			$sql = "SELECT * FROM `orders` WHERE `oid` = '$g_oid'";
			$result = mysqli_query($link, $sql);
			if (mysqli_num_rows($result) == 1) {
				while($row = mysqli_fetch_assoc($result)) {
					$taxes = $row['taxes_percent'];
				}
			}

			#Pricing details
			$sql2 = "SELECT * FROM `items_price` WHERE `id` = '$p_item'";
			$result2 = mysqli_query($link, $sql2);
			if (mysqli_num_rows($result2) == 1) {
				while($row2 = mysqli_fetch_assoc($result2)) {
					$newunitprice = $row2['price'];
				}
			}

			#Total including taxes
			$taxes_amnt = ($p_qty * $newunitprice) * $taxes / 100;
			$total_taxes = ($p_qty * $newunitprice) + $taxes_amnt;
			# Execute MySQL. If there is not error show green panel and notification.
			# Else show red panel and error notification.
			$sql = "INSERT INTO `orders_items` (`id`, `oid`, `iid`, `buyer`, `buy_date`, `quantity`, `unit_price`, `total_taxes`, `note_buyer`) VALUES (NULL, '$g_oid', '$p_item', '$s_uid', '$buydate', '$p_qty', '$newunitprice', '$total_taxes', '$p_note')";
			$result = mysqli_query($link, $sql);
			if ($result) { $notice = '<div class="panel panel-green"><div class="panel-heading">Price has been added.</div></div>'; $action = 'billrecalc'; }
			else { $notice = '<div class="panel panel-red"><div class="panel-heading">Error: Couldn\'t add new item price.</div></div>'; }
		}
	}

	if (!empty($action) && !empty($g_oid)) {
		#Update the bill total
		if ($action == 'billrecalc') {
			# Collect bill data
			$sql = "SELECT sum(total_taxes) FROM `orders_items` WHERE `oid` = '$g_oid' AND `buyer` = '$s_uid'";
			$result = mysqli_query($link, $sql);
			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) { $billamnt = $row['sum(total_taxes)']; }
			}

			# Check if bill exists
			$sql = "SELECT * FROM `orders_bills` WHERE `oid` = '$g_oid' AND `uid` = '$s_uid'";
			$result = mysqli_query($link, $sql);
			if (mysqli_num_rows($result) == 0) {
				$sql = "INSERT INTO `orders_bills` (`bid`, `oid`, `uid`, `status`, `shipping`, `grand_total`) VALUES (NULL, '$g_oid', '$s_uid', '1', '0', '$billamnt')";
				$result = mysqli_query($link, $sql);
			}
			else {
				$sql = "UPDATE `orders_bills` SET `grand_total` = `shipping` + '$billamnt' WHERE `oid` = '$g_oid' AND `uid` = '$s_uid'";
				$result = mysqli_query($link, $sql);
			}
		}
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
					<h1 class="page-header">Orders - Details</h1>
					<?PHP if (!empty($notice)) { print $notice; } ?>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-12">
					<!-- CODE -->

					<div class="panel panel-default">
						<div class="panel-body">
							<?php
								if (!empty($_GET['id'])) {
									#Order details
									$oid = $_GET['id'];
									$sql = "SELECT * FROM `orders` WHERE `oid` = '$oid'";
									$result = mysqli_query($link, $sql);
									if (mysqli_num_rows($result) < 1) { header('location:orders.php'); }
									else {
										while($row = mysqli_fetch_assoc($result)) {
											$oid = $row['oid'];
											$pid = $row['provider'];
											$uid = $row['creator'];
											$status = $row['status'];
											$closing = $row['closing'];
											$visibility = $row['visibility'];
											$visibility_group = $row['visibility_group'];
											$details = $row['details'];
											$taxes_percent = $row['taxes_percent'];

											# Expiration Check
											if ($closing < time()) {

											}

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
										}
										
										### Fill Table
										$data = '';
										$sql = "SELECT * FROM `orders_items` WHERE `oid` = '$oid'";
										$result = mysqli_query($link, $sql);
										if (mysqli_num_rows($result) < 1) { $data = '<tr><td colspan="8">No items to display.</td></tr>'; }
										else {
											while($row = mysqli_fetch_assoc($result)) {
												$id = $row['id'];
												$oid = $row['oid'];
												$iid = $row['iid'];
												$buyer = $row['buyer'];
												$buydate = $row['buy_date'];
												$quantity = $row['quantity'];
												$unitprice = $row['unit_price'];
												$totaltx = $row['total_taxes'];
												$note = $row['note_buyer'];

												# Buyer's name or username
												$sql2 = "SELECT * FROM `users` WHERE uid = '". $buyer ."' LIMIT 1";
												$result2 = mysqli_query($link, $sql2);
												if (mysqli_num_rows($result2) > 0) {
													while($row2 = mysqli_fetch_assoc($result2)) {
														$b_username = $row2['username'];
														$b_namef = $row2['name_first'];
														$b_namel = $row2['name_last'];

														if (!empty($b_namef) && !empty($b_namel)) { $buyername = $b_namef.' '.$b_namel; }
														else { $buyername = $b_username; }

														if ($_SESSION['uid'] != $uid && $_SESSION['uid'] != $buyer && $_SESSION['role'] != 'admin' && $_SESSION['role'] != 'manager') { $buyername = '-HIDDEN-'; }
													}
												}
												
												#Actions
												$actions = '';

												$data .= '<tr><td>'.$buyername.'</td><td>'.date("Y-m-d H:i:s", $buydate).'</td><td>'.$quantity.'</td><td>'.$unitprice.'$</td><td>'.$totaltx.'$</td><td>'.$note.'</td><td>'.$actions.'</td></tr>';
											}
										}

										#Bill Details
										$buid = $_SESSION['uid'];
										$sql = "SELECT * FROM `orders_bills` WHERE `oid` = '$oid' AND uid = '$buid'";
										$result = mysqli_query($link, $sql);
										if (mysqli_num_rows($result) == 1) {
											while($row = mysqli_fetch_assoc($result)) {
												$bstatus = $row['status'];
												$bshipping = $row['shipping'];
												$btotal = $row['grand_total'];
												$bnotebuyer = $row['note_buyer'];
												$bnoteorderer = $row['note_orderer'];
												$bratingbuyer = $row['rating_buyer'];
												$bratingorderer = $row['rating_orderer'];

												if ($bstatus == '1') { $bstatustext = 'Open'; }
												elseif ($bstatus == '2') { $bstatustext = 'Closed'; }
												elseif ($bstatus == '3') { $bstatustext = 'Ready for review'; }
												elseif ($bstatus == '4') { $bstatustext = 'Accepted - Pending payment'; }
												elseif ($bstatus == '5') { $bstatustext = 'Paid'; }
												elseif ($bstatus == '6') { $bstatustext = 'Refused/Cancelled'; }
												else { $bstatustext = 'UNKNOWN'; }
											}
										}
										else {
											$bstatustext = 'You made no order yet.';
											$bshipping = '0.00';
											$btotal = '0.00';
											$bnotebuyer = '';
											$bnoteorderer = '';
										}
									}
								}
							?>
							<div class="row">
								<div class="col-lg-12 col-md-6">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-4">
													<div class="huge"><i class="fa fa-info fa-2x"></i> <?PHP print sprintf("%010d", $oid); ?></div>
													<div>Status: <?PHP print $status; ?></div>
												</div>
												<div class="col-xs-8 text-right">
													<div class="huge"><?PHP print $provider; ?></div>
													<div>Closing: <?PHP print date("Y-m-d H:i:s", $closing); ?></div>
													<div>Created by: <?PHP print "$creator ($creator_rating)"; ?></div>
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
							<div class="row">
								<div class="col-lg-12 col-md-6">
									<div class="panel panel-primary">
										<div class="panel-body">
											<div class="row">
												<div class="col-xs-6">
													<div class="big"><i class="fa fa-shopping-basket"></i> <?PHP print $bstatustext; ?></div>
												</div>
												<div class="col-xs-6">
													<div class="pull-right">
														<i class="fa fa-truck"></i> <b>Shipping:</b><?PHP print $bshipping; ?>
														<br><i class="fa fa-credit-card"></i> <b>Order Total:</b><?PHP print $btotal; ?>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-xs-6">
													<div><b>Buyer Note:</b></div>
												</div>
												<div class="col-xs-6">
													<div><b>Order Manager Note:</b></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="table-responsive">
								<table class="table table-bordered" width="100%" id="xdataTable" cellspacing="0">
									<thead>
										<tr>
											<th>Buyer</th>
											<th>Date</th>
											<th>Qty</th>
											<th>Unit Price</th>
											<th>Total Inc. Taxes</th>
											<th>Note</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>Buyer</th>
											<th>Date</th>
											<th>Qty</th>
											<th>Unit Price</th>
											<th>Total Inc. Taxes</th>
											<th>Note</th>
											<th>Actions</th>
										</tr>
									</tfoot>
									<tbody>
										<?PHP print $data; ?>
									</tbody>
								</table>
							</div>
							<br>
							<div class="row">
								<div class="col-lg-12 col-md-6">
									<div class="panel panel-primary">
										<div class="panel-heading">
											Order new item
										</div>
										<div class="panel-body">
											<form role="form" method="post">
												<div class="form-group">
													<label>Quantity, Item & Note</label>
													<p class="form-inline">
													<input class="form-control" placeholder="0" name="newqty" value="<?PHP if (!empty($_POST['newqty'])) { print $_POST['newqty']; } ?>"><i class="fa fa-times fa-1x"></i>
													<select class="form-control" name="newitem">
														<?php
															$sql = "SELECT * FROM `items` ORDER BY `name` ASC";
															$result = mysqli_query($link, $sql);
															if (mysqli_num_rows($result) < 1) { print ""; }
															else {
																while($row = mysqli_fetch_assoc($result)) {
																	$niid = $row['iid'];
																	$sql2 = "SELECT * FROM `items_price` WHERE `iid` = '$niid' AND `pid` = '$pid' ORDER BY `unit_price` ASC";
																	$result2 = mysqli_query($link, $sql2);
																	if (mysqli_num_rows($result2) < 1) { print ""; }
																	else {
																		while($row2 = mysqli_fetch_assoc($result2)) {
																			if (empty($_POST['newitem']) || $_POST['newitem'] !== $row2['id']) { print '<option value="'. $row2["id"] .'">' . $row["name"] . ' - '.$row2['price'].' '.$row2['price_desc'].' ('.$row2['unit_price'].' '.$row2['unit_desc'].')</option>'; }
																			else { print '<option value="'. $row2["id"] .'" selected="selected">' . $row["name"] . ' - '.$row2['price'].'$ '.$row2['price_desc'].' ('.$row2['unit_price'].'$ '.$row2['unit_desc'].')</option>'; }
																		}
																	}
																}
															}
														?>
													</select>
													<input class="form-control" placeholder="Detail, Color, Size" name="newnote" value="<?PHP if (!empty($_POST['newnote'])) { print $_POST['newnote']; } ?>">
													</p>
													<p class="help-block">Total price reported above will include <?PHP print $taxes_percent; ?>% of taxes.</p>
												</div>
												<button type="submit" class="btn btn-default">Submit</button>
											</form>
										</div>
									</div>
								</div>
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

	<script>
		$(document).ready(function(){
			$('#xdataTable').dataTable({"lengthMenu": [[-1], ["All"]], "order": [[ 0, 'desc' ]]});
		});
	</script>
</body>

</html>

<?PHP include('xinc.foot.php'); ?>