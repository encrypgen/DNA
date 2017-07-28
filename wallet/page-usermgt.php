<?php
   error_reporting(E_ALL);
   include("../config.php");

	if (@$_POST['disableuser']) {
		print_r(@$_POST['disableuser']);
	}
    function password( $passlen = 8 ) {
  	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        return substr( str_shuffle( $chars ), 0, $passlen );
    }

?>

			<div class="row">

				<div class="col-sm-6">
					<h3>Insert/Modify a new User</h3>
<?php
    $id = intval(mysqli_real_escape_string($db,@$_POST['id']));
    if ($id==0) {
	    $id = intval(mysqli_real_escape_string($db,@$_GET['id']));
    }
	  if (@$_POST['action']=="insertnewuser") {
		  no_displayed_error_result($getnewaddress, multichain('getnewaddress'));
	      $sql = "INSERT INTO users ( role, username, email, passcode, active, address, data ) 
	      		  VALUES ('".$_POST['role']."', '".$_POST['username']."', '".$_POST['email']."', PASSWORD('".password(8)."'), '".$_POST['active']."', '".$getnewaddress."', '".date("Y-m-d H:i:s")."' )";
	      $result = mysqli_query($db,$sql,MYSQLI_USE_RESULT);
	      if (!$result) {
		    printf("Error: %s\n", mysqli_error($db));
	    	exit();
		  }
		  $id = mysqli_insert_id($db);
		  $success=no_displayed_error_result($permissiontxid, multichain('grantfrom', "1NR6eYpdGunZ45qydVMUZmSvEcmT1A6HQWYgpL", $getnewaddress, "connect,send,receive"));
		  if ( @$_POST['balance'] ) {
			  $success=no_displayed_error_result($balance, multichain('sendassetfrom', "1NR6eYpdGunZ45qydVMUZmSvEcmT1A6HQWYgpL", $getnewaddress, "DNA", (float)$_POST['balance']));
		  }
	  }
	
	  if (@$_POST['action']=="updateuser" AND intval(@$_POST['id'])>0) {
	      $sql = "UPDATE users SET role = '".$_POST['role']."', 
	      						   username = '".$_POST['username']."', 
	                               email = '".$_POST['email']."', 
	                               active = '".$_POST['active']."' 
	                               WHERE id = ".$_POST['id'];
	      $result = mysqli_query($db,$sql);
	      if (!$result) {
		    printf("Error: %s\n", mysqli_error($db));
	    	exit();
		  }
		  $id = 0;
	  }
      $sql = "SELECT * FROM users WHERE id = $id";
      $result = mysqli_query($db,$sql);
      if (!$result) {
	    printf("Error: %s\n", mysqli_error($db));
    	exit();
	  }
      $users = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $rolem = '<select id="role" name="role">';
      $rolem.= '<option value="Admin" '.(($users['role']=='Admin') ? 'selected' : ' ').'>Admin</option>';
      $rolem.= '<option value="User"  '.(($users['role']=='User' OR $id==0 ) ? 'selected' : ' ').'>User</option>';
	  $rolem.= '</select>';
      $active = '<select id="active" name="active">';
      $active.= '<option value="Y" '.(($users['active']=='Y') ? 'selected' : ' ').'>Yes</option>';
      $active.= '<option value="N" '.(($users['active']=='N') ? 'selected' : ' ').'>Not</option>';
	  $active.= '</select>';

	  $balance = "";
	  if ( @$users['address'] ) {
		  if (no_displayed_error_result($unlockedbalances, multichain('getaddressbalances', $users['address'], 0, false)) ) {
		  	  if (@$unlockedbalances[0]['qty']) {
			  	  $balance=html(number_format(@$unlockedbalances[0]['qty'],2));
		  	  } else {
			  	  $balance=html('------------');
		  	  }
		  }
	  }
?>
				<form method="POST" >
					<input type="hidden" name="id" value="<?php echo $users['id']; ?>">
					<table class="table table-bordered table-striped">
						<tr>
							<th>Id</th>
							<td><?php echo html($users['id'])?></td>
						</tr>
						<tr>
							<th>Role</th>
							<td><?php echo $rolem ?></td>
						</tr>
						<tr>
							<th>User name</th>
							<td><input type="text" name="username" value="<?php echo html($users['username'])?>"/></td>
						</tr>
						<tr>
							<th>Email address</th>
							<td><input type="text" name="email" value="<?php echo html($users['email'])?>"/></td>
						</tr>
						<tr>
							<th>Active</th>
							<td><?php echo $active ?></td>
						</tr>
						<tr>
							<th>Address</th>
							<td><?php echo html($users['address'])?></td>
						</tr>
						<tr>
							<th>Balance</th>
<?php if ( intval($id) == 0 ) { ?>
							<td> <img src="../favicon.ico" alt="DNA" height="40" width="40"/> <input type="text" name="balance" value=""/></td>
<?php } else { ?>
							<td> <img src="../favicon.ico" alt="DNA" height="40" width="40"/> <?php echo html($balance)?></td>
<?php } ?>
						</tr>
						<tr>
							<th>Data</th>
							<td><?php echo html($users['data'])?></td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center">
									<?php if ($id>0) {?>
										<button type="submit" name="action" value="updateuser">Update User</button>
									<?php } else { ?>
										<button type="submit" name="action" value="insertnewuser">Insert New User</button>
									<?php } ?>
								</div>
							</td>
						</tr>
					</table>
				</form>

					<h3>Select User to modify</h3>
<?php
      $sql = "SELECT * FROM users ORDER BY role, username, email";
      $result = mysqli_query($db,$sql);
      if (!$result) {
	    printf("Error: %s\n", mysqli_error($db));
    	exit();
	  }
      $users = '<select id="id" name="id"  onchange="this.form.submit()">';
      $users.= '<option value="">None</option>';
	  while($userid = mysqli_fetch_array($result,MYSQLI_ASSOC) ){
        $users.= '<option value="'.$userid['id'].'">'.$userid['username'].' / '.$userid['email'].'</option>';
      }
	  $users.= '</select>';
	if (no_displayed_error_result($peerinfo, multichain('getpeerinfo'))) {
?>
				<form method="POST" >
					<table class="table table-bordered table-striped table-break-words">
						<tr>
							<td colspan="2"><?php echo $users;?></td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center">
									<button type="submit" name="action" value="selectuser2update" >Select User to Update</button>
								</div>
							</td>
						</tr>
					</table>
				</form>
<?php	
	}

?>
				
	
						<h3>Search User by string</h3>
				<form method="POST" >
					<table class="table table-bordered table-striped table-break-words">
						<tr>
							<td colspan="2"><input type="text" name="stringtosearch" size="58" value="<?php if (@$_POST['stringtosearch']) { echo $_POST['stringtosearch'];} ?>" ></td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center">
									<button type="submit" name="action" value="selectuserbysearch" >Search User by string</button>
								</div>
							</td>
						</tr>
					</table>
				</form>
<?php
	  if (@$_POST['action']=="selectuserbysearch" AND @$_POST['stringtosearch'] != "" ) {
		  $sql = "SELECT * FROM users WHERE username LIKE '%".$_POST['stringtosearch']."%' OR email LIKE '%".$_POST['stringtosearch']."%' ORDER BY role, username, email";
	      $result = mysqli_query($db,$sql);
	      if (!$result) {
		    printf("Error: %s\n", mysqli_error($db));
	    	exit();
		  }
		  while($users = mysqli_fetch_array($result,MYSQLI_ASSOC) ){
?>
					<table class="table table-bordered table-striped">
						<tr>
							<th style="width:30%;">Id</th>
							<td><?php echo html($users['id'])?></td>
						</tr>
						<tr>
							<th style="width:30%;">Username</th>
							<td><?php echo html($users['username'])?></td>
						</tr>
						<tr>
							<th style="width:30%;">Email</th>
							<td><?php echo '<a href="'.'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'&amp;id='.$users['id'].'">'.$users['email']."</a>"; ?></td>
						</tr>
					</table>
<?php
	      }
	  }
	if (no_displayed_error_result($peerinfo, multichain('getpeerinfo'))) {
?>

<?php	
	}
?>
	
			<div style="color:red"><h3>PANIC BUTTON - SHUT DOWN the GeneChain node</h3></div>
				This function works <b>if and ONLY if</b> node is hosted on the same PC.
				After ShutDown the <b>only way</b> to restart the GeneChain is using the Jupyter Notebook
				<form method="POST" >
					<table class="table table-bordered table-striped table-break-words">
						<tr>
							<td colspan="2"><input type="password" name="password4shutdown" value="" > Re-type YOUR Admin Password</td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center">
									<button type="submit" name="action" value="shutdownthegenechain" >Shut down the GeneChain</button>
								</div>
							</td>
						</tr>
					</table>
				</form>
<?php
	  if ($role=="Admin" and @$_POST['action']=="shutdownthegenechain" AND @$_POST['password4shutdown'] != "" ) {
		  $sql = "SELECT * FROM users WHERE email = '$user' AND passcode = PASSWORD('".$_POST['password4shutdown']."')";
	      $result = mysqli_query($db,$sql);
	      if (!$result) {
		    printf("Error: %s\n", mysqli_error($db));
	    	exit();
		  }
		  $users = mysqli_fetch_array($result,MYSQLI_ASSOC);
		  if ( count($users)>0 ) {
			  if (no_displayed_error_result($multichainstop, multichain('stop'))) {
			  	  echo("GeneChain Node ShutDown effective; You shouldn't see anything but errors on the right side of this page");
		  	  } else {
		  	  	  print_r($multichainstop);
			  	  echo("GeneChain Node ShutDown NOT effective");
		  	  }
		  }
	  }
?>



				</div>

				<div class="col-sm-6">
					<h3>My Addresses</h3>
			
<?php
	if (no_displayed_error_result($getaddresses, multichain('getaddresses', true))) {
		$addressmine=array();
		
		foreach ($getaddresses as $getaddress)
			$addressmine[$getaddress['address']]=$getaddress['ismine'];
		
		$addresspermissions=array();
		
		if (no_displayed_error_result($listpermissions,
			multichain('listpermissions', 'all', implode(',', array_keys($addressmine)))
		))
			foreach ($listpermissions as $listpermission)
				$addresspermissions[$listpermission['address']][$listpermission['type']]=true;
		
		no_displayed_error_result($getmultibalances, multichain('getmultibalances', array(), array(), 0, true));
		
		$labels=multichain_labels();
	
		foreach ($addressmine as $address => $ismine) {
			if (count(@$addresspermissions[$address]))
				$permissions=implode(', ', @array_keys($addresspermissions[$address]));
			else
				$permissions='none';
				
			$label=@$labels[$address];
			$cansetlabel=$ismine && @$addresspermissions[$address]['send'];
			
			if ($ismine && !$cansetlabel)
				$permissions.=' (cannot set label)';
?>
						<table class="table table-bordered table-condensed table-break-words <?php echo ($address==@$getnewaddress) ? 'bg-success' : 'table-striped'?>">
<?php
			if (isset($label) || $cansetlabel) {
?>
							<tr>
								<th style="width:30%;">Label</th>
								<td><?php echo html(@$label)?><?php
								
				if ($cansetlabel)
					echo (isset($label) ? ' &ndash; ' : '').
					'<a href="'.chain_page_url_html($chain, 'label', array('address' => $address)).'">'.
					(isset($label) ? 'change label' : 'Set label').
					'</a>';
				
								?></td>
							</tr>
<?php
			}
?>
							<tr>
								<th style="width:30%;">Address</th>
								<td class="td-break-words small"><?php echo html($address)?><?php echo $ismine ? '' : ' (watch-only)'?></td>
							</tr>
							<tr>
								<th>Permissions</th>
								<td><?php echo html($permissions)?><?php

					echo ' &ndash; <a href="'.chain_page_url_html($chain, 'permissions', array('address' => $address)).'">change</a>';

							?></td></tr>
<?php
			if (isset($getmultibalances[$address])) {
				foreach ($getmultibalances[$address] as $addressbalance) {
?>
							<tr>
								<th><?php echo html($addressbalance['name'])?></th>
								<td><?php echo html($addressbalance['qty'])?></td>
							</tr>
<?php
				}
			}
?>
						</table>
<?php
		}
	}
?>
					<form class="form-horizontal" method="post" action="<?php echo chain_page_url_html($chain)?>">
						<div class="form-group">
							<div class="col-xs-12">
								<input class="btn btn-default" name="getnewaddress" type="submit" value="Get new address">
							</div>
						</div>
					</form>
				</div>
			</div>
