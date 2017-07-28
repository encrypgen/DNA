<?php
	include("config.php");
	print_r($_SESSION);
	$myusername = "";
	$email = @$_SESSION['login_user'];
	$ip = "";
	$page = 'http://' . @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'];
	$data = date("Y-m-d H:i:s");
	$sql = "INSERT INTO `log` (`id`, `username`, `email`, `ip`, `page`, `data`) VALUES (NULL, '$myusername', '$email', '$ip', '$page', '$data');";
	$result = mysqli_query($db,$sql);
	if (!$result) {
    	printf("Error: %s\n", mysqli_error($db));
  	}
?>