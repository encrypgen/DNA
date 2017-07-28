<?php
	include("../config.php");
	if (@$_POST['unlockoutputs'])
		if (no_displayed_error_result($result, multichain('lockunspent', true)))
			output_success_text('All outputs successfully unlocked');
	
	if (@$_POST['createoffer']) {
		if (no_displayed_error_result($prepare, multichain('preparelockunspentfrom',
				$_POST['from'], array($_POST['offerasset'] => floatval($_POST['offerqty']))))) {
			
			if (no_displayed_error_result($rawexchange, multichain('createrawexchange',
				$prepare['txid'], $prepare['vout'], array($_POST['askasset'] => floatval($_POST['askqty']))))) {
			
				output_success_text('Offer successfully prepared using transaction '.$prepare['txid'].' - please copy the raw offer below.');
				
				echo '<pre>'.html($rawexchange).'</pre>';
			}
		}
	}
	if (@$_POST['transfer']=="Transfer Asset") {
		if (@$_POST['from'] and @$_POST['to'] and @$_POST['asset'] and @$_POST['qty'] and @$_POST['password']) {
		    $sql = "SELECT * FROM users WHERE email = '$user' AND active='Y' AND passcode = PASSWORD('".$_POST['password']."')";
	        $result = mysqli_query($db,$sql);
	        if (!$result) {
		      printf("Error: %s\n", mysqli_error($db));
	    	  exit();
		    }
		    $users = mysqli_fetch_array($result,MYSQLI_ASSOC);
		    if ( count($users)>0 ) {
			    if (no_displayed_error_result($sendassetfrom, multichain('sendassetfrom',$_POST['from'],$_POST['to'],$_POST['asset'],(float)$_POST['qty']))) {
		  	  	  echo("Transaction id: <b>".$sendassetfrom."</b> - Asset Transfer Successful");
		  	    } else {
		  	  	  print_r($sendassetfrom);
			  	  echo("Asset Transfer NOT effectuated");
		  	   }
		  }
		}
	}

?>

			<div class="row">

				<div class="col-sm-5">
					<h3>Available Balances</h3>
			
<?php
	$sendaddresses=array();
	$usableaddresses=array();
	$keymyaddresses=array();
	$keyusableassets=array();
	$allassets=array();
	$haslocked=false;
	$getinfo=multichain_getinfo();
	$labels=array();
	
	if (no_displayed_error_result($getaddresses, multichain('getaddresses', true))) {
		if (no_displayed_error_result($listpermissions,
			multichain('listpermissions', 'send', implode(',', array_get_column($getaddresses, 'address')))
		))
			$sendaddresses=array_get_column($listpermissions, 'address');
			
		foreach ($getaddresses as $address)
			if ($address['ismine'])
				$keymyaddresses[$address['address']]=true;
				
		$labels=multichain_labels();

		if (no_displayed_error_result($listassets, multichain('listassets')))
			$allassets=array_get_column($listassets, 'name');

$toaddresses = $sendaddresses;
$sendaddresses=array($useraddress);

		foreach ($sendaddresses as $address) {
			if (no_displayed_error_result($allbalances, multichain('getaddressbalances', $address, 0, true))) {
				
				if (count($allbalances)) {
					$assetunlocked=array();

					if (no_displayed_error_result($unlockedbalances, multichain('getaddressbalances', $address, 0, false))) {
						if (count($unlockedbalances))
							$usableaddresses[]=$address;
							
						foreach ($unlockedbalances as $balance)
							$assetunlocked[$balance['name']]=$balance['qty'];
					}
					
					$label=@$labels[$address];
?>
						<table class="table table-bordered table-condensed table-break-words <?php echo ($address==@$getnewaddress) ? 'bg-success' : 'table-striped'?>">
<?php
			if (isset($label)) {
?>
							<tr>
								<th style="width:25%;">Label</th>
								<td><?php echo html($label)?></td>
							</tr>
<?php
			}
?>
							<tr>
								<th style="width:25%;">Address</th>
								<td class="td-break-words small"><?php echo html($address)?></td>
							</tr>
<?php
					foreach ($allbalances as $balance) {
						$unlockedqty=floatval($assetunlocked[$balance['name']]);
						$lockedqty=$balance['qty']-$unlockedqty;
						
						if ($lockedqty>0)
							$haslocked=true;
						if ($unlockedqty>0)
							$keyusableassets[$balance['name']]=true;
?>
							<tr>
								<th><?php echo html($balance['name'])?></th>
								<td><img src="../favicon.ico" alt="DNA" height="40" width="40"/><?php echo html(number_format($unlockedqty,2))?><?php echo ($lockedqty>0) ? (' ('.$lockedqty.' locked)') : ''?></td>
							</tr>
<?php
					}
?>
						</table>
<?php
				}
			}
		}
	}
	
	if ($haslocked) {
?>
				<form class="form-horizontal" method="post" action="./?chain=<?php echo html($_GET['chain'])?>&page=<?php echo html($_GET['page'])?>">
					<input class="btn btn-default" type="submit" name="unlockoutputs" value="Unlock all outputs">
				</form>
<?php
	}
?>
				</div>
				
				<div class="col-sm-7">
					<h3>Transfer Asset</h3>
					
					<form class="form-horizontal" method="post" action="./?chain=<?php echo html($_GET['chain'])?>&page=<?php echo html($_GET['page'])?>">
						<input type="hidden" name="from" id="from" value="<?php echo $useraddress; ?>"/>
						<div class="form-group">
							<label for="offerqty" class="col-sm-3 control-label">To Address</label>
							<div class="col-sm-9">
								<input class="form-control" name="to" id="to">
							</div>
						</div>
						<div class="form-group">
							<label for="asset" class="col-sm-3 control-label">Asset:</label>
							<div class="col-sm-9">
							<select class="form-control" name="asset" id="asset">
<?php
	foreach ($keyusableassets as $asset => $dummy) {
?>
								<option value="<?php echo html($asset)?>"><?php echo html($asset)?></option>
<?php
	}
?>						
							</select>
							</div>
						</div>
						<div class="form-group">
							<label for="offerqty" class="col-sm-3 control-label">qty:</label>
							<div class="col-sm-9">
								<input class="form-control" name="qty" id="qty" placeholder="0.0">
							</div>
						</div>
						<div class="form-group">
							<label for="password" class="col-sm-3 control-label">Password:</label>
							<div class="col-sm-9">
								<input type="password" name="password" id="password">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-9">
								<input class="btn btn-default" type="submit" name="transfer" value="Transfer Asset">
							</div>
						</div>
					</form>

				</div>
			</div>