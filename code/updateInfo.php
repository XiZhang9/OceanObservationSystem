
<?php
session_start();
// get session data
$PersonID = $_SESSION['PersonID'];
$UserName = $_SESSION['UserName'];
$UserRole = $_SESSION['UserRole'];
include("PHPconnectionDB.php");
?>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
	<body>
		<?php
		
		// make sure user is log in
		if (!$UserName){
			echo 'You are not login yet, you cannot change anything ';
			echo '<br/><br/>';
		} else{
			
			//establish connection
      	$conn=connect();		 		
      	if (!$conn) {
      		echo 'Cannot connect to database!';
      		echo '<br/><br/>';
      	} 
      }
      ?>
      


		<?php
		
		// make sure user is log in and system conncts to database
		if ($UserName){
			if ($conn) {
				
				// make sure user input first name
	   		if ($_POST[first_name]){
	   			
	   			// check format of first name, which should be letters
	   			if (ctype_alpha($_POST[first_name])){
	   				
	   				// update information
	   				$sql = "UPDATE persons SET first_name='$_POST[first_name]' WHERE person_id='$PersonID'";
	   				$stid = oci_parse($conn, $sql );	   
	   				if (oci_execute($stid)){
			      		oci_commit($conn);
			      		
		      		} else {
		      			oci_rollback($conn);
		      		}
	   			}
	   			
	   			// if format is illegal, tell user
	   			else {
	   				$firstnameErr="* Only letters are allowed";
	   			}
	   		}
	   		
	   		// make sure user input last name
	   		if ($_POST[last_name]){
	   			
	   			// check format of last name, which should be letters
	   			if (ctype_alpha($_POST[last_name])){
	   				
	   				// if format is legel, update information 
		   			$sql = "UPDATE persons SET last_name='$_POST[last_name]' WHERE person_id='$PersonID'";
		   			$stid = oci_parse($conn, $sql );	   
		   			if (oci_execute($stid)){
			      		oci_commit($conn);
		      		} else {
		      			oci_rollback($conn);
		      		}
	   			}
	   			
	   			// if format is illegal, tell user
	   			else {
	   				$lastnameErr="* Only letters are allowed";
	   				}
	   		}
	   		
	   		// update address if this field is not empty
	   		if ($_POST[address]){
	   			$sql = "UPDATE persons SET address='$_POST[address]' WHERE person_id='$PersonID'";
	   			$stid = oci_parse($conn, $sql );	   
	   			if (oci_execute($stid)){
		      		oci_commit($conn);
	      		} else {
	      			oci_rollback($conn);
	      		}
	   		}
	   		
	   		// check if email is not empty
	   		if ($_POST[email]){
	   			
	   			// check format of email
	   			if (filter_var($_POST[email], FILTER_VALIDATE_EMAIL)) {
	   				
	   				// update email
		   			$sql = "UPDATE persons SET email='$_POST[email]' WHERE person_id='$PersonID'";
		   			$stid = oci_parse($conn, $sql );	   
		   			if (oci_execute($stid)){
			      		oci_commit($conn);
		      		} else {
		      			oci_rollback($conn);
		      		}
		   		
		   		// if format is illegal, tell user
	   			} else {
	   				$emailErr="* Need to be a valid email address";
	   			}
	   		}
	   		
	   		// check if phone is not empty
	   		if ($_POST[phone]){
	   			
	   			// check format of phone number
	   			if (ctype_digit($_POST[phone]) && strlen($_POST[phone])<=10){
	   				
	   				// update phone number
	   				$sql = "UPDATE persons SET phone='$_POST[phone]' WHERE person_id='$PersonID'";
	   				$stid = oci_parse($conn, $sql );	   
	   				if (oci_execute($stid)){
			      		oci_commit($conn);
		      		} else {
		      			oci_rollback($conn);
		      		}
	   			
	   			// if format is illegal, tell user
	   			} else {
	   				$phoneErr="* Only 10 digits or less numbers are allowed";
	   			}
	   		}		
				
				// show all information of user on the top of the page
				$sql = "SELECT * FROM persons WHERE person_id='$PersonID'";
	      	$stid = oci_parse($conn, $sql);
	      	$res = oci_execute($stid);
	      	
	      	if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
	      		echo 'Your information:<br/><br/>';
	      		foreach ($row as $item) {
	        			$info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
	      			echo $info;
	      			echo '<br/>';
	      		}
	      		echo '<br/>';
	      	}
	      	
	     		// Free the statement identifier when closing the connection
		   	oci_free_statement($stid);
		   	oci_close($conn);	 
		   }
		}
		?>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
      <h2>Please enter your new information: (leave the input box blank if you do not want to change)</h2>
      <br/><br/>
		first_name:  <input type="text" name="first_name" value= "<?php echo $_POST[first_name];?>"/>
		 <span class="error"> <?php echo $firstnameErr;?></span> <br/>
		last_name: <input type="text" name="last_name" value="<?php echo $_POST[last_name];?>"/>
		<span class="error"> <?php echo $lastnameErr;?></span> <br/> 
		
		address: <input type="text" name="address" value="<?php echo $_POST[address];?>"/> <br/> 
		email: <input type="text" name="email" value="<?php echo $_POST[email];?>"/>
		<span class="error"> <?php echo $emailErr;?></span> <br/> 
		phone: <input type="text" name="phone" value="<?php echo $_POST[phone];?>"/>
		<span class="error"> <?php echo $phoneErr;?></span> <br/>     
		<br><br>
   	<input type="submit" name="validate" value="Submit">
		</form>
		
		<form action="<?php if($UserRole=='a'){echo 'welcomeAdmin.html';}elseif($UserRole=='s'){echo 'welcomeSci.html';}else{echo 'welcome.html';}?>">
    	<input type="submit" value="Back to last level">
      </form>
	</body>
</html>
		
