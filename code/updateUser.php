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
				echo 'You are not admin, you cannot update user information';
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
		<h2>Update user's information:</h2>
		Please enter the user name whose information you want to change: <input type="text" name="change_un" value= "<?php echo $_POST[change_un];?>"/> <br/>
		
   	<input type="submit" name="validate" value="Submit">
		</form>
		
		<form action="welcomeAdmin.html">
    	<input type="submit" value="Back to last level">
      </form>
		
		<?php
		
		// make sure user is login and system connects to database
		if($UserName){
			if ($conn){
				
				// go to this block if user has input user name
				if ($_POST[change_un]){
					
					// find user's role
					$sql = "SELECT role FROM users WHERE user_name='$_POST[change_un]'";
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);	      				
      			if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
						foreach ($row as $item) {
							
							// if user is admin, updating is not allowed
							if ($item != 'a') {
								$_SESSION['UserUpdate']=$_POST[change_un];
								header('Location: http://consort.cs.ualberta.ca/~han8/updateUser2.php'); 
								exit;
							} else {
								echo "The user is an administrator!";
							}
						}
					
					// if user name does not exist, tell admin	
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
				