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
		//Test if the user is logged in an test if the user is a scientist.
		if ($UserName== NULL){
			echo 'You are not login yet, you cannot change anything ';
			echo '<br/><br/>';
		} else{
			if ($UserRole!='s'){
				echo 'You are not scientist, you cannot access search module';
			} else {
			//establish connection
      	$conn=connect();
      	// Test id successfully connected to database		 		
      	if (!$conn) {
      		echo 'Cannot connect to database!';
      		echo '<br/><br/>';
      	} 
      	}
      }
      ?>
      
		

<?php
			if ($conn){
				//Search by key
				if ($_POST[search_key]){
					echo 'Search by keywords result image ids';
					echo '<br/>';	
					//Get result image id
					$sql="SELECT images.image_id FROM sensors , subscriptions , images WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=sensors.sensor_id AND sensors.description LIKE '%".$_POST[search_key]."%' AND images.sensor_id=sensors.sensor_id";				
					$stid = oci_parse($conn, $sql);
      			$res = oci_execute($stid);
      			while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
      				foreach ($row as $item) {
		        			$info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
		      			echo $info;
		      		}
	      			echo '<br/>';					
		   		}
		   		echo 'Search by keywords result audio ids';
		   		echo '<br/>';	
		   		//get result audio id
		   		$sql2="SELECT audio_recordings.recording_id FROM sensors , subscriptions , audio_recordings WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=sensors.sensor_id AND sensors.description LIKE '%".$_POST[search_key]."%' AND audio_recordings.sensor_id=sensors.sensor_id";				
					$stid2 = oci_parse($conn, $sql2);
	   			$res2 = oci_execute($stid2);
	   			while($row2 = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS)){
	   				foreach ($row2 as $item2) {
			     			$info2 = ($item2 !== null ? htmlentities($item2, ENT_QUOTES) : "&nbsp;");
			   			echo $info2;
			   		}
	   			echo '<br/>';					
		   		}
	      	}
	   	}
      	
      	?>
      	
      	<?php
			if ($conn){
				// Search by location
				if ($_POST[search_l]){
					echo 'Search by Location result image ids';
					echo '<br/>';	
					//Get result image id 
					$sql="SELECT images.image_id FROM sensors , subscriptions , images WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=sensors.sensor_id AND sensors.location  LIKE '%".$_POST[search_l]."%' AND images.sensor_id=sensors.sensor_id";				
					$stid = oci_parse($conn, $sql);
      			$res = oci_execute($stid);
      			while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
      				foreach ($row as $item) {
		        			$info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
		      			echo $info;
		      		}
      			echo '<br/>';					
		   		}
		   		echo 'Search by Location result audio ids';
		   		echo '<br/>';	
		   		// get result audio id
		   		$sql2="SELECT audio_recordings.recording_id FROM sensors , subscriptions , audio_recordings WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=sensors.sensor_id AND sensors.location  LIKE '%".$_POST[search_l]."%' AND audio_recordings.sensor_id=sensors.sensor_id";				
					$stid2 = oci_parse($conn, $sql2);
	   			$res2 = oci_execute($stid2);
	   			while($row2 = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS)){
	   				foreach ($row2 as $item2) {
			     			$info2 = ($item2 !== null ? htmlentities($item2, ENT_QUOTES) : "&nbsp;");
			   			echo $info2;
			   		}
	   			echo '<br/>';					
		   		}
	      	}
      	}
      	
      	?>
      	<?php
      	// Search by time range
			if ($conn){
				if ($_POST[search_time1]){
					if ($_POST[search_time2]){
						// if only search by time range
					  if ((!$_POST[o_name]) && (!$_POST[search_t])){
							echo 'Search by Time Range result image ids';
							echo '<br/>';	
							//get result image id 
							$sql="SELECT images.image_id FROM subscriptions , images WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=images.sensor_id AND images.date_created>=TO_DATE('$_POST[search_time1] 00:00:00', 'DD/MM/YYYY HH24:MI:SS') AND  images.date_created<=TO_DATE('$_POST[search_time2] 23:59:59', 'DD/MM/YYYY HH24:MI:SS')";				
							$stid = oci_parse($conn, $sql);
		      			$res = oci_execute($stid);
		      			while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
		      				foreach ($row as $item) {
				        			$info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
				      			echo $info;
				      		}
				      			echo '<br/>';					
				   		}
				   		echo 'Search by Time Range result audio ids';
				   		echo '<br/>';	
				   		// get result audio id
				   		$sql2="SELECT audio_recordings.recording_id FROM subscriptions , audio_recordings WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=audio_recordings.sensor_id AND audio_recordings.date_created>=TO_DATE('$_POST[search_time1] 00:00:00', 'DD/MM/YYYY HH24:MI:SS') AND  audio_recordings.date_created<=TO_DATE('$_POST[search_time2] 23:59:59', 'DD/MM/YYYY HH24:MI:SS') ";				
							$stid2 = oci_parse($conn, $sql2);
			   			$res2 = oci_execute($stid2);
			   			while($row2 = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS)){
			   				foreach ($row2 as $item2) {
					     			$info2 = ($item2 !== null ? htmlentities($item2, ENT_QUOTES) : "&nbsp;");
					   			echo $info2;
					   		}
				   			echo '<br/>';					
				   		}
				   		echo 'Search by Time Range result scalar_data ids';
				   		echo '<br/>';	
			   		//get result scalar data id
				   		$sql3="SELECT scalar_data.id FROM subscriptions , scalar_data WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=scalar_data.sensor_id AND scalar_data.date_created>=TO_DATE('$_POST[search_time1] 00:00:00', 'DD/MM/YYYY HH24:MI:SS') AND  scalar_data.date_created<=TO_DATE('$_POST[search_time2] 23:59:59', 'DD/MM/YYYY HH24:MI:SS') ";				
							$stid3 = oci_parse($conn, $sql3);
			   			$res3 = oci_execute($stid3);
			   			while($row3 = oci_fetch_array($stid3, OCI_ASSOC+OCI_RETURN_NULLS)){
			   				foreach ($row3 as $item3) {
					     			$info3 = ($item3 !== null ? htmlentities($item3, ENT_QUOTES) : "&nbsp;");
					   			echo $info3;
					   		}
			   			echo '<br/>';					
				   		}
			   		}
		   		if ($_POST[o_name]){
			   	   $filename=$_POST[o_name];
			   	   // Output a csv file 
			   	   $sql4="SELECT scalar_data.* FROM subscriptions , scalar_data WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=scalar_data.sensor_id AND scalar_data.date_created>=TO_DATE('$_POST[search_time1] 00:00:00', 'DD/MM/YYYY HH24:MI:SS') AND  scalar_data.date_created<=TO_DATE('$_POST[search_time2] 23:59:59', 'DD/MM/YYYY HH24:MI:SS') ";				
						$stid4 = oci_parse($conn, $sql4);
		   			$res4 = oci_execute($stid4);
		
						$file = fopen(dirname(__FILE__)."/thumbnail/'$filename'.csv","w");
						$stack=array();
			   		while($row4 = oci_fetch_array($stid4, OCI_ASSOC+OCI_RETURN_NULLS)){
		   				foreach ($row4 as $item4) {
				     			$info4 = ($item4 !== null ? htmlentities($item4, ENT_QUOTES) : "&nbsp;");
				     			array_push($stack, $info4);
		     			
		   			}
			   			fputcsv($file,$stack);
			   		   $stack=array();
			   		}
		   		   fclose($file);
			   		echo 'output csv file successed';
			   		echo '<br/>';			
			   		}
					if ($_POST[search_t]){
						//Search by time range and type
						if (($_POST[search_t]=='a' || $_POST[search_t]=='i' || $_POST[search_t]=='s')){
							echo 'Search by Type result image ids';
							echo '<br/>';	
							// Output image id 
							$sql="SELECT images.image_id FROM sensors , subscriptions , images WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=sensors.sensor_id AND sensors.sensor_type='$_POST[search_t]' AND images.sensor_id=sensors.sensor_id AND images.date_created>=TO_DATE('$_POST[search_time1] 00:00:00', 'DD/MM/YYYY HH24:MI:SS') AND  images.date_created<=TO_DATE('$_POST[search_time2] 23:59:59', 'DD/MM/YYYY HH24:MI:SS')";				
							$stid = oci_parse($conn, $sql);
		      			$res = oci_execute($stid);
		      			while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
		      				foreach ($row as $item) {
				        			$info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
				      			echo $info;
				      		}
			      			echo '<br/>';					
				   		}
				   		echo 'Search by Type result audio ids';
				   		echo '<br/>';	
				   		// Result audio id
				   		$sql2="SELECT audio_recordings.recording_id FROM sensors , subscriptions , audio_recordings WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=sensors.sensor_id AND sensors.sensor_type='$_POST[search_t]' AND audio_recordings.sensor_id=sensors.sensor_id AND audio_recordings.date_created>=TO_DATE('$_POST[search_time1] 00:00:00', 'DD/MM/YYYY HH24:MI:SS') AND  audio_recordings.date_created<=TO_DATE('$_POST[search_time2] 23:59:59', 'DD/MM/YYYY HH24:MI:SS') ";				
							$stid2 = oci_parse($conn, $sql2);
			   			$res2 = oci_execute($stid2);
			   			while($row2 = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS)){
			   				foreach ($row2 as $item2) {
					     			$info2 = ($item2 !== null ? htmlentities($item2, ENT_QUOTES) : "&nbsp;");
					   			echo $info2;
					   		}
				   			echo '<br/>';					
				   		}
				   		echo 'Search by Time Range result scalar_data ids';
				   		echo '<br/>';	
				   		//Result scalar data id 
				   		$sql3="SELECT scalar_data.id FROM sensors, subscriptions , scalar_data WHERE subscriptions.person_id='$PersonID' AND subscriptions.sensor_id=scalar_data.sensor_id AND subscriptions.sensor_id=sensors.sensor_id AND sensors.sensor_type='$_POST[search_t]' AND scalar_data.date_created>=TO_DATE('$_POST[search_time1] 00:00:00', 'DD/MM/YYYY HH24:MI:SS') AND  scalar_data.date_created<=TO_DATE('$_POST[search_time2] 23:59:59', 'DD/MM/YYYY HH24:MI:SS') ";				
							$stid3 = oci_parse($conn, $sql3);
			   			$res3 = oci_execute($stid3);
			   			while($row3 = oci_fetch_array($stid3, OCI_ASSOC+OCI_RETURN_NULLS)){
			   				foreach ($row3 as $item3) {
					     			$info3 = ($item3 !== null ? htmlentities($item3, ENT_QUOTES) : "&nbsp;");
					   			echo $info3;
					   		}
				   			echo '<br/>';					
				   		}
				   		
		      	}
		      	else{
		      		// test if input valid
		      	$tErr='* Sensor type must in a, i, s';
		      	}
	      	} 
      	}
   	}
	}
	
      	?>
				
			<?php
			if ($conn){
				//download image
					if ($_POST[d_imageid]){
					$sql="SELECT images.recoreded_data  FROM images WHERE images.image_id='$_POST[d_imageid]'";				
					$stid = oci_parse($conn, $sql);
      			$res = oci_execute($stid);
      			while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
      				foreach ($row as $item) {
	     					$bytes =$item;
	     					// following code based on http://stackoverflow.com/questions/3608322/php-cannot-download-more-than-1mb-of-oracle-blob-data-download-truncated-after
	      				ini_set("memory_limit", "200M");  
							set_time_limit(0);
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Cache-Control: public");
							header("Content-Description: File Transfer");
							header("Content-Type: image_type_to_mime_type(image/jpeg)");
							header("Content-Disposition: attachment; filename='$_POST[d_imageid]'.jpg");
							header("Content-Transfer-Encoding: binary");
							$content=$bytes->load();
							echo $content;
							

      				}
		      	}					
	   		}
      	}
      	?>
      	<?php
      	if ($conn){
      		//download audio
					if ($_POST[d_audioid]){
					$sql="SELECT audio_recordings.recorded_data  FROM audio_recordings WHERE audio_recordings.recording_id='$_POST[d_audioid]'";				
					$stid = oci_parse($conn, $sql);
      			$res = oci_execute($stid);
      			while($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
      				foreach ($row as $item) {
	     					$bytes =$item;
	      				ini_set("memory_limit", "200M");  
							set_time_limit(0);
							// following code based on http://stackoverflow.com/questions/3608322/php-cannot-download-more-than-1mb-of-oracle-blob-data-download-truncated-after
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Cache-Control: public");
							header("Content-Description: File Transfer");
							header("Content-Type: audio/x-wav");
							header("Content-Disposition: attachment; filename='$_POST[d_audioid]'.wav");
							header("Content-Transfer-Encoding: binary");
							$content=$bytes->load();
							echo $content;
							

      				}
		      	}						
	   		}
      	}

     ?>
				
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
      <h2>Search by keywords</h2>
      Enter keyword  <input type="text" name="search_key"/>
      <input type="submit" name="validate" value="Submit">
      <br/>
      <h2>Search by Sensor Location</h2>
      Enter Location <input type="text" name="search_l"/>
      <input type="submit" name="validate" value="Submit">
      <h2>Search by Time Range</h2>
      Enter Time Range <input type="text" name="search_time1" value= "<?php echo $_POST[search_time1];?>"/>
      TO 
      <input type="text" name="search_time2" value= "<?php echo $_POST[search_time2];?>"/>
      <input type="submit" name="validate" value="Submit">
      <span class="error"> <?php echo 'Please in DD/MM/YYYY format';?></span> 
       <h2>Search by Time Range and Sensor Type</h2>
      Enter type: <input type="text" name="search_t" value= "<?php echo $_POST[search_t];?>"/>
      <input type="submit" name="validate" value="Submit">
       <span class="error"> <?php echo $tErr;?></span> <br/>
      <br/>
      <h2>Download</h2>
      Image Id you want to download  <input type="text" name="d_imageid" value= "<?php echo $_POST[d_imageid];?>"/>
      <input type="submit" name="validate" value="Submit">
      <br/>
      Audio Id you want to download  <input type="text" name="d_audioid" value= "<?php echo $_POST[d_audioid];?>"/>
      <input type="submit" name="validate" value="Submit">
      <br/>
      Create a name for output csv file you want to download  <input type="text" name="o_name" value= "<?php echo $_POST[o_name];?>"/>
      <input type="submit" name="validate" value="Submit">
      <br/>
		</form>	
		
		<form action="welcomeSci.html">
    	<input type="submit" value="Back to last level">
      </form>

	</body>
</html>
		
