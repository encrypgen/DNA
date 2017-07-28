<?php
   include("config.php");
   session_start();
   $data = date('Y-m-d H:i:s',strtotime('-24 hours'));
   $sql = "DELETE FROM changepasswordrequest WHERE data<'".$data."'";
   $result = mysqli_query($db,$sql);
   if (!$result) {
     printf("Error: %s\n", mysqli_error($db));
	 exit();
   }
   $error="";
   if($_SERVER["REQUEST_METHOD"] == "GET") {
   	   if (isset($_GET['code'])){
   	   	   $code = $_GET['code'];
	       $sql = "SELECT * FROM changepasswordrequest WHERE code = '$code' and data>='".$data."'";
	       $result = mysqli_query($db,$sql);
	       if (!$result) {
		     printf("Error: %s\n", mysqli_error($db));
	    	 exit();
		   }
	       $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	       $count = mysqli_num_rows($result);
		   if ( $count == 1 ) {
		      $myusername  = $row['email'];
		  }
   	   }
   }

   if($_SERVER["REQUEST_METHOD"] == "POST") {
      $myusername = mysqli_real_escape_string($db,$_POST['username']);
      $newpassword = mysqli_real_escape_string($db,$_POST['password']); 
      $retypepassword = mysqli_real_escape_string($db,$_POST['retypepassword']); 
      if ($newpassword!=$retypepassword) {
      	  $error = "Password retype incorrect. please retry.";
      } else {
	      $sql = "SELECT * FROM users WHERE email = '$myusername'";
	      $result = mysqli_query($db,$sql);
	      if (!$result) {
		    printf("Error: %s\n", mysqli_error($db));
	    	exit();
		  }
	      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	      $count = mysqli_num_rows($result);
		  if ( $count == 1 ) {
		      $sql = "UPDATE users SET passcode = PASSWORD('$newpassword') WHERE email = '$myusername'";
		      $result = mysqli_query($db,$sql);
		      if (!$result) {
			    printf("Error: %s\n", mysqli_error($db));
		    	exit();
			  } else {
			    $data = date('Y-m-d H:i:s',strtotime('-24 hours'));
			    $sql = "DELETE FROM changepasswordrequest WHERE email = '$myusername'";
			    $result = mysqli_query($db,$sql);
			    if (!$result) {
			      printf("Error: %s\n", mysqli_error($db));
				  exit();
			    }
				$error = 'Password correctly changed. Click <a href="login.php">here</a> to login.';
			  }
		  } else {
			  $error = "Password NOT changed for duplicated email. Please contact support@encrypgen.com\n";
		  }
      }
   }

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
      <title>Login Page</title>
      
      <style type = "text/css">
         body {
            font-family:Arial, Helvetica, sans-serif;
            font-size:14px;
         }
         
         label {
            font-weight:bold;
            width:100px;
            font-size:14px;
         }
         
         .box {
            border:#666666 solid 1px;
         }
      </style>
      
   </head>
   
   <body bgcolor = "#FFFFFF">
	
      <div align = "center">
		<img src="logo-text-blue.png" alt="Encrypgen" width="<?php echo $width; ?>" height="<?php echo $height; ?>" >
         <div style = "width:300px; border: solid 1px #3399FF; " align = "left">
            <div style = "background-color:#3399FF; color:#FFFFFF; padding:3px;"><b>Login</b></div>
				
            <div style = "margin:30px">
               
               <form action = "" method = "post">
                  <label>Username  : </label><?php echo $myusername; ?><input type="hidden" name="username" value="<?php echo $myusername; ?>"><br /><br />
                  <label>New Password  :</label><input type = "password" name = "password" class = "box"/><br /><br />
                  <label>Retype Password  :</label><input type = "password" name = "retypepassword" class = "box" /><br/><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>
               
               <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
					
            </div>
				
         </div>
			
      </div>

   </body>
</html>