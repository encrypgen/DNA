<?php
   include('session.php');

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

?>
<html>
   
   <head>
      <title>Welcome </title>
   </head>
   
   <body>
      <div align = "center">
		<img src="logo-text-blue.png" alt="Encrypgen" width="<?php echo $width; ?>" height="<?php echo $height; ?>" >
         <div style = "width:300px; border: solid 1px #3399FF; " align = "left">
            <div style = "background-color:#3399FF; color:#FFFFFF; padding:3px;"><b>Welcome <?php echo $_SESSION['myusername']; ?></b></div>
				
            <div style = "margin:30px">
               
		      <h2>Click here to access to the <a href = "wallet/?chain=GeneChainCoin&page=offer">Wallet</a> or <a href = "logout.php">Sign Out</a></h2>
					
            </div>
				
         </div>

         </div>
   </body>
   
</html>