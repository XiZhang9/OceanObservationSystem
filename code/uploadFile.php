<?php
// start session
session_start();
// get session data
$pidLogin = $_SESSION['PersonID'];
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
			
		// make sure user is data curator
		} else{
			if ($UserRole!='d'){
				echo 'You are not data curator, you cannot upload file';
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
	    	<h2>Select file to upload:</h2>
	    	<input type="file" name="lob_upload">    	
	    	
			<h2>Please enter information about the file:</h2>
			Sensor ID: 
			<input type="text" name="sensorId" value= "<?php echo $_POST[sensorId];?>"/>
			<br/>		
			
			Date:
			<input type="text" name="date_date" value= "<?php if ($_POST[date_date]){echo $_POST[date_date];}else{echo "DD";}?>"/>
			<input type="text" name="date_month" value="<?php if ($_POST[date_month]){echo $_POST[date_month];}else{echo "MM";}?>"/>	
			<input type="text" name="date_year" value="<?php if ($_POST[date_year]){echo $_POST[date_year];}else{echo "YYYY";}?>"/>	
			<br/>		
			
			Description:
			<input type="text" name="description" value= "<?php echo $_POST[description];?>"/>
			<br/>	<br/><br/>	
			
			For audio files, please input length of the file:
			<input type="text" name="length" value= "<?php echo $_POST[length];?>"/>
			<br/><br/>		
			
			Click here to upload file:
			<input type="submit" value="Upload File">

		</form>
	
	   <form action="welcome.html">
    		<input type="submit" value="Back to last level">
      </form>
	
	
		<?php
		
		// make sure system connects to database
		if($conn) {
			
			// make sure user has chosen file to upload
			if(isset($_POST['lob_upload'])){
				
				// check file extension
				
				// go to this block if it is image file
				if (strpos($_POST['lob_upload'],'jpg')){
					
					// check the type of sensor
					$sql = "SELECT sensor_type FROM sensors WHERE sensor_id = $_POST[sensorId]";
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);
					while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
						foreach ($row as $item) {
							$type = $item;
						}
					}
					oci_free_statement($stid);
					
					// the sensor should be an image recorder
					if ($type=='i'){
						
						// get new id of image
						$sql = "SELECT image_id FROM images";
						$stid = oci_parse($conn, $sql);
					   oci_execute($stid);
					   $count1 = 1;
					   while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
							foreach ($row as $item) {
								$count1 = $count1+1;
							}
						}
						oci_free_statement($stid);
						
						// get file name of uploading file
						$up_file = $_POST['lob_upload'];
						$size = filesize($up_file);

						// get complete date
						$date_created = $_POST[date_date].'/'.$_POST[date_month].'/'.$_POST[date_year];
						
						// open file 
						$fp = fopen($up_file, "r");
						
						// change format of file
			      	$data = addslashes(fread($fp, filesize($up_file)));
			      	
			      	// make thumbnile of new size
					 	$src = imagecreatefromjpeg($up_file);
	
	  				   list($width,$height)=getimagesize($up_file);
					
						$newwidth=60;
	               $newheight=($height/$width)*$newwidth;
	               $des=imagecreatetruecolor($newwidth,$newheight);
	
	               imagecopyresampled($des,$src,0,0,0,0,$newwidth,$newheight, $width,$height);
	               
	               $tnfilename = '/compsci/webdocs/han8/web_docs/thumbnail/'.$up_file;
	               imagejpeg($des, $tnfilename);               
	               
	               $tnp = fopen($tnfilename, "r");
	               $tndata = addslashes(fread($tnp, filesize($tnfilename)));
	
						// close file   
			      	fclose($fp);   
			      	fclose($tnp);   
			      	
			      	// upload new image record
			      	$sql = "INSERT INTO images VALUES($count1, $_POST[sensorId], TO_DATE('$date_created', 'DD/MM/YYYY'), '$_POST[description]', EMPTY_BLOB(), EMPTY_BLOB()) RETURNING thumbnail, recoreded_data  INTO :tn, :rdata";
						$stmt = oci_parse($conn, $sql);
						$tnlob = oci_new_descriptor($conn, OCI_D_LOB);
						$newlob = oci_new_descriptor($conn, OCI_D_LOB);
						oci_bind_by_name($stmt, ":tn", $tnlob, -1, OCI_B_BLOB);
						oci_bind_by_name($stmt, ":rdata", $newlob, -1, OCI_B_BLOB);
						if (oci_execute($stmt,OCI_DEFAULT)){
							$tnlob->save(base64_decode($tndata));
							$newlob->save(base64_decode($data));
							oci_commit($conn);
							echo 'New Image Uploaded';
						} else{
							oci_rollback($conn);
						}
						
						// cleanup
						$tnlob->free();
						$newlob->free();
						// free statement
						oci_free_statement($stmt);  	
					
					// if sensor is not image recorder, tell user to change another sensor
					} else {
						echo 'Cannot upload to this sensor, because it is not image recorder';
					}
					
				// go to this block if it is audio file
				} elseif(strpos($_POST['lob_upload'],'wav')) {
					
					// check the type of sensor				
					$sql = "SELECT sensor_type FROM sensors WHERE sensor_id = $_POST[sensorId]";
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);
					while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
						foreach ($row as $item) {
							$type = $item;
						}
					}
					oci_free_statement($stid);
					
					// the sensor should be an audio recorder
					if ($type=='a'){
						
						// get new id of audio record
						$sql = "SELECT recording_id FROM audio_recordings";
						$stid = oci_parse($conn, $sql);
					   oci_execute($stid);
					   $count2 = 1;
					   while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
							foreach ($row as $item) {
								$count2 = $count2+1;
							}
						}
						oci_free_statement($stid);
						
						// get file name
						$up_file = $_POST['lob_upload'];
						// get file size
						$size = filesize($up_file);
						
						// get complete date
						$date_created = $_POST[date_date].'/'.$_POST[date_month].'/'.$_POST[date_year];
						
						// open file
						$fp = fopen($up_file, "r");
						
						// change file format
			      	$data = addslashes(fread($fp, filesize($up_file)));
			      	
			      	// close file
						fclose($fp);  
						
						// upload new audio record
						$sql = "INSERT INTO audio_recordings VALUES($count2, $_POST[sensorId], TO_DATE('$date_created', 'DD/MM/YYYY'), $_POST[length], '$_POST[description]', EMPTY_BLOB()) RETURNING recorded_data  INTO :rdata";
						$stmt = oci_parse($conn, $sql);
						$newlob = oci_new_descriptor($conn, OCI_D_LOB);
						oci_bind_by_name($stmt, ":rdata", $newlob, -1, OCI_B_BLOB);
						if (oci_execute($stmt,OCI_DEFAULT)){
							$newlob->save(base64_decode($data));
							oci_commit($conn);
							echo 'New Audio Uploaded';
						} else{
							oci_rollback($conn);
						}
						
						// cleanup
						$newlob->free();
						// free statement
						oci_free_statement($stmt);  
					
					// if sensor is not an audio recorder, tell user to choose another one
					} else {
						echo 'Cannot upload to this sensor, because it is not audio recorder';
					}
		      	
		      // go to this block if it is a scalar data file
				} elseif (strpos($_POST['lob_upload'],'csv')) {
					
					// check the type of sensor
					$sql = "SELECT sensor_type FROM sensors WHERE sensor_id = $_POST[sensorId]";
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);
					while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
						foreach ($row as $item) {
							$type = $item;
						}
					}
					oci_free_statement($stid);
					
					// the sensor should be a data recorder
					if ($type=='s'){					
						$up_file = $_POST['lob_upload'];
						$file = fopen($up_file,"r");
						while (!feof($file)){
							$line = fgetcsv($file);
							
							$vid = intval($line[0]);						
							$sensorId = $_POST[sensorId];
							$dateCreated = $line[1];
							$value = floatval($line[2]);
							if ($dateCreated){
								$sql = "INSERT INTO scalar_data VALUES ($vid, $sensorId, TO_DATE('$dateCreated', 'DD/MM/YYYY HH24:MI:SS'), $value)";
								$stid = oci_parse($conn, $sql);
					   		if (oci_execute($stid)){
						   		oci_free_statement($stid); 
						   		oci_commit($conn); 
						   	} else{
						   		oci_rollback($conn);
						   	}
							}
						}
						echo 'New Scalar File Uploaded.';
						fclose($file);
					
					// if sensor is not a data recorder, tell user to choose another one
					} else {
						echo 'Cannot upload to this sensor, because it is not scalar value recorder';
					}
				}
				
			}
			
			// close connection
			oci_close($conn);		
		}
		
		?>


	</body>
</html> 

