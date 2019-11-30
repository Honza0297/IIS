<?php
// Initialize the session
include_once "CustomElements.php";
setSession();

	unset($_SESSION["loggedin"]);
	unset($_SESSION["id"]);
	unset($_SESSION["role"]);
	header("Location: ".$_GET["page"]);
	exit;
?>