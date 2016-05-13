<?php
// start session
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
		if ($UserName== NULL){
			echo 'You are not login yet, you cannot change anything ';
			echo '<br/><br/>';
		} else{
			
			// make sure user is an admin
			if ($UserRole!='a'){
				echo 'You are not admin, you cannot createSensor';
			} else {
				
			//establish connection
      	$conn=connect();		 		
      	if (!$conn) {
      		echo 'Cannot connect to database!';
      		echo '<br/><br/>';
      	} 
      	}
      }
      ?>
      
		
		<?php
		
		// make sure user is log in and system connects to database
		if($UserName){
			if ($conn){
				
				// goto this block if user has input
				if (ctype_digit($_POST[new_id])){
					
					// check if sensor id exist in database
					$sql = "SELECT sensor_id FROM sensors WHERE sensor_id='$_POST[new_id]'";
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);
					$ide = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
					oci_free_statement($stid);
					
					// goto this block if id not exist
					if (!$ide) {
						
						// check if type is legal
						if (($_POST[new_type]=='a' || $_POST[new_type]=='i' || $_POST[new_type]=='s') && (ctype_digit($_POST[new_id]))) {
							
							// create sensor
							$sql = "INSERT INTO sensors VALUES ('$_POST[new_id]', '$_POST[new_loca]', '$_POST[new_type]', '$_POST[new_disc]')";
							$stid = oci_parse($conn, $sql);
			      		if (oci_execute($stid)){
			      			echo 'New Sensor is inserted!';
			      			oci_commit($conn);
			      		} else {
			      			oci_rollback($conn);
			      		}
			      		oci_free_statement($stid);
			      		
			      		$sidErr= "";
		      			$stypeErr= "";
		      		}
		      		
		      		// tell user if format of id is illegal
		      		if (($_POST[new_type]=='a' || $_POST[new_type]=='i' || $_POST[new_type]=='s') && (!ctype_digit($_POST[new_id]))){
		      			$sidErr= " Sensor ID must be numbers";
		      		}
		      		
		      		// tell user if format of type is illegal
		      		if ((!($_POST[new_type]=='a' || $_POST[new_type]=='i' || $_POST[new_type]=='s')) && (ctype_digit($_POST[new_id]))){
		      			$stypeErr= " Type must be in a, i or s!";
		      		}
		      		
		      		// tell user if both input are illegal
		      		if ((!($_POST[new_type]=='a' || $_POST[new_type]=='i' || $_POST[new_type]=='s')) && (!ctype_digit($_POST[new_id]))) {
		      			$sidErr= " Sensor ID must be numbers";
		      			$stypeErr= " Type must be in a, i or s!";
	      			} 
	      			
	      		// tell user if sensor already exist
	      		}else{
	      			echo 'Sensor ID already exist!';  			
	      		}
	   		} else {
	   			if (!ctype_digit($_POST[new_id])){
	   				$sidErr= " Sensor ID must be numbers";
	   			}
	   		}
   			// Free the statement identifier when closing the connection
	   		
	   		oci_close($conn);	
      	}
      }
      ?>
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
		<h2>Add new Sensor:</h2>
		Please enter new sensor id: <input type="text" name="new_id" value= "<?php echo $_POST[new_id];?>"/> 
		<span class="error"> * <?php echo $sidErr;?></span><br/>
		Please enter location: <input type="text" name="new_loca" value= "<?php echo $_POST[new_loca];?>"/>
		<span class="error"> * </span> <br/>
		Please enter sensor type: <input type="text" name="new_type" value= "<?php echo $_POST[new_type];?>"/>
		<span class="error"> * <?php echo $stypeErr;?></span> <br/>
		Please enter description: <input type="text" name="new_disc" value= "<?php echo $_POST[new_disc];?>"/>
		<span class="error"> * <br/>
		
     	<input type="submit" name="validate" value="Submit">
 	   </form>
	  	
	  	<form action="welcomeAdmin.html">
    	<input type="submit" value="Back to last level">
      </form>
	</body>
</html>				
				