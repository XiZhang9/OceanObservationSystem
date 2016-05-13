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
		} else{
			
			// make sure user is an admin
			if ($UserRole!='a'){
				echo 'You are not admin, you cannot remove user';
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
		<h2>Remove user:</h2>
		Please enter the user name whose information you want to delete: <input type="text" name="delete_un" value= "<?php echo $_POST[delete_un];?>"/> <br/>

   	<input type="submit" name="validate" value="Submit">
		</form>
		
		<form action="welcomeAdmin.html">
    	<input type="submit" value="Back to last level">
      </form>
		
		<?php
		
		// make sure system conncts to database
		if($UserName&&$UserRole=='a'){
			if ($conn){
				
				// make sure user has done input 
				if ($_POST[delete_un]){
					
					// check if user name exists in database
					$sql = "SELECT role FROM users WHERE user_name='$_POST[delete_un]'";
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);	  
					
					// go to this block if user exists 				
      			if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
						foreach ($row as $item) {

							// if user is scientist or data curator, delete the user from database
							if ($item != 'a') {							
								$sql = "DELETE FROM users WHERE user_name = '$_POST[delete_un]'";
								$stid = oci_parse($conn, $sql);
								if (oci_execute($stid)){
					      		oci_commit($conn);
					      		echo "User is deleted";
				      		} else {
				      			oci_rollback($conn);
				      		}
								
														
							// if user is an admin, this user cannot be deleted
							} else {
								echo "The user is an administrator!";
							}
						}
					
					// if user does not exist, tell user
					} else {
						echo "User does not exist!";
					}
					
					// free statement
					oci_free_statement($stid);	
      		}
      		
      		// close connection
				oci_close($conn);	
      	}
      }
      ?>
	</body>
</html>				
				