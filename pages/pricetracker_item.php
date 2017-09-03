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
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (empty($_POST['price']) || empty($_POST['unit'])) { $notice = '<div class="panel panel-red"><div class="panel-heading">Error: Price & Unit are mandatory</div></div>'; }
		elseif (!is_numeric($_POST['price']) || !is_numeric($_POST['unit'])) { $notice = '<div class="panel panel-red"><div class="panel-heading">Error: Price & Unit must be numerical</div></div>'; }
		else {
			$g_iid = $_GET['id'];
			$p_pid = $_POST['provider'];
			$p_price = $_POST['price'];
			$p_pricedesc = $_POST['pricedesc'];
			$p_unit  = $_POST['unit'];
			$p_unitdesc = $_POST['unitdesc'];
			$x_unitprice = $p_price / $p_unit;
			$p_url = $_POST['url'];
			$p_note = $_POST['note'];
			$lastupdate = time();
			
			# Execute MySQL. If there is not error show green panel and notification.
			# Else show red panel and error notification.
			$sql = "INSERT INTO `items_price` (`id`, `iid`, `pid`, `price`, `price_desc`, `unit`, `unit_desc`, `unit_price`, `note`, `url`, `last_update`) VALUES (NULL, '$g_iid', '$p_pid', '$p_price', '$p_pricedesc', '$p_unit', '$p_unitdesc', '$x_unitprice', '$p_note', '$p_url', '$lastupdate')";
			$result = mysqli_query($link, $sql);
			if ($result) { $notice = '<div class="panel panel-green"><div class="panel-heading">Price has been added.</div></div>'; }
			else { $notice = '<div class="panel panel-red"><div class="panel-heading">Error: Couldn\'t add new item price.</div></div>'; }
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
									$iid = $_GET['id'];
									$sql = "SELECT * FROM `items` WHERE `iid` = '$iid'";
									$result = mysqli_query($link, $sql);
									if (mysqli_num_rows($result) < 1) { $data = 'No data to display.'; }
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
												$note = $row2['note'];
												$lastupdate = $row2['last_update'];
												
												if (empty($url)) { $url = '#'; }

												if (empty($note)) { $note_icon = 'fa-comment-o'; }
												if (!empty($note)) { $note_icon = 'fa-commenting'; }

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
												
												$data .= '<tr><td>'.$prov.'</td><td>'.$price.'$ '.$pricedesc.'</td><td>&nbsp;</td><td>'.$unit.'</td><td>'.$unitprice.'$ '.$unitdesc.'</td><td>'.date("Y-m-d H:i:s", $lastupdate).'</td><td><a href="'.$url.'" title="Visit provider\'s item page" target="_BLANK"><i class="fa fa-globe fa-2x"></i></a> &nbsp; <a href="pricetracker_item_update.php?id='.$eid.'&iid='.$iid.'" title="Update this provider\'s data"><i class="fa fa-edit fa-2x"></i></a> &nbsp; <a data-toggle="tooltip" data-placement="left" title="'.$note.'"><i class="fa '.$note_icon.' fa-2x"></i></a></td></tr>';
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
								<table class="table table-bordered" width="100%" id="xdataTable" cellspacing="0">
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
							<br>
							<div class="row">
								<div class="col-lg-12 col-md-6">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<a id="new"></a>New Data
										</div>
										<div class="panel-body">
											<form role="form" method="post">
												<div class="form-group">
													<label>Provider</label>
													<select class="form-control" name="provider">
														<?php
															$sql = "SELECT * FROM `providers` ORDER BY `name` ASC";
															$result = mysqli_query($link, $sql);
															if (mysqli_num_rows($result) < 1) { print ""; }
															else {
																while($row = mysqli_fetch_assoc($result)) {
																	if (empty($p_pid) || $p_pid !== $row['pid']) { print '<option value="'. $row["pid"] .'">' . $row["name"] . '</option>'; }
																	else { print '<option value="'. $row["pid"] .'" selected="selected">' . $row["name"] . '</option>'; }
																}
															}
														?>
													</select>
												</div>
												<div class="form-group">
													<label>Price & Packaging description</label>
													<p class="form-inline"><input class="form-control" placeholder="0.00" name="price" value="<?PHP if (!empty($_POST['price'])) { print $_POST['price']; } ?>"><i class="fa fa-usd fa-1x"></i>  <input class="form-control" placeholder="Per Box of 20" name="pricedesc" value="<?PHP if (!empty($_POST['pricedesc'])) { print $_POST['pricedesc']; } ?>"></p>
													<p class="help-block">ex: 25.50$ per box of 20</p>
												</div>
												<div class="form-group">
													<label>Unit & Unit price description</label>
													<p class="form-inline"><input class="form-control" placeholder="1" name="unit" value="<?PHP if (!empty($_POST['unit'])) { print $_POST['unit']; } ?>"> <input class="form-control" placeholder="Per Unit" name="unitdesc" value="<?PHP if (!empty($_POST['unitdesc'])) { print $_POST['unitdesc']; } ?>"></p>
													<p class="help-block">ex: 20 | Per Stick </p>
												</div>
												<div class="form-group">
													<label>URL</label>
													<input class="form-control" placeholder="http://www.website.com/product.html" type="url" name="url" value="<?PHP if (!empty($_POST['url'])) { print $_POST['url']; } ?>">
													<p class="help-block">Input URL of the product from the provider website. </p>
												</div>
												<div class="form-group">
													<label>Note:</label>
													<textarea name="note" class="form-control"><?PHP if (!empty($_POST['note'])) { print $_POST['note']; } ?></textarea>
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
			$('#xdataTable').dataTable({"lengthMenu": [[50, 100, 250, -1], [50, 100, 250, "All"]]});
		});
	</script>
</body>

</html>

<?PHP include('xinc.foot.php'); ?>