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
	<body>
		<?php
		
		// make sure user is log in
		if ($UserName== NULL){
			echo 'You are not login yet, you cannot change anything ';
			echo '<br/><br/>';
		} else{
			
			// make sure user is an admin
			if ($UserRole!='a'){
				echo 'You are not admin, you cannot remove sensor';
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
      
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
		<h2>Delete Sensor:</h2>
		Please Enter Sensor ID: <input type="text" name="delete_id" value= "<?php echo $_POST[delete_id];?>"/> <br/>

   	<input type="submit" name="validate" value="Submit">
		</form>
		
		<form action="welcomeAdmin.html">
    	<input type="submit" value="Back to last level">
      </form>
		
		<?php
		
		// if system conncts to database
		if($UserName){
			if ($conn){
				
				// if user has done input 
				if ($_POST[delete_id]){
					
					// sensor id should be numbers
					if (ctype_digit($_POST[delete_id])){
						
						// if format of sensor id is legal, check if the sensor exists
						$sql = "SELECT sensor_id FROM sensors WHERE sensor_id='$_POST[delete_id]'";
						$stid = oci_parse($conn, $sql);
						oci_execute($stid);	
						
						// if sensor exists, delete it from table      				
	      			if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
							foreach ($row as $item) {
									$sql = "DELETE FROM subscriptions WHERE subscriptions.sensor_id = '$_POST[delete_id]'";
									$stid = oci_parse($conn, $sql);
									if (oci_execute($stid)){
						      		oci_commit($conn);
					      		} else {
					      			oci_rollback($conn);
					      		}
									$sql3 = "DELETE FROM audio_recordings WHERE audio_recordings.sensor_id = '$_POST[delete_id]'";
									$stid3 = oci_parse($conn, $sql3);
									if (oci_execute($stid3)){
						      		oci_commit($conn);
					      		} else {
					      			oci_rollback($conn);
					      		}
									$sql4 = "DELETE FROM images WHERE images.sensor_id = '$_POST[delete_id]'";
									$stid4 = oci_parse($conn, $sql4);
									if (oci_execute($stid4)){
						      		oci_commit($conn);
					      		} else {
					      			oci_rollback($conn);
					      		}
									$sql5 = "DELETE FROM scalar_data WHERE scalar_data.sensor_id = '$_POST[delete_id]'";
									$stid5 = oci_parse($conn, $sql5);
									if (oci_execute($stid5)){
						      		oci_commit($conn);
					      		} else {
					      			oci_rollback($conn);
					      		}
								   $sql2 = "DELETE FROM sensors WHERE sensors.sensor_id = '$_POST[delete_id]'";
									$stid2 = oci_parse($conn, $sql2);
									if (oci_execute($stid2)){
						      		oci_commit($conn);
					      		} else {
					      			oci_rollback($conn);
					      		}
									echo "Sensor is deleted";
							}
						} 
						
						// tell user that sensor does not exist in table
						else {
							echo "Sensor does not exist!";
						}
						
						// free statement
						oci_free_statement($stid);	
	   		 }
	   		 
	   		 // if id is not number, tell user to input again
	      	 else {
	      			echo "Sensor ID must be numbers";
	      	 }
	      	 
	      	 // close connection
				 oci_close($conn);	
      	}
   	  }
   }
      ?>
	</body>
</html>				
				