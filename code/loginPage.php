<?php
// start session
session_start();
include("PHPconnectionDB.php");
?>
<html>
	<body>
		<?php
		
		// check if user has done all input field
		if (isset ($_POST['validate'])){
		 	$uname = $_POST['userName'];
			$pword = $_POST['password'];

			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			
			// unset session
		   unset($_SESSION['PersonID']);
			unset($_SESSION['UserName']);
			unset($_SESSION['UserRole']);
			
	      //establish connection
	      $conn=connect();		 		
	      if (!$conn) {
	      	echo 'Cannot connect to database!';
	      } else{
	      	
	      // select right password of this user
	      $sql = "SELECT password FROM users WHERE user_name='$uname'";
		   $stid = oci_parse($conn, $sql );	   
		   $res=oci_execute($stid);
			if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
	   		foreach ($row as $item) {
	        		$rightPword = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
	    		}
	    		
	    		// check if user inputs the right password, if not, tell user to input again
	    		if ($rightPword != $pword) {
					echo 'Invalid password'; 
				 
	      	} else{
	      		
	      		// find person id using user name
	      		$sql = "SELECT person_id FROM users WHERE user_name='$uname'";
					$stid = oci_parse($conn, $sql );	 
					oci_execute($stid);
					if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
	   				foreach ($row as $item) {
	        				$pid = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
	    				}
	      		}
	      		
	      		// set session
	      		$_SESSION['PersonID'] = $pid;
	      		$_SESSION['UserName'] = $uname;
	      		
	      		// get user's role
	      		$sql = "SELECT role FROM users WHERE user_name = '$uname'";
					$stid = oci_parse($conn, $sql );	 
					oci_execute($stid);
					if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
	   				foreach ($row as $item) {
	        				$role = ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;");
	    				}
	    				$_SESSION['UserRole'] = $role;
	    				
	    				// open the right welcome page
	    				if($role=="a"){
	    					header('Location: http://consort.cs.ualberta.ca/~han8/welcomeAdmin.html'); 
	      				exit;
	      			} elseif($role=='s') {
	      				header('Location: http://consort.cs.ualberta.ca/~han8/welcomeSci.html');
	      			} else{
	      				header('Location: http://consort.cs.ualberta.ca/~han8/welcome.html'); 
	      				exit;
	      			}
	      			
	      		}
	      	}
	      
	      // if user name is wrong, tell user
	      } else {
				echo 'Invalid user name';
			}
			
			// free statement
		   oci_free_statement($stid);
		   
		   // close connection
		   oci_close($conn);
			}
		}

   	?>
   </body>
</html>