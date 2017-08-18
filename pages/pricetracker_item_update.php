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
	
	$iid = $_GET['iid'];
	$id = $_GET['id'];
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (empty($_POST['price']) || empty($_POST['unit'])) { $notice = '<div class="panel panel-red"><div class="panel-heading">Error: Price & Unit are mandatory</div></div>'; }
		elseif (!is_numeric($_POST['price']) || !is_numeric($_POST['unit'])) { $notice = '<div class="panel panel-red"><div class="panel-heading">Error: Price & Unit must be numerical</div></div>'; }
		else {
			$p_provider = $_POST['provider'];
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
			$sql = "UPDATE `items_price` SET `pid`='$p_provider', `price`='$p_price', `price_desc`='$p_pricedesc', `unit`='$p_unit', `unit_desc`='$p_unitdesc', `unit_price`='$x_unitprice', `url`='$p_url', `note`='$p_note', `last_update`='$lastupdate' WHERE `id`='$id'";
			$result = mysqli_query($link, $sql);
			if ($result) { $notice = '<div class="panel panel-green"><div class="panel-heading">Details has been updated.</div><div class="panel-footer"><a href="pricetracker_item.php?id='.$iid.'">Return to item.</a></div></div>'; }
			else { $notice = '<div class="panel panel-red"><div class="panel-heading">Error: Couldn\'t update the item details.</div></div>'; }
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
					<h1 class="page-header">Price Tracker - Update Item Price</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-12">
					<!-- CODE -->

					<div class="panel panel-default">
						<div class="panel-heading">
							Item Details.
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<?php
								$item = '-'; $cat = '-'; $details = '-';
								if (!empty($_GET['id']) && !empty($_GET['iid'])) {

									# Display Header Informations
									$sql = "SELECT * FROM `items` WHERE `iid` = '$iid'";
									$result = mysqli_query($link, $sql);
									if (mysqli_num_rows($result) > 0) {
										while($row = mysqli_fetch_assoc($result)) {
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
									}

									# Display
									$sql = "SELECT * FROM `items_price` WHERE `id` = '$id'";
									$result = mysqli_query($link, $sql);
									if (mysqli_num_rows($result) > 0) {
										while($row = mysqli_fetch_assoc($result)) {
											$pid = $row['pid'];
											$price = $row['price'];
											$pricedesc = $row['price_desc'];
											$unit = $row['unit'];
											$unitdesc = $row['unit_desc'];
											$unitprice = $row['unit_price'];
											$note = isset($row['note']) ? $row['note'] : '';
											$url = isset($row['url']) ? $row['url'] : '';

											if (!empty($pid)) {
												$sql2 = "SELECT * FROM `providers` WHERE pid = '". $pid ."'";
												$result2 = mysqli_query($link, $sql2);
												if (mysqli_num_rows($result2) > 0) {
													while($row2 = mysqli_fetch_assoc($result2)) {
														$prov = $row2['name'];
													}
												}
											}
											else { $prov = ""; }
										}
									}
								}
							?>
							<div class="row">
								<div class="col-lg-12 col-md-6">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-edit fa-5x"></i>
												</div>
												<div class="col-xs-9 text-right">
													<div class="huge">&nbsp;</div>
												</div>
											</div>
										</div>
										<div class="panel-footer">
											<span class="pull-left">
												<div class="huge"><a href="pricetracker_item.php?id=<?PHP print $iid; ?>"><?PHP print $item; ?></a></div>
												<div>Category: <?PHP print $cat; ?></div>
												<div>Detail: <?PHP print $details; ?></div>
												<div>Provider: <?PHP print $prov; ?></div>
											</span>
											<div class="clearfix"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12 col-md-6">
									<div class="panel panel-primary">
										<div class="panel-heading">
											Details to edit
										</div>
										<div class="panel-body">
											<?PHP if (!empty($notice)) { print $notice; } ?>
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
																	if (empty($pid) || $pid !== $row['pid']) { print '<option value="'. $row["pid"] .'">' . $row["name"] . '</option>'; }
																	else { print '<option value="'. $row["pid"] .'" selected="selected">' . $row["name"] . '</option>'; }
																}
															}
														?>
													</select>
												</div>
												<div class="form-group">
													<label>Price & Packaging description</label>
													<p class="form-inline"><input class="form-control" placeholder="0.00" name="price" value="<?PHP if (!empty($price)) { print $price; } ?>"><i class="fa fa-usd fa-1x"></i>  <input class="form-control" placeholder="" name="pricedesc" value="<?PHP if (!empty($pricedesc)) { print $pricedesc; } ?>"></p>
													<p class="help-block">ex: 25.50$ per box of 20</p>
												</div>
												<div class="form-group">
													<label>Unit & Unit price description</label>
													<p class="form-inline"><input class="form-control" placeholder="1" name="unit" value="<?PHP if (!empty($unit)) { print $unit; } ?>"> <input class="form-control" placeholder="Per Unit" name="unitdesc" value="<?PHP if (!empty($unitdesc)) { print $unitdesc; } ?>"></p>
													<p class="help-block">ex: 20 | Per Stick </p>
												</div>
												<div class="form-group">
													<label>URL</label>
													<input class="form-control" placeholder="http://www.website.com/product.html" type="url" name="url" value="<?PHP if (!empty($url)) { print $url; } ?>">
													<p class="help-block">Input URL of the product from the provider website. </p>
												</div>
												<div class="form-group">
													<label>Note:</label>
													<textarea name="note" class="form-control"><?PHP if (!empty($note)) { print $note; } ?></textarea>
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

</body>

</html>

<?PHP include('xinc.foot.php'); ?>