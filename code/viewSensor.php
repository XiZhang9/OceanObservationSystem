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
		
		// make sure user is data curator
		} else{
			if ($UserRole!='s'){
				echo 'You are not scientist, you cannot access subscribe module';
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
      <h2>All Sensors' Information</h2>
      <br/>
      <table style="width:50%" border="1">
	     	<tr>
				<td> Sensor ID </td>
				<td> Location </td>
				<td> Type </td>
				<td> description </td>
      	</tr>
		</form>

		<?php

		// make sure the system connects to database
		if ($conn) {
			
			// get all sensors from database
			$sql = "SELECT * FROM sensors ";
      	$stid = oci_parse($conn, $sql);
      	$res = oci_execute($stid);    	
      	while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
      		?>
      		<tr>
      		<?php
      		foreach ($row as $item) {
        			$info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");  			
        			?>
        			<td> <?php echo $info; ?></td>

      			<?php
      		}
      		?>
      		</tr>
      		<?php
      	}
      	
     		// Free the statement identifier
	   	oci_free_statement($stid); 
	   }
		?>
		
		</table>		
		<br/><br/>
		
		<?php

		// make sure the system connects to database
		if ($conn){
			
			// check format of id, which should be numbers			
			if (ctype_digit($_POST[add_id])){
				
				// check if this sensor exists in database
				$sql = "SELECT sensor_id FROM sensors WHERE sensor_id='$_POST[add_id]'";
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);
				$ide = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
				oci_free_statement($stid);
				
				// check if the sensor belongs to this user
				$sql = "SELECT sensor_id FROM subscriptions WHERE sensor_id='$_POST[add_id]' AND person_id='$PersonID'";
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);
				$sside = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
				oci_free_statement($stid);
				
				// goto this block if the sensor belongs to user and user has not subscribed this sensor
				if ($ide && (!$sside)) {
					
					// insert new subscription
					$sql = "INSERT INTO subscriptions VALUES ('$_POST[add_id]', '$PersonID')";
					$stid = oci_parse($conn, $sql);
					if (oci_execute($stid)){
		      		oci_commit($conn);
		      		oci_free_statement($stid);
	      		} else {
	      			oci_rollback($conn);
	      		}
	      		
	      		$asisErr='New subscriptions is inserted!';
	      		$asidErr= "";
      		}
		      
		      // if sensor does not exist, tell user
		      elseif(!$ide) {
		      	$asidErr='Sensor ID dose not exist'; 
		      }
		      
		      // if user has subscripted the sensor, tell user
		      elseif($sside) {
		      	$asidErr='Sensor ID already subscripted'; 
      		}
      	}
   		elseif ($_POST[add_id]) {
   			
   			// if the format of id is illegal, tell user to input again
   			if (!ctype_digit($_POST[add_id])){
	   			$asidErr='Sensor ID must be numbers';  			
   			}
   		}
			// Free the statement identifier when closing the connection
   		

   	}
      ?>
		<?php
		
		// make sure system connects to the database
		if ($conn){
			
			// check format of id, which should be numbers
			if (ctype_digit($_POST[delete_id])){
				
				// check if user has subscribed the sensor
				$sql = "SELECT sensor_id FROM subscriptions WHERE sensor_id='$_POST[delete_id]' AND person_id='$PersonID'";
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);
				$dside = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
				oci_free_statement($stid);
				
				// goto this block if the user has subscribed the sensor
				if ($dside) {
					
					// delete this subscription
					$sql = "DELETE FROM subscriptions WHERE person_id='$PersonID' AND sensor_id='$_POST[delete_id]'";
					$stid = oci_parse($conn, $sql);
	      		if (oci_execute($stid)){
		      		oci_commit($conn);
	      		} else {
	      			oci_rollback($conn);
	      		}
	      		oci_free_statement($stid);
	      		$dsis='Subscriptions deleted!';
	      		$dsidErr= "";
      		}
		      
		      // tell user if user has not subscripted the sensor
		      else {
		      	$dsidErr='You did not subscripted this sensor id'; 
		      }
      	}
   		elseif ($_POST[delete_id]) {
   			
   			// tell user if the format of id is illegal
   			if (!ctype_digit($_POST[delete_id])){
	   			$dsidErr='Sensor ID must be numbers';  			
   			}
   		}
   	}
      ?>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
      <h2>Sensors you have Subscripted To</h2>
      <br/>
		</form>
		
		<table style="width:50%" border="1">
	     	<tr>
				<td> Sensor ID </td>
				<td> Location </td>
				<td> Type </td>
				<td> description </td>
      	</tr>
      	
		<?php
		// make sure system connects to database
		if ($conn) {
			
			// get all information of the sensor and show on screen
			$sql = "SELECT s.sensor_id, s.location, s.sensor_type, s.description FROM sensors s, subscriptions su WHERE su.person_id='$PersonID' AND s.sensor_id=su.sensor_id";
      	$stid = oci_parse($conn, $sql);
      	$res = oci_execute($stid);    	
      	while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
      		?>
      		<tr>
      		<?php
      		foreach ($row as $item) {
        			$info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
      			?>
      			<td> <?php echo $info; ?></td>
      			<?php
      		}
      		?>
      		</tr>
      		<?php
      	}
      	
     		// Free the statement identifier when closing the connection
	   	oci_free_statement($stid); 
	   	oci_close($conn);	
	   }
		?>
		</table>
		<br/><br/>

		

		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
      <h2>Add Subscriptions to Sensors.</h2>
      Sensor ID:  <input type="text" name="add_id"/>
		 <span class="error"> <?php echo $asidErr;?></span> <br/>
      <br/>
      <span> <?php echo $asisErr;?></span> <br/>
      <input type="submit" name="validate" value="Submit">
		</form>

		<br/><br/>
		
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
      <h2>Delete Subscriptions to Sensors.</h2>
      Sensor ID:  <input type="text" name="delete_id"/>
		<span class="error"> <?php echo $dsidErr;?></span> <br/>
      <br/>
      <span> <?php echo $dsis;?></span> <br/>
      <input type="submit" name="validate" value="Submit">
		</form>
		
		<form action="welcomeSci.html">
    	<input type="submit" value="Back to last level">
      </form>
	</body>
</html>
		
