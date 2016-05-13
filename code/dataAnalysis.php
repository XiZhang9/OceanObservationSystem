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
		     		
		<h3>	Go Back To Last Level: </h3>
		<form action="welcomeSci.html">
    		<input type="submit" value="Back to last level">
      </form> 
      
      <br/>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
      <h2>OLAP Report Of Sensors You Have Subscripted To</h2>
      <br/>
		</form>

      
	<body>
		<?php
		
		// make sure user is log in
		if ($UserName== NULL){
			echo 'You are not login yet, you cannot change anything ';
			echo '<br/><br/>';
		} else{
			
			// make sure user is scientist
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

		<?php
		// make sure system connects to database
		if ($conn) {
	   	
	   	// selecting data from database, using cube
	   	$sql = "select s.sensor_id, s.location, d.date_created, avg(d.value), min(d.value), max(d.value) from scalar_data d, sensors s, subscriptions sub where sub.person_id = $PersonID and s.sensor_id = sub.sensor_id and s.sensor_type = 's' and d.sensor_id = s.sensor_id group by cube (s.sensor_id, s.location, d.date_created)";
	   	$stid = oci_parse($conn, $sql);
      	$res = oci_execute($stid); 
      	
      	// show result in a table
      ?>
         <table style="width:50%" border="1">
         	<tr>
         		<td> Sensor ID </td>
         		<td> Location </td>
         		<td> Created Date </td>
         		<td> Average Data </td>
         		<td> Minimum Data </td>
         		<td> Maximum Data </td>
         	</tr>
      	<?php while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
      		<tr>
      			<?php foreach ($row as $item) { ?>
	  				<td> <?php $info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
      						  echo $info; ?>
      			</td>
      			<?php } ?>
	  			</tr>
	  		<?php } ?>
			</table> 


		<br/>
		<h3>	Data Analysis: </h3>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			Enter the level you want to generalize data (Y as year, Q as quarter, M as month, W as week, D as day): <input type="input" name="level"  >
			<input type="submit" value="Submit">
		</form>		
		

      <?php
      
      // make sure system connects to database
      if ($conn){
      	
      	// if user has done input 
      	if (isset($_POST[level])){
      		
      		// get the level user want to see
      		$level = $_POST[level];
      		
      		// if view exist, drop it
      		try {
      			$sql = "drop view temp";
      			$stid = oci_parse($conn, $sql);  
      			if (oci_execute($stid)) {
      				oci_commit($conn);
	      		} else {
	      			oci_rollback($conn);
	      		}		
      		} catch(Exception $e) {
      		}

      		// create view
      		$sql = "create view temp as select s.sensor_id sensor_id, s.location location, d.value val, to_char(d.date_created,'YYYY') year, to_char(d.date_created,'Q') quarter, to_char(d.date_created,'MM') month, to_char(d.date_created,'IW') week, to_char(d.date_created,'ddd') day from scalar_data d, sensors s, subscriptions sub where sub.person_id = $PersonID and s.sensor_id = sub.sensor_id and s.sensor_type = 's' and d.sensor_id = s.sensor_id";
      		$stid = oci_parse($conn, $sql);
      		if(oci_execute($stid)){
      			oci_commit($conn);
			   } else {
			      oci_rollback($conn);
			   }
      		
      		// in year level
      		if ($level=='Y') {
      			
      			// select information for each year and show result in a table
      			$sql = "select sensor_id, location, year, avg(val), min(val), max(val) from temp group by rollup (sensor_id, location, year)";
      			$stid = oci_parse($conn, $sql);
					if (oci_execute($stid)) {
						?>
			         <table style="width:50%" border="1">
			         	<tr>
			         		<td> Sensor ID </td>
			         		<td> Location </td>
			         		<td> Year </td>
			         		<td> Average Data </td>
			         		<td> Minimum Data </td>
			         		<td> Maximum Data </td>
			         	</tr>	
						<?php while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
			      		<tr>
			      			<?php foreach ($row as $item) { ?>
				  				<td> <?php $info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
			      						  echo $info; ?>
			      			</td>
			      			<?php } ?>
				  			</tr>
				  		<?php } ?>
						</table> 
											
						<?php
					}
					
				// in quarter level
      		} elseif($level=='Q') {
      			
      			// select information for each quarter in each year and show result in a table
      			$sql = "select sensor_id, location, year, quarter, avg(val), min(val), max(val) from temp group by rollup (sensor_id, location, year, quarter)";
      			$stid = oci_parse($conn, $sql);
					if (oci_execute($stid)) {
						?>
			         <table style="width:50%" border="1">
			         	<tr>
			         		<td> Sensor ID </td>
			         		<td> Location </td>
			         		<td> Year </td>
								<td> Quarter </td>
			         		<td> Average Data </td>
			         		<td> Minimum Data </td>
			         		<td> Maximum Data </td>
			         	</tr>	
						<?php while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
			      		<tr>
			      			<?php foreach ($row as $item) { ?>
				  				<td> <?php $info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
			      						  echo $info; ?>
			      			</td>
			      			<?php } ?>
				  			</tr>
				  		<?php } ?>
						</table> 
											
						<?php
					}      		
				
				// in month level	
      		} elseif($level=='M') {
      			
      			// select information for each month in each year and show result in a table
      			$sql = "select sensor_id, location, year, month, avg(val), min(val), max(val) from temp group by rollup (sensor_id, location, year, month)";
      			$stid = oci_parse($conn, $sql);
					if (oci_execute($stid)) {
						?>
			         <table style="width:50%" border="1">
			         	<tr>
			         		<td> Sensor ID </td>
			         		<td> Location </td>
			         		<td> Year </td>
								<td> Month </td>
			         		<td> Average Data </td>
			         		<td> Minimum Data </td>
			         		<td> Maximum Data </td>
			         	</tr>	
						<?php while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
			      		<tr>
			      			<?php foreach ($row as $item) { ?>
				  				<td> <?php $info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
			      						  echo $info; ?>
			      			</td>
			      			<?php } ?>
				  			</tr>
				  		<?php } ?>
						</table> 
											
						<?php
					}  
					
				// in week level
      		} elseif($level=='W') {
      			
      			// select information for each week in each year and show result in a table
      			$sql = "select sensor_id, location, year, week, avg(val), min(val), max(val) from temp group by rollup (sensor_id, location, year, week)";
      			$stid = oci_parse($conn, $sql);
					if (oci_execute($stid)) {
						?>
			         <table style="width:50%" border="1">
			         	<tr>
			         		<td> Sensor ID </td>
			         		<td> Location </td>
			         		<td> Year </td>
								<td> Week </td>
			         		<td> Average Data </td>
			         		<td> Minimum Data </td>
			         		<td> Maximum Data </td>
			         	</tr>	
						<?php while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
			      		<tr>
			      			<?php foreach ($row as $item) { ?>
				  				<td> <?php $info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
			      						  echo $info; ?>
			      			</td>
			      			<?php } ?>
				  			</tr>
				  		<?php } ?>
						</table> 
											
						<?php
					} 
				
				// in date level
      		} elseif($level=='D') {
      			
      			// select information for each day in each year and show result in a table
      			$sql = "select sensor_id, location, year, day, avg(val), min(val), max(val) from temp group by rollup (sensor_id, location, year, day)";
      			$stid = oci_parse($conn, $sql);
					if (oci_execute($stid)) {
						?>
			         <table style="width:50%" border="1">
			         	<tr>
			         		<td> Sensor ID </td>
			         		<td> Location </td>
			         		<td> Year </td>
								<td> Day </td>
			         		<td> Average Data </td>
			         		<td> Minimum Data </td>
			         		<td> Maximum Data </td>
			         	</tr>	
						<?php while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){ ?>
			      		<tr>
			      			<?php foreach ($row as $item) { ?>
				  				<td> <?php $info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
			      						  echo $info; ?>
			      			</td>
			      			<?php } ?>
				  			</tr>
				  		<?php } ?>
						</table> 
											
						<?php
					} 
      		}
      	}
      }
      ?>
      
      
      <?php 
      	// close connection
	   	oci_close($conn);	
	   }
		?>

	</body>
</html>
      