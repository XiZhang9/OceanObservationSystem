<?php

// connet to database
function connect(){
	$conn = oci_connect('han8', 'han82SQL');
	if (!$conn) {
		$e = oci_error();
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}

	return $conn;
}
?>
