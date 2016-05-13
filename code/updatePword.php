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
		if ($PersonID == NULL){
			echo 'You are not login yet, you cannot change anything ';
			echo '<br/><br/>';
		} else{
			
			//establish connection
      	$conn=connect();		 		
      	if (!$conn) {
      		echo 'Cannot connect to database!';
      		echo '<br/><br/>';
      	} 
      }
      ?>
		
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
		<h2>Change password:</h2>
		Please enter your old password: <input type="text" name="old_pw"/> <br/>
		Please enter your new password: <input type="text" name="new_pw"/> <br/>
   	<input type="submit" name="validate" value="Submit">
		</form>
		<form action="<?php if($UserRole=='a'){echo 'welcomeAdmin.html';}elseif($UserRole=='s'){echo 'welcomeSci.html';}else{echo 'welcome.html';}?>">
    	<input type="submit" value="Back to last level">
      </form>
		
		<?php
		// make sure system connects to database
		if($PersonID){
			if ($conn){
				
				// get right password of the user
				$sql = "SELECT password FROM users WHERE user_name = '$UserName'";	
				$stid = oci_parse($conn, $sql);
		     	$res = oci_execute($stid);
				if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)){
		      	foreach ($row as $item) {
		        		$info = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
		        		if ($info==$_POST['old_pw']) {
		        			$rightOldPw = TRUE;
		        		} else {
		        			$rightOldPw = FALSE;
		        		}
		      	}
		      }
				
				// check if old password is right
				if ($rightOldPw) {
					
					// check if the new password is entered
					if ($_POST['new_pw']) {
						
						// update password
						$sql = "UPDATE users SET password='$_POST[new_pw]' WHERE user_name='$UserName'";
						$stid = oci_parse($conn, $sql );	
						
						// if updating successes, tell user 
						if (oci_execute($stid)){
			      		oci_commit($conn);
			      		echo 'Password updated!';
		      		} else {
		      			oci_rollback($conn);
		      		}  
					} 
				
				// if old password is wrong and user has input new password, tell user to input again
				} else {
					if ($_POST['new_pw']){
						echo 'Sorry, your old password is wrong, we cannot update your password';
					}	
				}
			}
		}

		
		?>
	</body>
</html>
				
		
		