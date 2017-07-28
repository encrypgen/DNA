<?php
    session_start();
	error_reporting(0);
    if(!isset($_SESSION['login_user']) or !isset($_SESSION['role']) or !isset($_SESSION['address']) ){
       header("location:../login.php");
    }

	$user = $_SESSION['login_user'];
	$useraddress = $_SESSION['address'];
	$role = $_SESSION['role'];
	$functions4role=[ 'User'=> ['send', 'offer', 'accept', 'transfer', 'logout'],
		              'Admin' => ['node', 'label', 'permissions', 'issue',  'update', 'send',
		                  		  'offer', 'accept', 'transfer', 'create', 'publish', 'view',
								  'asset-file', 'stat', 'usermgt', 'logout']
	                ];
?>