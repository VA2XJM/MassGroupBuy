<?PHP
	# PrepInventory Configuration File
	# 
	# Change settings below only if you know what you are doing.
	
	# MySQL - Set to use your MySQL database
	$conf['mysql']['address'] = '127.0.0.1';
	$conf['mysql']['username'] = 'root';
	$conf['mysql']['password'] = 'vertrigo';
	$conf['mysql']['database'] = 'massgroupbuy';

	####################################################################################

	# Do not change settings below, this could break your setup and result in data loss. 
	# Do not change settings below, this could break your setup and result in data loss. 
	# Do not change settings below, this could break your setup and result in data loss. 
	# Do not change settings below, this could break your setup and result in data loss. 
	# Do not change settings below, this could break your setup and result in data loss. 
	$g_version = '0.0.0';

	####################################################################################

	# MySQL connection
	$link = mysqli_connect($conf['mysql']['address'], $conf['mysql']['username'], $conf['mysql']['password'], $conf['mysql']['database']);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
?>