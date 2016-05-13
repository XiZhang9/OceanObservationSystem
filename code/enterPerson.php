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
		// check if logged in
		if ($UserName== NULL){
			echo 'You are not login yet, you cannot change anything ';
			echo '<br/><br/>';
		} else{
			// check if role is 'a'
			if ($UserRole!='a'){
				echo 'You are not admin, you cannot enter new user';
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
		//Test id the user role is 'a'
		if($UserName&&$UserRole=='a'){
			if ($conn){
				
				//Check if all blanks are filled 
				if ($_POST[new_id] && $_POST[new_fn] && $_POST[new_ln] && $_POST[new_add] && $_POST[new_em] && $_POST[new_ph]){
					
					// check if person id is number
					if(is_numeric($_POST[new_id])) {
						
						// check if email is valid
						if(filter_var($_POST[new_em] , FILTER_VALIDATE_EMAIL)) {
							
							// check length of phone number
							if (strlen($_POST[new_ph])<=10) {
								//check if id or email is unique
								$sql = "SELECT * FROM persons WHERE person_id='$_POST[new_id]'";
								$stid = oci_parse($conn, $sql);
								oci_execute($stid);
								$pie = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);					
								oci_free_statement($stid);
								$sql2 = "SELECT * FROM persons WHERE email='$_POST[new_em]'";
								$stid2 = oci_parse($conn, $sql2);
								oci_execute($stid2);
								$ee = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS);					
								oci_free_statement($stid2);
								if ((!$pie) && (!$ee)) {
									// insert new person
									$sql = "INSERT INTO persons VALUES ('$_POST[new_id]' , '$_POST[new_fn]' , '$_POST[new_ln]' , '$_POST[new_add]' , '$_POST[new_em]' , '$_POST[new_ph]')";
									$stid = oci_parse($conn, $sql);
					      		if (oci_execute($stid)){
						      		oci_commit($conn);
					      		} else {
					      			oci_rollback($conn);
					      		}
					      		oci_free_statement($stid);
					      		echo 'New person is inserted!';
				      		}
				      		 else {
				      			echo 'Person id and email must be unique';
				   			} 
			   			} else {
			   				echo 'Length of phone number should be less than or equal to 10';
			   			}
			   			
		      		}else{
		      			echo 'You must have a valid email';
		      			
		      		}
		      	} else {
		      		echo 'Person id should be numbers';
		      	}
			}
			else {
				echo 'Remember you must fill all blanks';
   		}
		}
   			// Free the statement identifier when closing the connection
	   		
	oci_close($conn);	
	}
      ?>
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
		<h2>Add new person:</h2>
		Please enter new person id: <input type="text" name="new_id" value= "<?php echo $_POST[new_id];?>"/> <br/>
		Please enter first name: <input type="text" name="new_fn" value= "<?php echo $_POST[new_fn];?>"/> <br/>
		Please enter last name: <input type="text" name="new_ln" value= "<?php echo $_POST[new_ln];?>"/> <br/>
		Please enter address: <input type="text" name="new_add" value= "<?php echo $_POST[new_add];?>"/> <br/>
		Please enter email: <input type="text" name="new_em" value= "<?php echo $_POST[new_em];?>"/> <br/>
		Please enter phone: <input type="text" name="new_ph" value= "<?php echo $_POST[new_ph];?>"/> <br/>
		
   	<input type="submit" name="validate" value="Submit">
		</form>
		
		<form action="welcomeAdmin.html">
    	<input type="submit" value="Back to last level">
      </form>
	</body>
</html>	