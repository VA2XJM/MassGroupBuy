		<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php"><?PHP print $g_title; ?></a>
			</div>
			<!-- /.navbar-header -->

			<?PHP
				$sql = "SELECT * FROM `notifications` WHERE `readed`='0' AND `deleted`='0' AND `uid`='".$_SESSION['uid']."' ORDER BY `time` DESC";
				$result = mysqli_query($link, $sql);
				$notifs = mysqli_num_rows($result);
				if ($notifs < 1) { $notifications = '<li>No new notifications.</li>'; }
				else {
					$notifications = '';
					while($row = mysqli_fetch_assoc($result)) {
						$notifications = '<li><a href="#"><div><i class="fa fa-comment fa-fw"></i> New Comment<span class="pull-right text-muted small">4 minutes ago</span></div></a></li>';
					}
				}
			?>
			<ul class="nav navbar-top-links navbar-right">
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="fa fa-exclamation fa-fw"></i>&nbsp;
						<?PHP if ($notifs > 1) { print '<span class="badge badge-pill badge-primary">'.$notifs.' New</span>&nbsp;'; } ?>
						<i class="fa fa-caret-down"></i>
					</a>

                    			<ul class="dropdown-menu dropdown-alerts">
						<?PHP print $notifications; ?>
						<li class="divider"></li>
						<li>
							<a class="text-center" href="notifications.php">
								<strong>See All notifications</strong>
								<i class="fa fa-angle-right"></i>
							</a>
						</li>
					</ul>
					<!-- /.dropdown-tasks -->
				</li>
				<!-- /.dropdown -->

				<?PHP
					if ($_SESSION['role'] == 'admin') { ?>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="fa fa-star fa-fw"></i>  <i class="fa fa-caret-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-user">
						<li><a href="admin_users.php"><i class="fa fa-user fa-fw"></i> User Management</a></li>
					</ul>
					<!-- /.dropdown-user -->
				</li>
					<?PHP }
				?>
				<!-- /.dropdown -->

				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-user">
						<li><a href="#"><i class="fa fa-user fa-fw"></i> <?PHP print $_SESSION['username']; ?></a>
						</li>
						<li><a href="settings.php"><i class="fa fa-gear fa-fw"></i> Settings</a>
						</li>
						<li class="divider"></li>
						<li><a href="login.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
						</li>
					</ul>
					<!-- /.dropdown-user -->
				</li>
				<!-- /.dropdown -->
			</ul>
			<!-- /.navbar-top-links -->

			<div class="navbar-default sidebar" role="navigation">
				<div class="sidebar-nav navbar-collapse">
					<ul class="nav" id="side-menu">
						<li>
							<a href="index.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
						</li>
						<li>
							<a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Orders<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li>
									<a href="orders.php">Orders Overview</a>
								</li>
								<li>
									<a href="orders_new.php">New Orders</a>
								</li>
							</ul>
							<!-- /.nav-second-level -->
						</li>
						<li>
							<a href="#"><i class="fa fa-usd fa-fw"></i> Price Tracker<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li>
									<a href="pricetracker.php">By items</a>
								</li>
								<li>
									<a href="pricetracker_cat.php">By categories</a>
								</li>
								<li>
									<a href="pricetracker_prov.php">By providers</a>
								</li>
							</ul>
							<!-- /.nav-second-level -->
						</li>
						<li>
							<a href="#"><i class="fa fa-cubes fa-fw"></i> Items<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li>
									<a href="items.php">Items</a>
								</li>
								<li>
									<a href="items_categories.php">Categories</a>
								</li>
							</ul>
							<!-- /.nav-second-level -->
						</li>
						<li>
							<a href="#"><i class="fa fa-truck fa-fw"></i> Providers<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li>
									<a href="providers.php">Providers</a>
								</li>
								<li>
									<a href="providers_add.php">New provider</a>
								</li>
							</ul>
							<!-- /.nav-second-level -->
						</li>
						<li>
							<a href="#"><i class="fa fa-support fa-fw"></i> Help & About<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li>
									<a href="https://github.com/xJMV/MassGroupBuy" target="_BLANK">MassGroupBuy <?PHP print $g_version; ?></a>
								</li>
								<li>
									<a href="https://github.com/xJMV/MassGroupBuy" target="_BLANK">GitHub</a>
								</li>
								<li>
									<a href="https://github.com/xJMV/MassGroupBuy/wiki" target="_BLANK">Help</a>
								</li>
								<li>
									<a href="https://github.com/xJMV/MassGroupBuy/issues" target="_BLANK">Bugs & Issues</a>
								</li>
							</ul>
							<!-- /.nav-second-level -->
						</li>
					</ul>
				</div>
				<!-- /.sidebar-collapse -->
			</div>
			<!-- /.navbar-static-side -->
		</nav>