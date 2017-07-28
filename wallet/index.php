<?php
	require_once 'checkrole.php';
	require_once 'functions.php';
	
	function isMobile() {
    	return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
	
	if(isMobile()){
    	// Do something for only mobile users
    	$width="320px";
    	$height="120px";
	} else {
    	// Do something for only desktop users
    	$width="800px";
    	$height="290px";
	}

	$config=read_config();
	$chain=@$_GET['chain'];
	
	if (strlen($chain))
		$name=@$config[$chain]['name'];
	else
		$name='';

	$page=@$_GET['page'];
	if (!array_search($page,$functions4role[$role])) {
		echo ("Sorry you can't access this page");
		die;
	}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<title>GeneChain DNA Wallet</title>
		<!--
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
		-->
		<link rel="stylesheet" href="bootstrap.min.css">
		<link rel="stylesheet" href="styles.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	</head>
	<body>
		<div class="container">
			<img src="../logo-text-blue.png" alt="Encrypgen" width="<?php echo $width; ?>" height="<?php echo $height; ?>" >

			<h1><a href="./">GeneChain DNA Wallet</a><?php if (strlen($user)) { ?> &ndash; <?php echo html($user)?><?php } ?></h1>

<?php
	if (strlen($chain)) {
		$name=@$config[$chain]['name'];
?>
			
			<nav class="navbar navbar-default" style = "background-color:#3399FF; color:#FFFFFF; padding:3px;">
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<?php if (array_search('node',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>">Node</a></li>
						<?php } ?>
						<?php if (array_search('permissions',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=permissions">Permissions</a></li>
						<?php } ?>
						<?php if (array_search('issue',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=issue" class="pair-first">Issue Asset</a></li>
						<?php } ?>
						<?php if (array_search('update',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=update" class="pair-second">| Update</a></li>
						<?php } ?>
						<?php if (array_search('send',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=send">Send</a></li>
						<?php } ?>
						<?php if (array_search('offer',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=offer" class="pair-first">Create Offer</a></li>
						<?php } ?>
						<?php if (array_search('accept',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=accept" class="pair-second">| Accept</a></li>
						<?php } ?>
						<?php if (array_search('transfer',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=transfer">Transfer</a></li>
						<?php } ?>
						<?php if (array_search('create',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=create">Create Stream</a></li>
						<?php } ?>
						<?php if (array_search('publish',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=publish">Publish</a></li>
						<?php } ?>
						<?php if (array_search('view',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=view">View Streams</a></li>
						<?php } ?>
						<?php if (array_search('stat',$functions4role[$role]) and 1==0) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=stat">Statistics</a></li>
						<?php } ?>
						<?php if (array_search('stat',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=usermgt">User Mgt</a></li>
						<?php } ?>
						<?php if (array_search('logout',$functions4role[$role])) { ?>
							<li><a href="./?chain=<?php echo html($chain)?>&page=logout">Logout</a></li>
						<?php } ?>
					</ul>
				</div>
			</nav>

<?php
		set_multichain_chain($config[$chain]);
		
		switch (@$_GET['page']) {
			case 'label':
			case 'permissions':
			case 'issue':
			case 'update':
			case 'send':
			case 'offer':
			case 'accept':
			case 'create':
			case 'publish':
			case 'view':
			case 'stat':
			case 'transfer':
			case 'usermgt':
			case 'logout':
			case 'asset-file':
				require_once 'page-'.$_GET['page'].'.php';
				break;
			default:
				if ($role == 'Admin') {
					require_once 'page-default.php';
			    }
				break;
		}
		
	} else {
?>
			<p class="lead"><br/>Choose an available node to get started:</p>
		
			<p>
<?php
		foreach ($config as $chain => $rpc)
			if (isset($rpc['rpchost']))
				echo '<p class="lead"><a href="./?chain='.html($chain).'">'.html($rpc['name']).'</a><br/>';
?>
			</p>
<?php
	}
?>
		</div>
	</body>
</html>