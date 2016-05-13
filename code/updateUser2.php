
<?php
// start session
session_start();
// get session data
$PersonID = $_SESSION['PersonID'];
$UserUpdate = $_SESSION['UserUpdate'];
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
		// make sure user is login
		if ($PersonID== NULL){
			echo 'You are not login yet, you cannot change anything ';
			echo '<br/><br/>';
			
		// make sure user is an admin
		} else{
			if ($UserRole!='a'){
				echo 'You are not admin, you cannot update user';
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
			
			// go to this block if admin input new role
   		if ($_POST[role]){
   			
   			// check if new role is legal
   			if ($_POST[role]=='a' || $_POST[role]=='d' || $_POST[role]=='s' ){
   				
   				// if new role is legal, update information
   				$sql = "UPDATE users SET role = '$_POST[role]' WHERE user_name='$UserUpdate'";
   				$stid = oci_parse($conn, $sql );	   
   				if (oci_execute($stid)){
		      		oci_commit($conn);
	      		} else {
	      			oci_rollback($conn);
	      		}
   			
   			// if new role is illegal, tell admin and allow admin to input again
   			} else {
   				$firstnameErr="* role must be a or d or s";
   			}
   		}
   		
   		// show all information of user on screen
   		echo 'Information of user ';
			$sql = "SELECT user_name, role, person_id FROM users WHERE user_name='$UserUpdate'";
      	$stid = oci_parse($conn, $sql);
      	$res = oci_execute($stid);
      	if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
      		foreach ($row as $item) {
        			$info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
      			echo $info;
      			echo '<br/>';
      		}
      		echo '<br/>';
      	}
     		// Free the statement identifier when closing the connection
	   	oci_free_statement($stid);
	   	oci_close($conn);	 
		}
		?>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
      <h2>Please enter new role:</h2>
      <br/><br/>
		<input type="text" name="role" value= "<?php echo $_POST[role];?>"/>
		<span class="error"> <?php echo $firstnameErr;?></span> <br/>

		<br><br>
   	<input type="submit" name="validate" value="Submit">
		</form>
		
		<form action="updateUser.php">
    	<input type="submit" value="Back to last level">
      </form>
	</body>
</html>
