<?php
   include("config.php");
   session_start();
   $error="";
   
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      $myusername = mysqli_real_escape_string($db,$_POST['username']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']); 
	  if (isset($_POST['changepasswordrequest'])) {
	      $changepasswordrequest = mysqli_real_escape_string($db,$_POST['changepasswordrequest']); 
	      $sql = "SELECT * FROM users WHERE email = '$myusername'";
	      $result = mysqli_query($db,$sql);
	      if (!$result) {
		    printf("Error: %s\n", mysqli_error($db));
	    	exit();
		  }
	      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	      $count = mysqli_num_rows($result);
		  if ( $count == 1 ) {
		      $data = date("Y-m-d H:i:s");
		      $codice = hash("sha256",$data);
		      $sql = "INSERT INTO `changepasswordrequest` (`id`, `email`, `code`, `data`) VALUES (NULL, '$myusername', '$codice', '$data');";
		      $result = mysqli_query($db,$sql);
		      if (!$result) {
			    printf("Error: %s\n", mysqli_error($db));
		    	exit();
			  }
			  require('mailserver/PHPMailerAutoload.php');
			  require('mailserver/class.phpmailer.php');
			  require('mailserver/class.smtp.php');
			  require('mailserver/class.pop3.php');
			  $mail = new PHPMailer();
				  $mail->Host = '';
				  $mail->Port = 0;
				  $mail->SMTPSecure = '';
				  $mail->SMTPAuth = True;
				  $mail->Username = '';
				  $mail->Password = '';
			  $mail->SMTPDebug = 0;
			  $mail->IsSMTP();
			  $mail->IsHTML(true);
			  $mail->CharSet="UTF-8";
		      $mail->Debugoutput = 'html';
			  $mail->From = 'wallet@encrypgen.com';
			  $mail->FromName = 'Wallet Encrypgen.com';
			  $mail->AddAddress($myusername);
			  $mail->AddReplyTo('wallet@encrypgen.com', 'Wallet Encrypgen.com');
			  $mail->Subject = 'First Time Access or Change Password Request on GeneChain';
			  $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
			  $mail->Body = "A password change request was issued on the GeneChain site.</br></br>If you haven't request any password modification please don't consider the present email.</br></br>
			  			     Otherwise, click on this link (or copy the url address in your browser) <a href=".'http://65.43.216.44/genechain/recoverypassword.php?code='.$codice.">http://65.43.216.44/genechain/recoverypassword.php?code=$codice</a> to recovery the password not over 24 hours from the issue.";
			  if(!$mail->Send()){
				  echo 'Mailer Error: ' . $mail->ErrorInfo;
				  exit();
			  }
  		      $error = "An email with a link was sent to your address $myusername. You have 24 hours to click on it and change the password";
		  } else {
	          $error = "Email $myusername NOT in users. Please contact Encrypgen at support@encrypgen.com";
		  }
	  } else {
	      $sql = "SELECT * FROM users WHERE email = '$myusername' and passcode = PASSWORD('$mypassword') and active='Y'";
	      $result = mysqli_query($db,$sql);
	      if (!$result) {
		    printf("Error: %s\n", mysqli_error($db));
	    	exit();
		  }
	      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	      $count = mysqli_num_rows($result);
		  if ( $count == 1 ) {
		      $name    = $row['username'];
		      $role    = $row['role'];
		      $address = $row['address'];
		      $active  = $row['active'];
		  } else {
		  	  $active = "N";
		  }
		  if ( $active == "Y" ) {
		      if($count == 1) {
		         $_SESSION["myusername"] = $name;
		         $_SESSION['login_user'] = $myusername;
		         $_SESSION['role'] = $role;
		         $_SESSION['address'] = $address;
		         header("location: welcome.php");
		      }else {
		         $error = "Your Login Name or Password is invalid";
		      }
		  } else {
	          $error = "User $myusername NOT active. Please contact Encrypgen at support@encrypgen.com";
	          $sql = "INSERT INTO `log` (`id`, `email`, `ip`, `page`, `data`) VALUES (NULL, '$email', '$ip', 'PASSWORD INVALID', '$data');";
			  $result = mysqli_query($db,$sql);
			  if (!$result) {
			  	printf("Error: %s\n", mysqli_error($db));
			  }
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
				
            <div align = "center" style = "margin:30px">
               
               <form action = "" method = "post">
                  <label>Email :<br /></label><input type = "text" name = "username" class = "box"/><br /><br />
                  <label>Password  :<br /></label><input type = "password" name = "password" class = "box" /><br/><br />
                  <input type = "checkbox" name = "changepasswordrequest" class = "box" /><label>First time access or<br /> Password Recovery</label><br/><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>
               
               <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
				
            </div>
				
         </div>

         </div>

   </body>
</html>