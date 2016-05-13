<?php

// tell user that user log out sucessfully, and destroy all session
session_start();
session_destroy();
?>
<html>
<h2>You are log out now</h2>
      <form name="registration" method="post" action="loginPage.html">
    	<input type="submit" value="Click To Log In Again">
      </form>
</html>