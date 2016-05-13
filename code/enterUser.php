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
		
		// make sure user is log in and user is admin
		if ($UserName== NULL){
			echo 'You are not login yet, you cannot change anything ';
			echo '<br/><br/>';
		} else{

			// make sure user is scientist
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
      
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
		<h2>Add new user:</h2>
		Please enter new user name: <input type="text" name="new_un" value= "<?php echo $_POST[new_un];?>"/> <br/>
		Please enter password: <input type="text" name="new_pw" value= "<?php echo $_POST[new_pw];?>"/> <br/>
		Please enter role: <input type="text" name="new_role" value= "<?php echo $_POST[new_role];?>"/> <br/>
		Please enter person ID: <input type="text" name="new_pid" value= "<?php echo $_POST[new_pid];?>"/> <br/>
		
   	<input type="submit" name="validate" value="Submit">
		</form>
		
		<form action="welcomeAdmin.html">
    	<input type="submit" value="Back to last level">
      </form>
		
		<?php
		
		// make sure user is admin and system connects to database
		if($UserName&&$UserRole=='a'){
			if ($conn){
				
				// if user has done input
				if ($_POST[new_un]){
					
					// format of date
					$date = date("Y/m/d");
					
					// check if user name exists
					$sql = "SELECT user_name FROM users WHERE user_name='$_POST[new_un]'";
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);
					$une = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
					oci_free_statement($stid);
					
					// check if this person exists
					$sql = "SELECT * FROM persons WHERE person_id='$_POST[new_pid]'";
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);
					$pie = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);					
					oci_free_statement($stid);
					
					// make sure person exists and user name does not exist
					if ((!$une)&&($pie)) {
						
						// make sure role of user is legal
						if ($_POST[new_role]=='a' || $_POST[new_role]=='d' || $_POST[new_role]=='s' ) {
							
							// add new user
							$sql = "INSERT INTO users VALUES ('$_POST[new_un]', '$_POST[new_pw]', '$_POST[new_role]', '$_POST[new_pid]', TO_DATE('$date', 'YYYY/MM/DD'))";
							$stid = oci_parse($conn, $sql);
							
							// if insertion succeed, tell user
			      		if (oci_execute($stid)) {
			      			oci_commit($conn);
			      			echo 'New user is inserted!';
			      		} else {
			      			oci_rollback($conn);
			      			echo 'insertion failed';
			      		}
			      		oci_free_statement($stid);
			      		
			      	// if role is illegal, ask user to input again
		      		} else {
		      			echo 'Role must be in a, d or s!';
	      			} 
	      		
	      		}else{
	      			
	      			// if user name exists, ask user to input new user name
	      			if ($une) {
	      				echo 'User name exist!';
	      				
	      			// if person does not exist, tell user
	      			} else {
	      				echo 'Person id does not exist!';
	      			}
	      			
	      		}

	   		}
   			// Free the statement identifier when closing the connection
	   		oci_close($conn);	
      	}
      }
      ?>
	</body>
</html>				
				