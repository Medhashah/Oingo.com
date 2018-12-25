<?php
	session_start();
	session_destroy();
	echo "string";
	header("location:index.php");
	echo "string";
?>