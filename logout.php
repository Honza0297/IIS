<?php
// Initialize the session
	session_start();

	unset($_SESSION["loggedin"]);
	unset($_SESSION["id"]);
	unset($_SESSION["role"]);
	header("Location: ".$_GET["page"]);
	exit;
?>