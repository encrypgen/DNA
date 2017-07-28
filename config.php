<?php
    define('DB_SERVER', '');
    define('DB_USERNAME', '');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', '');
    $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
	$email = @$_POST['username'];
	$ip = @$_SERVER['REMOTE_ADDR'];
	$page = 'http://' . @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'];
	$data = date("Y-m-d H:i:s");
	$sql = "INSERT INTO `log` (`id`, `email`, `ip`, `page`, `data`) VALUES (NULL, '$email', '$ip', '$page', '$data');";
	$result = mysqli_query($db,$sql);
	if (!$result) {
    	printf("Error: %s\n", mysqli_error($db));
  	}
	$data_meno_10minuti = date("Y-m-d H:i:s",time()-600);
	$sql = "SELECT * FROM `log` WHERE page ='PASSWORD INVALID' AND ip='$ip' AND email='$email' AND data >= '$data_meno_10minuti';";
	$result = mysqli_query($db,$sql);
	if ( mysqli_num_rows($result) > 5 ) {
		echo "You've been blocked for some minutes for too much attempt !";
		$_POST['username'] = 'User blocked';
	}
	
	$sql = "SELECT * FROM `users` WHERE email='".@$user."' and active='N';";
	$result = mysqli_query($db,$sql);
	if ( mysqli_num_rows($result) > 0 ) {
		echo "You are NOT active !";
		exit();
	}

?>